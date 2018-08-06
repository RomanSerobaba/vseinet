module.exports.url = function(source) {
    return source.url
}
module.exports.parse = function(source, data, callback) { 
    var $as = document.querySelectorAll('.name_city a.open-contact'),
        $points = new Array();if (null !== $as) {
        for (var i = 0; i < $as.length; i++) {
            var a = $as[i];
    
            $points[i] = {
                'name' : a.innerHTML,
                'link' : '',
                'id' : a.getAttribute(['data-id']),
                'trading_id' : source.id
            };
        }
    }
    
    callback({
        points: $points
    })
}
module.exports.cookies = []