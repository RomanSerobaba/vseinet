var execSync = require('child_process').execSync

module.exports.encode1251 = function(text) {
    var str = ''
    var buffer = execSync('echo "' + text.replace(/"/g, '\\"') + '" | iconv -t CP1251//TRANSLIT -f UTF-8', function(error, stdout, stderr) {
        return stdout
    })
    for(var i = 0; i < buffer.length; i++) {
        str += buffer[i] != 0xa ? '%' + buffer[i].toString(16).toUpperCase() : ''
    }
    return str
}
module.exports.url = function(source, data) {
    return data.next_url ||
        source.site + '/sitesearch.html?query=' + this.encode1251(data.code || data.name)
}
module.exports.parse = function(source, data, callback) {
    var url = null
    var next_url = null
    var result = null
    var $name = document.querySelector('h1[itemprop=name]')
    if (null !== $name) {
        result = {
            name: $name.textContent.trim(),
            brand: '',
            model: '',
            images: [],
            description: '',
            details: []
        }
        var $brand = document.querySelector('[itemprop=brand]')
        if (null !== $brand) {
            result.brand = $brand.getAttribute('content').trim()
        }
        var $images = document.querySelectorAll('.displayedItem__images__thumbLink')
        var len = $images.length
        if (len) {
            for (var i = 0; i < len; i++) {
                var fullimageurl = $images[i].getAttribute('data-fullimageurl')
                if (null !== fullimageurl) {
                    result.images.push(source.site + fullimageurl.trim())
                }
            }
        }
        else {
            var $image = document.querySelector('#displayedItem__images__bigLinkID img')
            if ($image !== null) {
                result.images.push(source.site + $image.getAttribute('src').trim().replace('/m/', '/b/'))
            }
        }
        var $description = document.querySelector('.descriptionText_cover')
        if (null !== $description) {
            result.description = $description.innerHTML
        }
        var $details = document.querySelectorAll('.dottedList__backLine')
        var len = $details.length
        if (len) {
            var details = []
            for (var i = 0; i < len; i++) {
                var $value = $details[i].nextElementSibling
                if (null !== $value) {
                    if ('Код производителя:' == $details[i].textContent.trim()) {
                        var code = $value.textContent.trim().replace('*', '').replace(/_/g, ' ') 
                        if (code != data.code && parseInt(code) != parseInt(data.code)) {
                            result = null
                            break
                        } 
                    }
                    details.push({
                        name: $details[i].textContent.trim(),
                        value: $value.textContent.trim()
                    })
                }
            }
            if (result && details.length) {
                result.details.push({
                    name: 'Основное',
                    details: details
                })
            }
        }
    }
    else {
        var encode1251js = function(text) {
            var map = {1027: 129, 8225: 135, 1046: 198, 8222: 132, 1047: 199, 1168: 165, 1048: 200, 1113: 154, 1049: 201, 1045: 197, 1050: 202, 1028: 170, 160: 160, 1040: 192, 1051: 203, 164: 164, 166: 166, 167: 167, 169: 169, 171: 171, 172: 172, 173: 173, 174: 174, 1053: 205, 176: 176, 177: 177, 1114: 156, 181: 181, 182: 182, 183: 183, 8221: 148, 187: 187, 1029: 189, 1056: 208, 1057: 209, 1058: 210, 8364: 136, 1112: 188, 1115: 158, 1059: 211, 1060: 212, 1030: 178, 1061: 213, 1062: 214, 1063: 215, 1116: 157, 1064: 216, 1065: 217, 1031: 175, 1066: 218, 1067: 219, 1068: 220, 1069: 221, 1070: 222, 1032: 163, 8226: 149, 1071: 223, 1072: 224, 8482: 153, 1073: 225, 8240: 137, 1118: 162, 1074: 226, 1110: 179, 8230: 133, 1075: 227, 1033: 138, 1076: 228, 1077: 229, 8211: 150, 1078: 230, 1119: 159, 1079: 231, 1042: 194, 1080: 232, 1034: 140, 1025: 168, 1081: 233, 1082: 234, 8212: 151, 1083: 235, 1169: 180, 1084: 236, 1052: 204, 1085: 237, 1035: 142, 1086: 238, 1087: 239, 1088: 240, 1089: 241, 1090: 242, 1036: 141, 1041: 193, 1091: 243, 1092: 244, 8224: 134, 1093: 245, 8470: 185, 1094: 246, 1054: 206, 1095: 247, 1096: 248, 8249: 139, 1097: 249, 1098: 250, 1044: 196, 1099: 251, 1111: 191, 1055: 207, 1100: 252, 1038: 161, 8220: 147, 1101: 253, 8250: 155, 1102: 254, 8216: 145, 1103: 255, 1043: 195, 1105: 184, 1039: 143, 1026: 128, 1106: 144, 8218: 130, 1107: 131, 8217: 146, 1108: 186, 1109: 190}
            var buffer = []
            var str = ''
            for (var i = 0; i < text.length; i++) {
                var ord = text.charCodeAt(i)
                if (ord > 127) {
                    if (!(ord in map)) {
                        throw 'Character `' + text.charAt(i) + '` in `' + text + '` doen`t support in windows-1251'
                    }
                    buffer.push(map[ord])
                } 
                else {
                    buffer.push(ord)
                }
            }
            for(var i = 0; i < buffer.length; i++) {
                str += buffer[i] != 0xa ? '%' + buffer[i].toString(16).toUpperCase() : ''
            }
            return str
        } // encode1251js
        var $items = document.querySelectorAll('.search__findedItem')
        var loop = true
        var len = $items.length 
        if (len) {
            var $wrapper = document.createElement('div')
            document.body.appendChild($wrapper)
            for (var i = 0; i < len && loop; i++) {
                var $link = $items[i].querySelector('.fastViewLink')
                if (null !== $link) {
                    var xhr = new XMLHttpRequest()
                    xhr.open('GET', source.site + '/goods.php?handler=popup&handlermode=preview&itemid=' + $link.dataset.itemid, false)
                    xhr.send()
                    if (200 == xhr.status) {
                        $wrapper.innerHTML = xhr.responseText
                        var $details = $wrapper.querySelectorAll('.dottedList li')
                        var len2 = $details.length
                        if (len2) {
                            for (var j = 0; j < len2; j++) {
                                var $detail = $details[j].querySelector('.dottedList__backLine')
                                if (null !== $detail && 'Код производителя:' == $detail.textContent.trim()) {
                                    $details[j].removeChild($detail)
                                    var code = $details[j].textContent.trim().replace('*', '').replace(/_/g, ' ')
                                    if (code == data.code || parseInt(code) == parseInt(data.code)) {
                                        var $link = $items[i].querySelector('.search__findedItem__itemLink')
                                        if (null !== $link) {
                                            url = next_url = source.site + $link.getAttribute('href').trim()
                                        }
                                        loop = false
                                        break
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if (loop && null == next_url) {
            var params = window.location.search.replace('?', '').split('&')
            var len = params.length
            if (len) {
                var query = '0' + data.code 
                if (('' + parseInt(data.code)) != data.code) {
                    query = encode1251js(data.code.replace(/ /g, '_'))
                }
                for (var i = 0; i < len; i++) {
                    var param = params[i].split('=')
                    if ('query' == param[0] && query != param[1]) {
                        next_url = source.site + '/sitesearch.html?query=' + query
                    }
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