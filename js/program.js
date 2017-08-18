"use strict";


$(function() {
  $('#get-program-form').submit(function(event){
    event.preventDefault();
    $("#get-program-button").prop("disabled",true);
    var formData = $("#get-program-form").serialize()
    $("#get-program-form :input").prop("disabled", true);
    $(".ajax-loader").show();
    $(".ajax-response").empty();
    sendForm(formData);
  });


});


function sendForm(formData) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#get-program-form").attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      console.log(data);
      $( "#ajax-response" ).html( data );
      newtoken();
      setTimeout(function(){
        $( "#get-program-button" ).prop("disabled",false);
        $( "#get-program-form :input").prop("disabled", false);
        $(".ajax-loader").hide();
      }, 200);

      document.getElementById("get-program-form").reset();
    })
    .fail(function(data) {
      console.log(data);
      newtoken();
      if (data.status == 404)
        $( "#ajax-response" ).html( "Något har gått fel. Kunde inte hitta svarssidan." );
      else
        $( "#ajax-response" ).html( "Något har gått fel. " + data.responseText );
      $( "#get-program-button" ).prop("disabled",false);
      $( "#get-program-form :input" ).prop("disabled", false);
      $(".ajax-loader").hide();
    });
}

function newtoken() {
  var dataObj = {};
  dataObj["form"] = 'program';
  dataObj["expiration"] = 2000;
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
      $( "#ajax-response" ).append( "<p>Kunde inte generera ny säkerhetoken. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a></p>" );
    });
}
