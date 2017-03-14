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
      $(formMessages).text(response);

      //logged in. do reload
      setTimeout(function(){
        location.reload();
      }, 100);

    })
    .fail(function(data) {

      //exit prevent doubleclick
      setTimeout(function(){
        grecaptcha.reset();
        $('#login-submit').prop("disabled",false);
      }, 1000);


    // Set the message text.
      if (data.responseText !== '') {
          $(formMessages).text(data.responseText);
        } else {
            $(formMessages).text('Okänt fel vid inloggingen. Tomt svar från take-login.php.');
        };

      });
}
