const config = require('./config')

const rabbit = require('./lib/rabbit').create(config.rabbit_host)
const logger = require('./lib/logger').create(config.logs_path, 'executor')

const spawn = require('child_process').spawn

rabbit.consume('execute.command', request => {

    return new Promise((fulfill, reject) => {
        let id = Math.random().toString(16).substr(2)

        logger.info('[%s] start `%s`', id, request.command)
        let args = []
        if ('object' == typeof request.args) {
            logger.info('[%s] args\n%s', id, JSON.stringify(request.args))
            for (let key in request.args) {
                if (request.args.hasOwnProperty(key)) {
                    args.push('object' == typeof request.args[key] ? JSON.stringify(request.args[key]) : request.args[key])
                }
            }
        }

        var php = spawn(config.php, [config.console, 'executor:' + request.command].concat(args))

        php.stdout.on('data', function(message) {
            if ('OK!' == message.toString().substr(0, 3)) {
                logger.info('[%s] done:\n%s', id, message.toString())

                return fulfill()
            }

            php.stderr.emit('data', message)
        })

        php.stderr.on('data', function(message) {
            logger.error('[%s] error:\n%s', id, message.toString())

            php.stdin.pause()
            php.kill('SIGKILL')
            
            fulfill()
        })   
    })
})