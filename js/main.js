"use strict";


$(function() {


  //Scroll listner

  $(window).scroll(function() {
    var y = $(this).scrollTop();
    if (y > 600) {
      $('#to-top-chevron').fadeTo( 0 , 1);
      $('#main-header').addClass("docked");
    } else {
      $('#to-top-chevron').fadeTo( 0 , 0);
      $('#main-header').removeClass("docked");
    }
  });


  $('#newsletter-form').submit(function(event){
    event.preventDefault();
    $("#newsletter-form-send").prop("disabled",true);
    var token = grecaptcha.getResponse();
    if (!token) {
      grecaptcha.execute();
    } else {
      sendNewsletterForm(token);
    }

  });


});



function onUserVerified(token) {
  sendNewsletterForm(token);
}

function sendNewsletterForm(token) {
  var formData = $("#newsletter-form").serialize();
  $("#newsletter-form :input").prop("disabled", true);
  $("#newsletter-form-send-default").hide();
  $("#newsletter-loader").show();
  $("#newsletter-response").empty();
  $("#newsletter-response").show();
  sendNewsletter(formData);
}

function sendNewsletter(formData) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#newsletter-form").attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      $( "#newsletter-response" ).html( data );

      fbq('track', 'Lead');
      ga('send', 'event', 'Lead', 'Nyhetsbrev', 'Nyhetsbrev', 0);

      setTimeout(function(){
        $( "#newsletter-form-send" ).prop("disabled",false);
        $( "#newsletter-form :input").prop("disabled", false);
        $("#newsletter-loader").hide();
        $("#newsletter-form-send-default").show();
      }, 200);

      document.getElementById("newsletter-form").reset();
    })
    .fail(function(data) {
      if (data.status == 404)
        $( "#newsletter-response" ).html( "Något har gått fel. Kunde inte hitta svarssidan." );
      else
        $( "#newsletter-response" ).html( "Något har gått fel. " + data.responseText );
      $( "#newsletter-form-send" ).prop("disabled",false);
      $( "#newsletter-form :input" ).prop("disabled", false);
      $("#newsletter-loader").hide();
      $("#newsletter-form-send-default").show();
    });
}
