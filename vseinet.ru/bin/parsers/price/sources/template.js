module.exports.url = function(source, data) {
    return <absolute url>
}
module.exports.parse = function(source, data, callback) {
    var price = '' 
    if (<has parice>) {
        price = <price>
    }
    callback({
        price: price
    })
}
module.exports.cookies = [
    {name: <name>, value: <value>},
    ...
]