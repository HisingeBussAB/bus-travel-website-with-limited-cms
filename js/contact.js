"use strict";


$(function() {
  $('#get-contact-form').submit(function(event){
    event.preventDefault();
    $("#get-contact-button").prop("disabled",true);
    var formData = $("#get-contact-form").serialize()
    $("#get-contact-form :input").prop("disabled", true);
    $("#contact-text").prop("disabled", true);
    $("#get-contact-button").hide();
    $(".ajax-loader").show();
    $(".ajax-response").empty();
    sendForm(formData);
  });




});


function sendForm(formData) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#get-contact-form").attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      dataLayer.push({
        'visitorType': 'high-value',
        'event': 'contact-form-sent',
        'form_result': 'success',
        'visitor_email': $("input[name=email]").val().toLowerCase(),
        'visitor_phone': Number($("input[name=tel]").val()),
    });
      $( "#ajax-response" ).html( data );
      newtoken();
      setTimeout(function(){
        $( "#get-contact-button" ).prop("disabled",false);
        $( "#get-contact-form :input").prop("disabled", false);
        $("#contact-text").prop("disabled", false);
        $(".ajax-loader").hide();
        $("#get-contact-button").show();
      }, 200);

      document.getElementById("get-contact-form").reset();
    })
    .fail(function(data) {
      dataLayer.push({
        'visitorType': 'high-value',
        'event': 'contact-form-sent',
        'form_result': 'fail',
        'visitor_email': $("input[name=email]").val()
    });
      newtoken();
      if (data.status == 404)
        $( "#ajax-response" ).html( "Något har gått fel. Kunde inte hitta svarssidan." );
      else
        $( "#ajax-response" ).html( "Något har gått fel. " + data.responseText );
      $( "#get-contact-button" ).prop("disabled",false);
      $( "#get-contact-form :input" ).prop("disabled", false);
      $(".ajax-loader").hide();
      $("#get-contact-button").show();
    });
}

function newtoken() {
  var dataObj = {};
  dataObj["form"] = 'contact';
  dataObj["expiration"] = 4000;
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
