'use strict';

const path = require('path');
const fsp = require('./../lib/fsp');

const FILE = path.normalize(__dirname + '/whitelist');

class Firewall {
    constructor(filename = FILE) {
        this.setFilename(filename);
    }

    isblock(hostname) {
        return !~this.whitelist.indexOf(this.canonize(hostname));
    }

    append(hostname) {
        let hostnames = hostname instanceof Array ? hostname : [hostname];
        hostnames.map(hostname => {
            hostname = this.canonize(hostname);
            if (!~this.whitelist.indexOf(hostname)) {
                this.whitelist.push(hostname);
            }
        });
        return fsp.write(this.filename, this.whitelist.join('\r\n'));
    }

    remove(hostname) {
        if (!(hostname instanceof Array)) {
            hostname = [hostname];
        }
        hostname.map(host => {
            host = this.canonize(host);
            let index = this.whitelist.indexOf(host);
            if (~index) {
                this.whitelist = this.whitelist.splice(index, 1);
            }
        });
        fsp.write(this.filename, this.whitelist.join('\r\n'));
    }

    canonize(hostname) {
        let fragments = hostname.split('.').reverse();
        return fragments[1] + '.' + fragments[0];
    }

    setFilename(filename) {
        return fsp.read(this.filename = filename).then(data => this.whitelist = data.toString().split('\r\n'));
    }

    getFilename() {
        return this.filename;
    }
}

module.exports = Firewall;
module.exports.create = (filename) => new Firewall(filename);
