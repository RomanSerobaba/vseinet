const config = require('./config')
const logger = require('./lib/logger').create(config.logs_path, 'sphinx')

const mysql = require('./lib/db').create({
    host: config.sphinxql_host,
    port: config.sphinxql_port  
})

const spawn = require('child_process').spawn

const command = spawn('sudo', ['-u', 'sphinx', 'indexer', '--config', '/home/dev/www/vseinet.ru/bin/sphinx/pgsql/dev.conf', '--rotate', '--all'])

command.on('close', function(code) {
    mysql.query('TRUNCATE RTINDEX base_product_index_rt')
    mysql.query('TRUNCATE RTINDEX supplier_product_index_rt')
})

command.stdout.on('data', function(message) {
    logger.info(message.toString())
})

command.stderr.on('data', function(message) {
    logger.error(message.toString())

    command.stdin.pause()
    command.kill('SIGKILL')
})