module.exports.url = function(source, data) {
    return data.next_url ||  
        source.site + '/cgi-bin/dsp.pl?event=simple&cl=search&search_string=' + encodeURIComponent(data.ccode)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $product = document.querySelector('.main.product')
    if (null !== $product) {
        result = {
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $name = $product.querySelector('h1')
        if (null !== $name) {
            result.name = $name.textContent.trim()
            if (/Parker/gi.test(result.name)) {
                result.brand = 'Parker'
            }
            else if (/Waterman/gi.test(result.name)) {
                result.brand = 'Waterman'
            }
        }
        var $images = $product.querySelectorAll('#gallery-icons a')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                result.images.push($images[i].getAttribute('href').trim())
            }
        }
        $description = $product.querySelector('.item_annotation')
        if (null !== $description) {
            var rows = $description.innerHTML.split('<br>')
            var len = rows.length
            if (len) {
                var details = []
                for (var i = 0; i < len; i++) {
                    var row = rows[i]
                    if (/strong/gm.test(row)) {
                        var match = row.match(/strong>([^<]*)<\/strong>(.*)/)
                        if (match && 3 == match.length) {
                            details.push({
                                name: match[1].replace(':', '').trim(),
                                value: match[2].trim()
                            })
                        }
                    }
                    else { 
                        row = row.trim()
                        if (row.length) {
                            result.description += '<p>' + row + '</p>';
                        }
                    }
                }
                if (details.length) {
                    result.details.push({
                        name: 'Основное',
                        details: details
                    })
                }
            }
        }
    }
    else {
        $products = document.querySelectorAll('.search-page .goods_main')
        var len = $products.length
        for (var i = 0; i < len; i++) {
            var $articul = $products[i].querySelector('.articul')
            if (null !== $articul) {
                if (data.ccode == $articul.textContent.replace('Артикул:', '').trim()) {
                    var $link = $products[i].querySelector('h4 a')
                    if (null !== $link) {
                        url = next_url = source.site + $link.getAttribute('href').trim()
                    }
                    break
                }
            }
        }
    }
    callback({
        url: url,
        next_url: next_url,
        data: result
    })
}
module.exports.cookies = []