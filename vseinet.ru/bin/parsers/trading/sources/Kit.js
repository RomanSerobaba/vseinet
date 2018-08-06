module.exports.url = function(source) {
    return source.url
}
module.exports.parse = function(source, data, callback) { 
   var $as = document.querySelectorAll('.russia + .citys a'),
        $points = new Array()

    if (null !== $as) {
        for (var i = 0; i < $as.length; i++) {
            var a = $as[i];
    
            $points[i] = {
                'name' : a.innerHTML,
                'link' : a.href,
                'id' : a.href.replace(/http:\/\/tk-kit\.ru\/contact\//,'').replace('/',''),
                'trading_id' : source.id
            };
        }
    }
    
    callback({
        points: $points
    })
}
module.exports.cookies = []