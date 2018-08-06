module.exports.url = function(source, data) {
    return <absolute url>
}
module.exports.parse = function(source, data, callback) {
    var points = ''

    if (<has points>) {
        for (var i = 0; i < <points.length>; i++) {
            points[i] = {
                'name' : <points.i>.querySelector(<name>),
                'link' : <points.i>.querySelector(<link>),
                'id' : <points.i>.querySelector(<id>)
            };
        }
    }
    
    callback({
        points: points
    })
}