"use strict";

window.addEventListener('load', function() {
  document.getElementById('login-form').addEventListener('submit', function(e){
    sendLogin(e);
  })
});

function sendLogin(event) {
  event.preventDefault();
  $("#login-submit").disabled = true;
  $("#login-message").text("Sending...");


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

      //exit prevent doubleclick
      setTimeout(function(){
        $('#login-submit').disabled = false;
      }, 9000);

    })
    .fail(function(data) {

      //exit prevent doubleclick
      setTimeout(function(){
        grecaptcha.reset();
        $('contact-submit').disabled = false;
      }, 2000);

      // Set the message text.
      if (data.responseText !== '') {
          $(formMessages).text(data.responseText);
        } else {
            $(formMessages).text('Okänt fel vid inloggingen. Tomt svar från take-login.php.');
        };
        $('#contact-submit').disabled = false;
      });
};
