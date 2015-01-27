/*
window.postMessage method is part of HTML5 introductions.
It allows communication between window frames without being subject to same origin policy.
Using postMessage() one can trigger a message event with attached data on another window,
even if the window has different domain, port or a protocol.

The frame where the event is triggered must add an event listener in order to be able to respond.

Let's see an example.
Assume, we are on http://example.com (1) website and would like to make a request to http://example2.net (2) domain.
We first must obtain a reference to (2) window.
This can be either iframe.contentWindow, window.open, or window.frames[].
For our case it's best to create a hidden iframe element and send messages to it.
Here is how it looks.*/

// Create an iframe element
$('<iframe />', { id: 'myFrame', src: 'http://example2.net' }).appendTo('body');

// Get reference to the iframe element
var iframe = $('#myFrame').get(0);

// Send message with {some: "data"} data
iframe.postMessage( {some: 'data'}, 'http://example2.net');

// --------------------------

var iframe = $('#myFrame').get(0);
iframe.postMessage( {some: 'data'}, 'http://example.com');

$(window).on("message", function( event ){
    if (event.origin !== "http://example2.net") return;
    console.log( event.data ); // Logs {name: "Someone", avatar: "url.jpg"}
});
