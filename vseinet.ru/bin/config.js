const path = require('path')
const yaml = require('yamljs')
const parameters = path.normalize(__dirname + '/../app/config/parameters.yml')

const config = yaml.load(parameters).parameters;

config.logs_path = path.normalize(__dirname + '/../var/logs')

config.php = '/usr/bin/php'
config.console = path.normalize(__dirname + '/console')

config.ws_port = 8080

// var config = {
//     client: {
//         db: {
//             host: '127.0.0.1', // localhost
//             user: 'root', // 'local user'
//             password: 'dst138', // 'local passport'
//             database: 'data_parsers', // 'local database'
//         },
//         logpath: path.normalize(__dirname + '/logs'),
//     },
//     server: {
//         ip: '192.168.122.195', // 'vseinet ip'
//         host: 'dev.vseinet.ru', // 'vseinet.ru'
//         port: 80,
//         wsPort: 8080,
//         db: {
//             host: '192.168.122.195', // 'vseinet ip'
//             user: 'k2fl', // 'vseinet'
//             password: 'uKFY8~qo~$', // 'vseinet passwort'
//             database: 'k2fl', // 'vseinet'
//         },
//         php: '/usr/bin/php71',
//         console: path.normalize(__dirname + '/console'),
//         logpath: path.normalize(__dirname + '/../var/logs'),
//     },
// }

module.exports = config