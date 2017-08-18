"use strict";

$(function() {
  $('#login-form').submit(function(event){
    event.preventDefault();
    $("#login-submit").prop("disabled",true);
    $("#login-message").text("Authorizing...");
    sendLogin();
  });
});

function sendLogin() {

  var formData = $("#login-form").serialize();
  var formMessages = $("#login-message");

  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#login-form").attr('action'),
    data: formData,
    statusCode: {
      404:function(){
        grecaptcha.reset();
        $(formMessages).text('Något har gått fel i inloggingen. Error: 404.');
      }
    }
  })
    .done(function(response) {

      // Set the message text.
      $(formMessages).html(response);

      //logged in. do reload
      location.reload();

    })
    .fail(function(data) {
      newtoken();
      //exit prevent doubleclick
      setTimeout(function(){
        grecaptcha.reset();
        $('#login-submit').prop("disabled",false);
      }, 2000);


    // Set the message text.
      if (data.responseText !== '') {
          $(formMessages).html(data.responseText);
        } else {
            $(formMessages).text('Okänt fel vid inloggingen. Tomt svar från servern.');
        };

      });
}


function newtoken() {
  var dataObj = {};
  dataObj["form"] = 'login';
  dataObj["expiration"] = 1000;
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
