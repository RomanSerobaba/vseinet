'use strict';

const io = require('socket.io')();
const fsp = require('../lib/fsp');
const path = require('path');
const spawn = require('child_process').spawn;
const amqp = require('amqplib/callback_api');
const config = require('./config');
const logger = require('./../lib/logger').create(config.server.logpath, 'parsers');
const mysql = require('../lib/mysql').create(config.server.db);
const rabbit = require('../lib/rabbit').create('localhost')

const EventEmitter = require('events');

const server = new EventEmitter()
const clients = {};

const parsers = new Map(config.parsers.map(name => [ 
    name, 
    {
        name,
        store: require('./' + name + '/server-store')(mysql),
        sources: new Map(),
    }
]));

io.sockets.on('connection', client => {
    client.on('handshake', mac => {
        if (!mac) {
            client.disconnect();
            logger.error('Connection error: invalid MAC address');
        }
        else if (typeof clients[mac] === 'undefined') {
            logger.info('Connection client %s', mac);
            client.on('disconnect', function() {
                client.removeAllListeners();
                delete clients[mac];
                logger.info('Disconnect client %s', mac);
            });
            for (let [ parser, { store } ] of parsers) {
                store.getActiveSources().then(sources => {
                    const promises = sources.map(source => { 
                        parsers.get(parser).sources.set(source.id, source);
                        return fsp.read(path.normalize(__dirname + '/' + parser + '/sources/' + source.name + '.js'))
                            .then(js => ({ ...source, js })); 
                    });
                    Promise.all(promises).then(sources => client.emit('get-sources', parser, sources));
                });
            }
            client.on('request-data', (parser, sourceId) => {
                parsers.get(parser).store.getRequestData(parsers.get(parser).sources.get(sourceId)).then(data => {
                    if (data) {
                        client.emit('get-request-data', parser, sourceId, data);
                    }
                });
            });
            client.on('response-data', (parser, data) => {
                parsers.get(parser).store.setResultData(data).then(ids => {
                    if (ids.length) {
                        client.emit('cleanup', parser, ids);
                    }
                });
            });
        }
        else {
            client.disconnect();
            logger.error('Connection error: client with MAC address %s already connected', mac);    
        }
        client.on('error', error => {
            logger.error('Error socket.io client %s', error.stack);
        });
    });
});
io.listen(config.server.port);

rabbit.consume('toggle.parser.source', (data) => {
    parsers.get(data.parser).store.getSource(data.id).then(source => {
        if (source.is_active) {
            parsers.get(data.parser).sources.set(source.id, source);
            return fsp.read(path.normalize(__dirname + '/' + data.parser + '/sources/' + source.name + '.js')).then(js => {
                clients.map(client => client.emit('get-source', { ...source, js })); 
            });
        }
        clients.map(client => client.emit('off-source', data.parser, source));
    });
});
