module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('.product__price')
    if (null !== $element) {
        price = $element.textContent.replace(/(,.*|\s)/ig,'')
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: 'regionID', value: '3257'}
]