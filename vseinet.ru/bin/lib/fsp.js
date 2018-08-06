var fs = require('fs');
var path = require('path');

module.exports.mkdir = (dir, mode) => new Promise((fulfill, reject) => {
    dir = path.normalize(dir);
    fs.stat(dir, error => {
        if (error) {
            return fs.mkdir(dir, mode || 0775, error => {
                if (error) {
                    return reject(error);
                }
                fulfill(dir);
            });
        }
        fulfill(dir);
    });
});


module.exports.read = (file, options) => new Promise((fulfill, reject) => {
    fs.readFile(path.normalize(file), options, (error, data) => {
        if (error) {
            return reject(error);
        }
        fulfill(data);
    });
});

module.exports.write = (file, data, options) => new Promise((fulfill, reject) => {
    fs.writeFile(path.normalize(file), data, options, error => {
        if (error) {
            return reject(error);
        }
        fulfill();
    });
});

module.exports.append = (file, data, options) => new Promise((fulfill, reject) => {
    fs.appendFile(path.normalize(file), data, options, error => {
        if (error) {
            return reject(error);
        }
        fulfill();
    });
});

module.exports.stat = dir => new Promise((fulfill, reject) => {
    fs.stat(path.normalize(dir), (error, stats) => {
        if (error) {
            return reject(error);
        }
        fulfill(stats);
    });
});

module.exports.unlink = file => new Promise((fulfill, reject) => {
    fs.unlink(path.normalize(file), error => {
        if (error) {
            return reject(error);
        }
        fulfill();
    });
});
