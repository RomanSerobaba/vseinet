module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('[itemprop=price]')
    if (null !== $element) {
        price = $element.getAttribute('content')
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: 'vi_represent_id', value: '58'}
]