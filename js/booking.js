"use strict";


$(function() {

  $('#booktour-form').submit(function(event){
    event.preventDefault();
    if (!$("#terms").is(':checked')) {
      $("#terms-label").css("color", "red");
      $( "#ajax-response" ).html( "<p>Vänligen godkänn resevillkoren</p>" );
      return false;
    } else {
    $(".ajax-response").empty();
    $("#booktour-button").prop("disabled",true);
    var formData = $("#booktour-form").serialize()
    $("#booktour-form :input").prop("disabled", true);
    $("#booktour-button").hide();
    $(".ajax-loader").show();
    sendForm(formData);
    }
  });




});


function sendForm(formData) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#booktour-form").attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      dataLayer.push({
        'visitorType': 'high-value',
        'form_result': 'success',
        'conversionValue': Number($("#price-int").val()).toFixed(2),
        'visitor_email': $("input[name=email]").val(),
        'visitor_phone': $("input[name=phone]").val(),
        'event': 'booking-form-sent'});
      $( "#ajax-response" ).html( data );
      newtoken();
      setTimeout(function(){
        $( "#booktour-button" ).prop("disabled",false);
        $( "#booktour-form :input").prop("disabled", false);
        $(".ajax-loader").hide();
        $("#booktour-button").show();
      }, 200);

      document.getElementById("booktour-form").reset();
    })
    .fail(function(data) {
      dataLayer.push({
        'form_result': 'fail',
        'visitorType': 'high-value',
        'conversionValue': Number($("#price-int").val()).toFixed(2),
        'visitor_email': $("input[name=email]").val().toLowerCase(),
        'visitor_phone': Number($("input[name=phone]").val()),
        'event': 'booking-form-sent'});
      newtoken();
      if (data.status == 404)
        $( "#ajax-response" ).html( "Något har gått fel. Kunde inte hitta svarssidan." );
      else
        $( "#ajax-response" ).html( "Något har gått fel. " + data.responseText );
      $( "#booktour-button" ).prop("disabled",false);
      $( "#booktour-form :input" ).prop("disabled", false);
      $(".ajax-loader").hide();
      $("#booktour-button").show();
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
