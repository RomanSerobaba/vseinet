'use strict';

const config = require('./config');
const logger = require('./../lib/logger').create(config.client.logpath, 'parsers');
const fsp = require('./../lib/fsp');
const path = require('path');
const cp = require('./../lib/cp');
const mysql = require('./../lib/mysql').create(config.client.db);

const EventEmitter = require('events');

const client = new EventEmitter();
const server = require('socket.io-client')('http://' + config.server.host + ':' + config.server.port);

const parsers = new Map(config.parsers.map(name => [ 
    name, 
    {
        name,
        store: require('./' + name + '/client-store')(mysql),
        sources: new Map(),
    }
]));

let timer = null;

server.on('connect', () => {
    cp.execute('ifconfig -a')
        .then((stdout) => {
            const m = /HWaddr\s+(([a-z0-9]{2}[:\-]){5}[a-z0-9]{2})/ig.exec(stdout.toString()) 
            if (!(m instanceof Array) || m.length < 2) {
                throw new Error('Can not detect mac address');
            } 
            server.emit('handshake', m[1]);
            logger.info('Handshake. MAC address %s', m[1]);
            
            timer = setInterval(() => {
                for (let [ parser, { store } ] of parsers) {
                    store.getResultData().then(data => {
                        if (data) {
                            server.emit('response-data', parser, data);
                            logger.info('Response %s data to server', parser);
                        }
                    });
                }
            }, 30000);
        })
        .catch(error => {
            logger.error('Error connect: %s', error.stack);
        });
});

server.on('disconnect', () => {
    clearInterval(timer);
    logger.info('Server disconnect');
});

function getSource(parser, source) {
    offSource(parser, source);

    fsp.write(path.normalize(__dirname + '/' + parser + '/sources/' + source.name + '.js'), source.js.toString())
        .then(() => {
            parsers.get(parser).sources.set(source.id, {
                ...source,
                lastRequestTime: 0,
                js: require('./' + parser + '/sources/' + source.name),
            });
            logger.info('Load %s - %s', parser, source.name);
        })
        .catch(error => {
            logger.error('Error %s - %s, %s', parser, source.name, error.stack);  
        });
}

function offSource(parser, source) {
    if (parsers.get(parser).sources.delete(source.id)) {
        delete require.cache[require.resolve('./' + parser + '/sources/' + source.name)];    
        logger.info('Off %s - %s', parser, source.name);  
    }
}

server.on('get-sources', (parser, sources) => sources.map(source => getSource(parser, source)));
server.on('get-source', (parser, source) => getSource(parser, source));
server.on('off-source', (parser, source) => offSource(parser, source));

server.on('get-request-data', (parser, sourceId, data) => {
    parsers.get(parser).store.setRequestData(parsers.get(parser).sources.get(sourceId), data);
});

server.on('cleanup', (parser, ids) => parsers.get(parser).store.cleanup(ids));

class Pool {
    constructor() {
        this.pi = 0;
        this.si = 0;
    }
    get() {
        return new Promise(fulfill => {
            this.check(fulfill);
        });
    }

    check(fulfill) {
        if (parsers.size) {
            const pk = [];
            for (let key of parsers.keys()) {
                pk.push(key);
            }
            if (typeof pk[this.pi] === 'undefined') {
                this.pi = 0;
                this.si = 0;
            } 
            const parser = parsers.get(pk[this.pi]);   
            if (parser.sources.size) {
                const sk = [];
                for (let key of parser.sources.keys()) {
                    sk.push(key);
                }
                if (typeof sk[this.si] === 'undefined') {
                    this.pi++;
                    this.si = 0;
                }
                const source = parser.sources.get(sk[this.si]);
                this.si++;            
                fulfill({ parser, source });
            }
            else {
                this.pi++;
            }
        }
        setImmediate(() => this.check(fulfill));
    }
}

module.exports = { 
    client, 
    server, 
    parsers, 
    pool: new Pool(),
};