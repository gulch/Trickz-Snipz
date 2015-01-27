/**
 * CORS is a W3C recommendation and supported by all major browsers.
 * It makes use of HTTP headers to help browser decide if a cross-domain AJAX request is secure.
 * Basically, when you make a CORS request, browser adds Origin header with the current domain value.
 * For example:
 *
 *      Origin: http://jquery-howto.blogspot.com
 *
 * The server, where the script makes its' CORS request,
 * checks if this domain is allowed and sends response with Access-Control-Allow-Origin response header.
 * Upon receiving, browser checks if the header is present and has the current domain value.
 * If domains match, browser carries on with AJAX request, if not throws an error.
 *
 *      Access-Control-Allow-Origin: http://your-site.com
 *
 * To make a CORS request you simply use XMLHttpRequest in Firefox 3.5+,
 * Safari 4+ & Chrome and XDomainRequest object in IE8+.
 * When using XMLHttpRequest object,
 * if the browser sees that you are trying to make a cross-domain request it will seamlessly trigger CORS behaviour.
 *
 * Here is a javascript function that helps you create a cross browser CORS object.
 */

function createCORSRequest(method, url){
    var xhr = new XMLHttpRequest();
    if ("withCredentials" in xhr){
        // XHR has 'withCredentials' property only if it supports CORS
        xhr.open(method, url, true);
    } else if (typeof XDomainRequest != "undefined"){ // if IE use XDR
        xhr = new XDomainRequest();
        xhr.open(method, url);
    } else {
        xhr = null;
    }
    return xhr;
}

var request = createCORSRequest( "get", "http://www.google.com" );
if ( request ){
    // Define a callback function
    request.onload = function(){};
    // Send request
    request.send();
}
