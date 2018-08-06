'use strict';

var config = require('./config');
var socket = require('net').createConnection(80, 'vseinet.ru');

socket.on('connect', () => {
    if (socket.address().address === config.server.ip) {
        require('./server');
    }
    else {
        require('./worker');
    }
});

socket.on('error', error => {
    throw error;
});