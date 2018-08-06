module.exports.url = function(source, data) {
    return data.url.replace('www.', 'penza.').replace('pnz.', 'penza.')
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('[data-price]')
    if (null !== $element) {
        price = $element.getAttribute('data-price')
    }
    callback({
        price: price
    })
}
module.exports.cookies = []