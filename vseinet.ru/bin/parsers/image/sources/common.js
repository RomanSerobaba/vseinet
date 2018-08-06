module.exports.url = function(source, data) {
    return 'about:blank';//data.url
}

module.exports.parse = function(source, data, callback) {
    var image = new Image();
    image.onerror = function() {
        callback({
            imageBase64: null
        });
    }
    image.onload = function() {
        var canvas = document.createElement('canvas');
        canvas.width = image.width;
        canvas.height = image.height;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(image, 0, 0, image.width, image.height);
        imageBase64 = canvas.toDataURL('image/jpeg');
        callback({
            imageBase64: imageBase64
        });
    }
    document.body.appendChild(image);
    image.src = data.url;
}
module.exports.cookies = []
