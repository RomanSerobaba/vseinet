var cp = require('child_process')

module.exports.execute = function(command, options) {
    return new Promise(function(fulfill, reject) {
        cp.exec(command, options, function(error, stdout, stderr) {
            if (error) {
                return reject(error)
            }
            fulfill(stdout)
        })
    })
}