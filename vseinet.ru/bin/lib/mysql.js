var mysql = require('mysql')

var db = function(config) {
    this.pool = mysql.createPool({
        host: config.host,
        port: config.port || 3306,
        user: config.user,
        password: config.password,
        database: config.database,
        queryFormat: function(query, values) {
            if (!Object.keys(values).length) {
                return query
            }
            var self = this
            return query.replace(/\:(\w+)/g, function(param, key) {
                if (values.hasOwnProperty(key)) {
                    var value = values[key]
                    if ('array' == typeof value)
                        return value.map(function(value) {
                            return self.escape(value)
                        }).join(',')
                    return self.escape(value)
                }
                return param
            })
        }
    })
}

db.prototype.query = function(sql, params) {
    var self = this
    return new Promise(function(fulfill, reject) {
        self.pool.query(sql, params || {}, function(err, result) {
            if (err) {
                return reject(err)
            }
            fulfill(result)
        })
    })
}

db.prototype.queryRow = function(sql, params) {
    return this.query(sql, params).then(function(result) {
        return result.length ? result[0] : null
    })
}

module.exports = db
module.exports.create = function(config) {
    return new db(config)
}