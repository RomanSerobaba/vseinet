module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var price = 0;
    var $element = document.querySelector('[data-role="current-price-value"]');
    if (null !== $element) {
        price = $element.getAttribute('data-price-value');
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: 'city_path', value: 'penza'}
]
