'use strict';

var http = require('http');
var net = require('net');
var url = require('url');

class Proxy {
    constructor(port, host, firewall) {
        this.port = port;
        this.host = host;
        this.firewall = firewall;
    }

    start() {            
        this.server = http.createServer(this.http.bind(this));
        this.server.on('connect', this.https.bind(this));
        this.server.listen(this.port, this.host);

        return this;
    }

    http(request, response) {
        let ph = url.parse(request.url);
        if (this.firewall && this.firewall.isblock(ph.hostname)) {
            response.writeHead(500, {
                'Content-Length': 0,
                'Content-Type': 'text/plain',
            });
            return response.end();
        }

        let proxyRequest = http.request({
            port: ph.port,
            hostname: ph.hostname,
            method: request.method,
            path: ph.path,
            headers: request.headers,
        });
        proxyRequest.on('response', (proxyResponse) => {
            proxyResponse.on('data', (chunk) => response.write(chunk, 'binary'));
            proxyResponse.on('end', () => response.end());
            response.writeHead(proxyResponse.statusCode, proxyResponse.headers);    
        });
        proxyRequest.on('error', () => {});

        request.on('data', chunk => proxyRequest.write(chunk, 'binary'));
        request.on('end', () => proxyRequest.end());
        request.on('error', () => {});
    }

    https(request, socketRequest, head) {
        let ph = url.parse('https://' + request.url);
        if (this.firewall && this.firewall.isblock(ph.hostname)) {
            socketRequest.write("HTTP/" + request.httpVersion + " 500 Connection error\r\n\r\n");
            return socketRequest.end();
        }

        let socket = net.connect(ph.port, ph.hostname, () => {
            socket.write(head);
            socketRequest.write("HTTP/" + request.httpVersion + " 200 Connection established\r\n\r\n");
        });
        socket.on('data', chunk => socketRequest.write(chunk));
        socket.on('end', () => socketRequest.end());
        socket.on('error', () => {
            socketRequest.write("HTTP/" + request.httpVersion + " 500 Connection error\r\n\r\n");
            socketRequest.end();
        });
        socketRequest.on('data', chunk => socket.write(chunk)); 
        socketRequest.on('end', () => socket.end()); 
        socketRequest.on('error', () => socket.end()); 
    }

    close() {
        if (this.server) {
            this.server.close();
            delete this.server;
        }
    }
}

module.exports = Proxy;
module.exports.create = (host, port, firewall) => new Proxy(port,host, firewall).start(); 
