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
      $( "#ajax-response" ).html( data );
      newtoken();
      setTimeout(function(){
        $( "#booktour-button" ).prop("disabled",false);
        $( "#booktour-form :input").prop("disabled", false);
        $(".ajax-loader").hide();
        $("#booktour-button").show();
      }, 200);

      fbq('track', 'Purchase', {
        currency: 'SEK',
        value: $("#price-int").val(),
      });

      ga('send', 'event', 'Order', 'Order', 'Order', $("#price-int").val());

      document.getElementById("booktour-form").reset();
    })
    .fail(function(data) {
      console.log(data);
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
