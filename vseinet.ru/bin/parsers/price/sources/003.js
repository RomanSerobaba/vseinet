module.exports.url = function(source, data) {
    return data.url;
}
module.exports.parse = function(source, data, callback) {
    var price = 0;
    var $element = document.querySelector('.price span');
    if (null !== $element) {
        price = $element.textContent.replace(/(.-|\s)/g, '');
    }
    callback({
        price: price
    });
}
module.exports.cookies = [
    {name: 'geo_ip_region_id_003', value: '58'},
    {name: 'geo_ip_current_city_id_003', value: '80'}  
]
