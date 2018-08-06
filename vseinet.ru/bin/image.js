const config = require('./config')
const logger = require('./lib/logger').create(config.logs_path, 'image')

const { Client } = require ('pg')
const pgsql = new Client({
    host: config.pgsql_host,
    port: config.pgsql_port,
    user: config.pgsql_user,
    password: config.pgsql_password,
    database: config.pgsql_dbname
})

const io = require('socket.io')()
const fsp = require('./lib/fsp')
const path = require('path')
const gm = require('gm').subClass({imageMagick: true})
const randomstr = require('./lib/utils').randomstr
const rabbit = require('./lib/rabbit').create(config.rabbit_host)

/**
 * @param {string} basepath - path to the folder with images 
 */
const basepath = path.normalize(__dirname + '/../web/u/products') + '/'
/**
 * @param {array} sizes - array of image sizes
 */
const sizes = [800, 280, 200, 100, 60]

/** 
 * @param {number} id - base_product id
 * @param {string} imageBase64 - base64 image encode 
 * @return {promise|string} basename
 */
function upload(id, imageBase64) {
    var basename = '' + Math.floor(id / 1000)
    return fsp.mkdir(basepath + basename).then(function() {
        basename += '/' + id
        return fsp.mkdir(basepath + basename).then(function() {
            basename += '/' + randomstr(8)
            var buffer = new Buffer(imageBase64, 'base64')
            var data = buffer.toString('binary').replace(/^data:image\/\w+;base64,/, '')
            var buffer = new Buffer(data, 'base64')
            var filename = basepath + basename + '_o.jpg'
            return fsp.writeFile(filename, buffer.toString('binary'), 'binary').then(function() {
                return basename
            })
        })
    })
}

/**
 * @param {string} basename
 * @return {promise|object} size - width & height
 */
function identify(basename) {
    return new Promise(function(fulfill, reject) {
        var filename = basepath + basename + '_o.jpg'
        gm(filename).noProfile().fuzz(1, '%').trim().write(filename, function(error) {
            if (error) {
                return reject(error)
            }
            gm(filename).identify(function(error, data) {
                if (error) {
                    return reject(error)
                }
                fulfill(data.size)
            })
        })   
    })
}

/**
 * @param {string} basename
 * @return {promise} 
 */
function resize(basename) {
    var promises = sizes.map(function(size) {
        return new Promise(function(fulfill, reject) {
            var image = gm(basepath + basename + '_o.jpg').resize(size, size, '>')
            image.background('#FFFFFF').gravity('Center').extent(size, size)
            image.write(basepath + basename + '_' + size + '.jpg', function(error) { 
                if (error) {
                    return reject(error)
                }
                fulfill()
            })   
        })
    })
    return Promise.all(promises)
}

/**
 * @param {string} basename
 * @param {string} cover - absolute path to file with cover
 */
function coverup(basename, cover) {
    return new Promise(function(fulfill, reject) {
        var filename = basepath + basename + '_o.jpg'
        gm(filename).composite(cover).write(filename, function(error) {
            if (error) {
                return reject(error)
            }
            fulfill(basename)
        })
    })
}

/**
 * UPLOAD
 */
var sql = {
    getLastImage: 
        "SELECT * " +
        "FROM base_product_image " +
        "WHERE base_product_id = :base_product_id " +
        "ORDER BY priority DESC " +
        "LIMIT 1",
    insertImage:
        "INSERT INTO base_product_image (base_product_id, priority, basename, auto, approved_date, approved_manager_id, width, height, source_image_id) " +
        "VALUES (:base_product_id, :priority, :basename, :auto, IF(:approved, NOW(), NULL), :approved_manager_id, :width, :height, :source_image_id)",
    updateImage:
        "UPDATE base_product_image " +
        "SET width = :width, height = :height " +
        "WHERE id = :id",
}

var covers = {
    30: __dirname + '/covers/PL.png'
}
var execSync = require('child_process').execSync

rabbit.consume('upload.image', function(data) {
    return upload(data.base_product_id, data.image_base_64).then(function(basename) {
        return new Promise(function(fulfill, reject) {
            var cover = covers[data.source_id]
            if (cover) {
                var background = execSync('convert ' + basepath + basename + '_o.jpg -crop 3x3+22+17 -scale 1x1\\! -format "%[pixel:s]" info:-').toString()
                if ('white' != background) {
                    var m = background.match(/(\d+),(\d+),(\d+)/)
                    if (m && m.length && m[1] > 10 * m[2] && m[1] > 10 * m[2]) {
                        return coverup(basename, cover).then(function() {
                            fulfill(basename)
                        })
                    }
                }
            }
            return fulfill(basename)
        }).then(function(basename) {
            return identify(basename).then(function(size) {
                return resize(basename).then(function() {
                    return db.queryRow(sql.getLastImage, data).then(function(image) {
                        if (null === image) {
                            image = {
                                base_product_id: data.base_product_id,
                                priority: 0,
                            }
                        }
                        image.source_image_id = data.source_image_id || 0
                        image.basename = basename 
                        image.priority++
                        image.auto = data.auto || 0
                        image.approved = 0 == image.auto || 1 == image.priority
                        image.approved_manager_id = data.manager_id || -1
                        image.width = size.width
                        image.height = size.height 
                        return db.query(sql.insertImage, image).then(function() {
                            logger.info('Upload image id: %d/%d.', image.base_product_id, image.priority)
                        })
                    })
                })
            })   
        })
    })
    .then(function() {
        return new Promise(function(fulfill, reject) {
            setTimeout(function() {
                fulfill()
            }, 100)
        })
    })
    .catch(function(error) {
        logger.error('Error upload image id: %d. %s.', data.base_product_id, error.stack)
    })   
})

/**
 * RESIZE
 */
rabbit.consume('resize.image', function(image) {
    return identify(image.basename).then(function(size) {
        return resize(image.basename).then(function() {
            image.width = size.width
            image.height = size.height 
            return db.query(sql.updateImage, image).then(function() {
                logger.info('Resize image id: %d/%d.', image.base_product_id, image.priority)
            })
        })
    })
    .catch(function(error) {
        logger.error('Error resize image id: %d. %s.', image.base_product_id, error.stack)
    })   
})
rabbit.consume('resize.image.auto', function(image) {
    return identify(image.basename).then(function(size) {
        return resize(image.basename).then(function() {
            image.width = size.width
            image.height = size.height 
             return db.query(sql.updateImage, image).then(function() {
                 logger.info('Resize image id: %d/%d.', image.base_product_id, image.priority)
             })
//            return new Promise(function(fulfill, reject) {
//                db.query(sql.updateImage, image).then(function() {
//                logger.info('Resize image id: %d/%d.', image.base_product_id, image.priority)
//                })
//                .then(function() {
//                    setTimeout(function() {
//                        fulfill();
//                    }, 1000);
//                })
//                .catch(function(error) {
//                    throw error
//                }) 
//            })
        })
    })
    .catch(function(error) {
        logger.error('Error resize image id: %d. %s.', image.base_product_id, error.stack)
    })   
})
