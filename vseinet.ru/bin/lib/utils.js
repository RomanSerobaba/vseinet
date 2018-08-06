module.exports.randomstr = function(size) {
    var pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    var result = []
    for (var i = 0; i < size; i++) {
        result.push(pool[Math.floor(Math.random() * pool.length)])
    }
    return result.join('')
}