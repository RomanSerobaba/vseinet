module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('[itemprop=price]')
    if (null !== $element) {
        price = parseFloat($element.getAttribute('content'))
        $element = document.querySelector('.product-bonustest')
        if (null !== $element) {
            price -= parseFloat($element.textContent)
        }
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: 'StoreID', value: '114'}
]
