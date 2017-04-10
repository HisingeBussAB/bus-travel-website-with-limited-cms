"use strict";

$(function() {

  loadCategories();


  /* Submit listener
  $('#login-form').submit(function(event){
    event.preventDefault();
    $("#login-submit").prop("disabled",true);
    $("#login-message").text("Authorizing...");
    sendLogin();
  });
  */
});


function loadCategories() {

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/adminajax/getcategories',
    statusCode: {
      404:function(){
        console.log('Något har gått fel. Error: 404.');
      }
    }
  })
    .done(function(response) {
      console.log(response);
      if (response.length <= 0) {
        
      }
    })
    .fail(function(data) {
      console.log(data)
      console.log("FEL"); //!!!TODO
    });
}

/* AJAX construct
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
*/
