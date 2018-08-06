const config = require('./config')

const rabbit = require('./lib/rabbit').create(config.rabbit_host)
const logger = require('./lib/logger').create(config.logs_path, 'notifier')

const WebSocket = require('ws')

const wss = new WebSocket.Server({ port: config.ws_port })

const subscribers = {}

wss.on('connection', ws => {
    ws.on('message', message => {        
        let event = JSON.parse(message)
        
        if ('undefined' == typeof event.types || !(event.types instanceof Array)) {
            ws.send(JSON.stringify({
                type: 'error',
                message: 'Specify the types of events you are subscribing to'
            }))

            return logger.error('No event types specified')
        }

        event.types.map(type => {
            if ('undefined' == typeof subscribers[type]) {
                subscribers[type] = []
            }
            subscribers[type].push(ws)
            logger.info('Connection new client, subscribed to `%s`', type)
        })
    })
    ws.on('close', () => {
        logger.info('Disconnect client')
        for (let type in subscribers) {
            subscribers[type] = subscribers[type].filter(client => client != ws)
        }
        delete ws
    })
})

rabbit.consume('notify.front', request => {
    return new Promise((fulfill, reject) => {
        if ('undefined' != typeof request.type && 'undefined' != typeof subscribers[request.type]) {
            subscribers[request.type].map(function(client) {
                client.send(JSON.stringify(request))
            })
            logger.info('Broadcast type `%s` with data `%s`', request.type, JSON.stringify(request.data))
        }
        fulfill()
    })
})