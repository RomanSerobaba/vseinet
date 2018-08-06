const config = require('./config')
const rabbit = require('./lib/rabbit').create(config.rabbit_host)

const logger = require('./lib/logger').create(config.logs_path, 'listendb')

const LISTEN = 'table_update'

const mysql = require('./lib/db').create({
    host: config.sphinxql_host,
    port: config.sphinxql_port  
})

const { Client } = require ('pg')
const pgsql = new Client({
    host: config.pgsql_host,
    port: config.pgsql_port,
    user: config.pgsql_user,
    password: config.pgsql_password,
    database: config.pgsql_dbname
})

const cp = require('child_process')

pgsql.connect(err => {
    if (err) {
        logger.error('Connection error: %s', err)
    }

    pgsql.on('notification', message => {
        processNotify(JSON.parse(message.payload))
    })

    pgsql.query('LISTEN ' + LISTEN)

    processMissed(new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().replace(/T/, ' ').replace(/\..+/, ''))
})

function processNotify(payload) {
    let data = JSON.parse(payload.data)

    return Promise.resolve().then(() => {
        let promise = tables[payload.table](payload.operation, data)
        if (promise instanceof Promise) {
            return promise
        }
        
        return Promise.resolve()
    })
    .then(() => {
        return pgsql.query("DELETE FROM table_update_data WHERE id = $1", [ payload.id ])
    })
    .catch(err => {
        logger.error('%s %s, id: %d, error: %s', payload.table, payload.operation, data.id, err)
    })    
}

function processMissed(now) {
    return new Promise((fulfill, reject) => {
        pgsql.query("SELECT * FROM table_update_data WHERE notify_at < $1 ORDER BY notify_at, id LIMIT 1000", [ now ]).then(result => {
            if (0 == result.rows.length) {
                return fulfill()
            }

            let promises = result.rows.map(payload => {
                return processNotify(payload)
            })

            return Promise.all(promises).then(() => {
                return processMissed(now)  
            })
        })
        .catch(err => {
            logger.error(err)
            reject()
        })
    })
}

function spawn(command, args = {}) {
    let id = Math.random().toString(16).substr(2)

    logger.info('[%s] start `%s`', id, command)
    if (args = args ? JSON.stringify(args) : '') {
        logger.info('[%s] arguments:\n%s', id, args)
    }

    return new Promise((fulfill, reject) => {
        let php = cp.spawn(config.php, [ config.console, command, args ])

        php.stdout.on('data', message => {
            if ('OK!' == message.toString().substr(0, 3)) {
                logger.info('[%s] done:\n%s', id, message.toString())

                return fulfill()
            }

            php.stderr.emit('data', message)
        })

        php.stderr.on('data', message => {
            logger.error('[%s] error:\n%s', id, message.toString())

            php.stdin.pause()
            php.kill('SIGKILL')
            
            reject(message.toString())
        })
    })
}

const tables = {

    base_product: (operation, data) => {
        return mysql.query("UPDATE base_product_index SET killbill = 1 WHERE id = :id", {
            id: data.id
        })
        .then(() => {
            if ('DELETE' == operation) {
                return mysql.query("DELETE FROM base_product_index_rt WHERE id = :id", {
                    id: data.id
                })
            }

            return mysql.query("REPLACE INTO base_product_index_rt (id, name, killbill) VALUES (:id, :name, 0)", {
                id: data.id,
                name: data.name 
            })
        })
    },

    brand: (operation, data) => {
        if ('DELETE' != operation) {
            return spawn('executor:rename:base:products', { brandId: data.id })
        }
    },

    category: (operation, data) => {
        if ('DELETE' != operation) {
            return spawn('executor:rename:base:products', { categoryId: data.id })
        }
    },

    supplier_category: (operation, data) => {

    },

    supplier_product: (operation, data) => {
        return mysql.query("UPDATE supplier_product_index SET killbill = 1 WHERE id = :id", { 
            id: data.id 
        })
        .then(() => {
            if ('DELETE' == operation || !data.base_product_id) {
                return mysql.query("DELETE FROM supplier_product_index_rt WHERE id = :id", { 
                    id: data.id 
                })
            }

            return mysql.query("REPLACE INTO supplier_product_index_rt (id, `code`, killbill) VALUES (:id, :code, 0)", { 
                id: data.id, 
                code: data.code || data.article
            })
        })
    },
    supplier_product_sml: (operation, data) => {
        return tables.supplier_product(operation, data)
    },
    supplier_product_other: (operation, data) => {
        return tables.supplier_product(operation, data)
    }
}
