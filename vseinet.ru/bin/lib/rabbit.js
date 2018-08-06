var amqplib = require('amqplib')
var connections = {}

var Rabbit = function(host) {
    if (!connections[host]) {
        connections[host] = amqplib.connect('amqp://' + host)
    }
    this.connection = connections[host]
}

/**
 * @param {string} queue - name of queue
 * @param {mixin} data - if array, messages will be sent for each item 
 * @return {promise}
 */
Rabbit.prototype.publish = function(queue, data) {
    return this.connection.then(function(connection) {
        return connection.createChannel().then(function(channel) {
            return channel.assertQueue(queue, {durable: true}).then(function(ok) {
                function send(data) {
                    if ('object' == typeof data) {
                        data = JSON.stringify(data)
                    }
                    channel.sendToQueue(queue, new Buffer(data))
                }
                if (data instanceof Array) {
                    data.map(send)
                }
                else {
                    send(data)
                }
                return channel.close()    
            })
        })  
    })
}

/**
 * @param {string} queue - name of queue
 * @param {function} callback - callback get one argument ({mixin} data)
 * @return {promise}
 */
Rabbit.prototype.consume = function(queue, callback) {
    return this.connection.then(function(connection) {
        return connection.createChannel().then(function(channel) {
            return channel.assertQueue(queue, {durable: true}).then(function(ok) {
                return channel.prefetch(1).then(function() {
                    return channel.consume(queue, function(message) {
                        var data = message.content.toString()
                        return callback(JSON.parse(data) || data).then(function() {
                            return channel.ack(message)
                        })    
                    })
                })
            })
        })
    })
}

module.exports = Rabbit
module.exports.create = function(host) {
    return new Rabbit(host)
}