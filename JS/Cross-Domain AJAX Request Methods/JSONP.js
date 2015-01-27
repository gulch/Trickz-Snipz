// https://graph.facebook.com/10150232496792613
/*{
    "id": "10150232496792613",
    "url": "http://jquery-howto.blogspot.com/",
    "type": "website",
    "title": "jQuery Howto",
...
}*/

$.getJSON( "https://graph.facebook.com/10150232496792613?callback=?", function( data ){
    console.log( data.title ); // Logs "jQuery Howto"
});

// OR using $.ajax()
$.ajax({
    type:     "GET",
    url:      "https://graph.facebook.com/10150232496792613",
    dataType: "jsonp",
    success: function(data){
        console.log(data);
    }
});
