module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('#product_price_rub')
    if (null !== $element) {
        price = $element.textContent
    }
    callback({
        price: price
    })
}
module.exports.cookies = []