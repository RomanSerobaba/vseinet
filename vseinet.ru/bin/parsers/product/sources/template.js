module.exports.url = function(source, data) {
    return <absolute url>
}
module.exports.parse = function(source, data, callback) {
    var url = null 
    var next_url = null 
    var result = null 
    if (<product page>) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        <filling result>
            breadcrumbs = [
                {
                    name: <category name>,
                    url: <category absolute url>
                },
                ...
            ]
            images = [
                <image absolute url>,
                ...
            ]
            details = [
                {
                    name: <group name>, 
                    details: [
                        {
                            name: <detail name>, 
                            value: <detail value>
                        },
                        ...
                    ]
                },
                ...
            ]
        /<filling result>
    }
    else {
        next_url = <product page absolute url>
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = [
    {name: <name>, value: <value>},
    ...
]