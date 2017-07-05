"use strict";

function newtoken(idfield, tokenfield, reply) {
  var dataObj = {};
  dataObj["form"] = type;
  dataObj["expiration"] = 5400;
  dataObj["unique"] = true;
  $.ajax({
    type: 'POST',
    cache: false,
    url: '/ajax/gettoken',
    data: dataObj,
    dataType: "json",
  })
    .done(function(data) {
      $( idfield ).val( data.token.id );
      $( tokenfield ).val( data.token.token );
    })
    .fail(function() {
      $( reply ).append( "<p>Kunde inte generera ny säkerhetoken. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a></p>" );
    });
}
