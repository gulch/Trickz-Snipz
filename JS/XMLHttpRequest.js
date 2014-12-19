var xhr = function() {
    var xhr = new XMLHttpRequest();
    return function( method, url, callback ) {
        xhr.onreadystatechange = function() {
            if ( xhr.readyState === 4 ) {
                callback( xhr.responseText );
            }
        };
        xhr.open( method, url );
        xhr.send();
    };
}();
/*
 xhr('get', 'http://google.com', function(data) {
    console.log(data);
 });
 */