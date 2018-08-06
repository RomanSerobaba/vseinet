const config = require('./config');

const rabbit = require('./lib/rabbit').create(config.rabbit_host);
const logger = require('./lib/logger').create(config.logs_path, 'notifier');

const WebSocket = require('ws');

const wss = new WebSocket.Server({ port: config.ws_port });

let listeners = [];

wss.on('connection', ws => {
    listeners.push(ws);
    ws.on('close', () => {
        listeners = listeners.filter(client => client != ws);
        delete ws;
    });
});

rabbit.consume('notify.front', request => {
    return new Promise((fulfill, reject) => {
        const message = JSON.stringify(request);
        listeners.map(client => client.send(message));
        logger.info('Broadcast `%s`', message);
        fulfill();
    });
});