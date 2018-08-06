'use strict';

const path = require('path');

const client = {
    db: {
        host: '127.0.0.1', 
        user: 'root',           
        password: 'dst138', 
        database: 'data_parsers',     
    },
    logpath: path.normalize(__dirname + '/logs'),
};

const server = {
    ip: '192.168.122.195', 
    port: 8080,
    host: 'k2fl.vseinet.ru',
    db: {
        host: '192.168.122.195', 
        user: 'k2fl', 
        password: 'uKFY8~qo~$', 
        database: 'k2fl', 
    },
    php: '/usr/bin/php',
    cron: path.normalize(__dirname + '/../../../../www/vseinet.ru/cron.php'),
    logpath: path.normalize(__dirname + '/../../../../../logs'),    
};

// const parsers = ['price', 'product', 'image', 'supplier-product', 'trading'];
const parsers = [ 'product', 'image', 'supplier-product' ];

module.exports = { client, server, parsers };