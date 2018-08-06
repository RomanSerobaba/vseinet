module.exports.url = function(source, data) {
    return data.next_url || source.site + '/search?' + require('querystring').stringify({
        q: data.artikul,
    });
}

module.exports.wait = function(url) {
    if (/search/.test(url)) {
        return 'a[href*="/product/"]';
    }
    return 'img[alt][title]';
}

module.exports.parse = function(source, data, callback) {
    var url = null;
    var next_url = null;
    var result = null;
    var $root = document.querySelector('a[href="/catalog-list/"');
    if (null !== $root) {
        result = {
            breadcrumbs: [],
            name: '',
            brand: '',
            model: '',
            images: [],
            description: '',
            details: [],
        };
        $breadcrumbs = $root.closest('ul').querySelectorAll('a');
        var len = $breadcrumbs.length ;
        for (var i = 1; i < len - 1; i++) {
            result.breadcrumbs.push({
                name: $breadcrumbs[i].textContent.trim(),
                url: source.site + $breadcrumbs[i].getAttribute('href').trim(),
            });
        }
        var $name = document.querySelector('h1');
        if (null !== $name) {
            result.name = $name.textContent.trim();
            var $main = $name.nextElementSibling;
            if (null !== $main) {
                var $items = $main.querySelectorAll('div > span');
                var len = $items.length;
                for (var i = 0; i < len; i++) {
                    var item = $items[i].textContent.trim();
                    if (/Код/.test(item)) {
                        result.code = item.replace('Код:', '').trim();
                    }
                    else if (/Артикул/.test(item)) {
                        result.artikul = item.replace('Артикул:', '').trim();
                    }
                    else if (/Штрихкод/.test(item)) {
                        result.items_code = item.replace('Штрихкод:', '').trim();
                    }
                    else if (/Бренд/.test(item)) {
                        result.brand = item.replace('Бренд', '').trim();
                    }
                }
                var $content = $main.nextElementSibling;
                if (null !== $content) {
                    var $tabs = $content.querySelectorAll('div[name="ProductOnePageTabs"] span');
                    var $sheets = $content.childNodes;
                    var len = $tabs.length;
                    for (var i = 0; i < len; i++) {
                        var tab = $tabs[i].textContent.trim();
                        if ('Описание' == tab) {
                            for (var j = 1; j < $sheets.length; j++) {
                                var description = $sheets[j].textContent.trim();
                                if (description) {
                                    result.description = description;
                                    break;
                                }
                            }
                        }
                        if ('Характеристики' == tab) {
                            $tabs[i].parentNode.click()
                            var $details =  $content.querySelectorAll('div[title]');
                            var details = [];
                            for (var j = 0; j < $details.length; j++) {
                                details.push({
                                    name: $details[j].parentNode.previousElementSibling.textContent.trim(),
                                    value: $details[j].getAttribute('title').trim(),
                                });
                            }
                            if (details.length) {
                                result.details.push({
                                    name: 'Основное',
                                    details: details,
                                });
                            }
                        }
                    }
                }
            }
        }
        var $image = document.querySelector('img[alt][title]');
        if (null !== $image) {
            var $parent = $image.parentNode.parentNode.nextElementSibling;
            var $gallery = null === $parent ? [$image] : $parent.querySelectorAll('img');
            var len = $gallery.length;
            var promises = [];
            for (var i = 0; i < len; i++) {
                (function(i) {
                    promises.push(new Promise(function(fulfill, reject) {
                        setInterval(function() {
                            if (/\/upload/.test($gallery[i].getAttribute('src'))) {
                                fulfill();
                            }
                        }, 100);
                    }));
                })(i);
            }
            Promise.all(promises).then(function() {
                for (var i = 0; i < len; i++) {
                    result.images.push(source.site + $gallery[i].getAttribute('src').trim());
                }
                callback({
                    url: url,
                    next_url: next_url,
                    data: result,
                });
            });
        }
    }
    else {
        var $links = document.querySelectorAll('a[href^="/product/"');
        var len = $links.length;
        for (var i = 0; i < len; i++) {
            if (!/scroll/.test($links[i].getAttribute('href'))) {
                var $items = $links[i].nextElementSibling.children;
                for (var j = 0; j < $items.length; j++) {
                    var artikul = $items[j].textContent.trim();
                    if (/Арт:/.test(artikul)) {
                        artikul = artikul.replace('Арт:', '').trim();
                        if (artikul.toLowerCase() == data.artikul.toLowerCase()) {
                            url = next_url = source.site + $links[i].getAttribute('href').trim();
                        }
                        break;
                    }
                }
            }
        }
        callback({
            url: url,
            next_url: next_url,
            data: result,
        });
    }
}

module.exports.cookies = [];