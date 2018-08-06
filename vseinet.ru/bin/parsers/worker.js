'use strict';

const config = require('./config');
const Firewall = require('./firewall');
const Proxy = require('./proxy');
const os = require('os');
const { Builder, By, Key, until } = require('selenium-webdriver');
const Options = require('selenium-webdriver/chrome').Options;
const logger = require('./../lib/logger').create(config.client.logpath, 'parsers');
const { findFreePort } = require('selenium-webdriver/net/portprober');
const { server, pool } = require('./client');

async function parse(driver) {
    const { parser, source } = await pool.get();
    const data = await parser.store.getRequestData(source);
    if (data === null) {
        const requestTime = +new Date();
        if (source.lastRequestTime < requestTime - 60000) {
            source.lastRequestTime = requestTime;
            server.emit('request-data', parser.name, source.id);
            logger.info('request-data', parser.name, source.name);
        }
    }
    else {
        try {
            const url = await source.js.url(source, data);
            if (url) {
                await driver.get(url);
                try {
                    const alert = await driver.switchTo().alert();
                    alert.dismiss();
                }
                catch (e) {

                }
                if (source.js.cookies.length) {
                    source.js.cookies.map(async cookie => {
                        await driver.addCookie({
                            ...cookie,
                            expiry: Math.ceil(Date.now() / 1000),
                        });
                    });
                    await driver.navigate().refresh();
                }
                if (source.js.wait) {
                    const wait = source.js.wait(url);
                    if (wait) {
                        try {
                            await driver.wait(until.elementLocated(By.css(wait)), 5000);   
                        }
                        catch (e) {

                        }
                    }
                }
                const result = await driver.executeAsyncScript(source.js.parse, source, data);
                await parser.store.setResultData(source, data, result);    
            }
            else {
                await parser.store.setErrorData(source, data, { status: 400 });     
            }
        }
        catch (e) {
            parser.store.setErrorData(source, data, { status: 500 });
            throw e;
        }
    }
}

async function start(port, index) {
    let x = 50;
    let y = 20;
    if ('2' == index || '4' == index) {
        x = 680;
    }
    if ('3' == index || '4' == index) {
        y = 400;
    }
    const options = new Options().addArguments([
        'user-data-dir=/var/ramdisk/' + index,
        'ignore-certificate-errors',
        'verbose',
        'chrome-frame',
        'window-size=600,350',
        'window-position=' + x + ',' + y,
        'disable-infobars',
        'disable-notifications',
        'fast-start',
        'disable-session-crashed-bubble',
        'disable-background-networking',
        'disable-client-side-phishing-detection',
        'disable-component-update',
        'disable-default-apps',
        'disable-hang-monitor',
        'disable-prompt-on-repost',
        'disable-sync',
        'disable-web-resources',
        'disable-web-security',
        'enable-logging',
        'log-level=0',
        'metrics-recording-only',
        'no-first-run',
        'password-store=basic',
        'safebrowsing-disable-auto-update',
        'safebrowsing-disable-download-protection',
        'test-type=webdriver',
        'use-mock-keychain', 
        // 'load-extension=/home/eugene/.config/google-chrome/Default/Extensions/pfmgfdlgomnbgkofeojodiodmgpgmkac/2.0.2_0', 
        'load-extension=' + __dirname + '/anti-guard',
        'proxy-server=localhost:' + port,
    ]);

    const driver = await new Builder().forBrowser('chrome').setChromeOptions(options).build();

    try {
        await new Promise(async (fulfill, reject) => {
            try {
                while (true) {
                    await parse(driver);
                }
            }
            catch (e) {
                reject(e); 
            }
        });
    }
    catch (e) {
        driver.quit();
        logger.error('An error occurred:', e.stack);
    }
    setImmediate(() => start(port, index));    
}

(async () => {
    const port = await findFreePort('localhost');
    const firewall = await Firewall.create();
    const proxy = await new Proxy(port, 'localhost', firewall).start(); 

    logger.info('Proxy started at port %d', port);

    for (let index = 1; index <= os.cpus().length; index++) {
        start(port, index);
    }
})();