module.exports.url = function(source, data) {
    return data.url;
}
module.exports.parse = function(source, data, callback) {
    var price = 0;
    var $elementMain = document.querySelector('.eOzonPrice_main'), // rubles
        $elementSubMain = document.querySelector('.eOzonPrice_submain'); // kopeks
    if (null !== $elementMain) {
        price = parseInt($elementMain.textContent.replace(/( )/g, ''));
    }
    if (null !== $elementSubMain) {
        price += $elementMain.textContent / 100;
    }
    callback({
        price: price
    });
}
module.exports.cookies = []
