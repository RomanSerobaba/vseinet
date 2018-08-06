module.exports.url = function(source, data) {
    return data.url
}
module.exports.parse = function(source, data, callback) {
    var $lis = document.querySelectorAll('.on-list li[data-i]'),
        $points = new Array()

    if (null !== $lis) {
        for (var i = 0; i < $lis.length; i++) {
            var li = $lis[i];
    
            $points[i] = {
                'name' : li.querySelector('a').innerHTML,
                'link' : li.querySelector('a').href,
                'id' : li.querySelector('a').href.match(/\?filial=(\d+)/)[1],
                'trading_id' : source.id
            };
        }
    }
    
    callback({
        points: $points
    })
}
