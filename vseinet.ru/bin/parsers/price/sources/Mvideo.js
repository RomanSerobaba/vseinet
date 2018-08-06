module.exports.url = function(source, data) {
    return data.url.replace('www.', 'penza.')
}
module.exports.parse = function(source, data, callback) {
    var price = 0
    var $element = document.querySelector('[itemprop=price]')
    if (null !== $element) {
        price = $element.textContent.trim()
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: 'MVID_CITY_ID', value: 'CityCZ_7182'}
]
