"use strict";

$(function() {
    $('#save-trip-button').click(function(event){
      event.preventDefault();
      $("#save-trip-button").prop("disabled",true);
      var formData = $("#category-form").serialize()
      $("#category-form :input").prop("disabled", true);
      sendForm(formData);
    });



});

function sendForm(formData) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#category-form").attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      $( "#form-reply" ).html( data.responseText );
      newtoken();
      setTimeout(function(){ $( "#category-form :input").prop("disabled", false); }, 1000);
      setTimeout(function(){ $( "#save-trip-button" ).prop("disabled",false); }, 1000);
    })
    .fail(function(data) {
      console.log(data);
      if (data.status == 404)
        $( "#form-reply" ).html( "Något har gått fel. Error: 404." )
      else
        $( "#form-reply" ).html( "Något har gått fel. Fel: " + data.responseText );

      $( "#save-trip-button" ).prop("disabled",false);
      $( "#category-form :input" ).prop("disabled", false);
    });
}

function newtoken() {
  var dataObj = {};
  dataObj["form"] = 'category';
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
      $( "#tokenid" ).val( data.token.id );
      $( "#token" ).val( data.token.token );
    })
    .fail(function() {
      $( "#form-reply" ).append( "<p>Kunde inte generera ny säkerhetoken. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a></p>" );
    });
}
