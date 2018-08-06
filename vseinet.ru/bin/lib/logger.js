const fsp = require('./fsp');
const path = require('path');
const util = require('util');

class Logger {
    constructor(logpath, logname) {
        fsp.mkdir(this.logpath = path.normalize(logpath + '/' + logname));
        this.logname = logname;
        this.tzoffset = (new Date()).getTimezoneOffset() * 60000;
    }

    log(level, message) {
        let time = (new Date(Date.now() - this.tzoffset)).toISOString().replace(/(\.\d{3}Z)/, '').split('T');
        let logname = this.logname + '-' + time[0] + '.log';
        let data = time[0] + ' ' + time[1] + ' [' + level + '] ' + message + "\r\n";
        return fsp.append(this.logpath + '/' + logname, data).then(() => {
            if ('undefined' == typeof console[level]) {
                level = 'log';
            }
            console[level](time[1] + ' ' + message);   
        });    
    }

    debug(message) {
        return this.log('debug', util.format.apply(null, arguments));   
    }

    info(message) {
        return this.log('info', util.format.apply(null, arguments));
    }

    notice(message) {
        return this.log('notice', util.format.apply(null, arguments));    
    }

    warning(message) {
        return this.log('warning', util.format.apply(null, arguments));
    }

    error(message) {
        return this.log('error', util.format.apply(null, arguments));
    }

    critical(message) {
        return this.log('critical', util.format.apply(null, arguments));
    }

    alert(message) {
        return this.log('alert', util.format.apply(null, arguments));
    }

    emergency(message) {
        return this.log('emergency', util.format.apply(null, arguments));
    }
}

module.exports = Logger;
module.exports.create = (logpath, logname) => new Logger(logpath, logname);