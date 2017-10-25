"use strict";


$(function() {

  


  //Scroll listner

  $(window).scroll(function() {
    var y = $(this).scrollTop();
    if (y > 700) {
      console.log("1");
      $('#to-top-chevron').fadeTo( 0 , 1);
    } else {
      console.log("2");
      $('#to-top-chevron').fadeTo( 0 , 0);
    }
  });


  $('#newsletter-form').submit(function(event){
    event.preventDefault();
    $("#newsletter-form-send").prop("disabled",true);
    var formData = $("#newsletter-form").serialize()
    $("#newsletter-form :input").prop("disabled", true);
    $("#newsletter-form-send-default").hide();
    $("#newsletter-loader").show();
    $("#newsletter-response").empty();
    $("#newsletter-response").show();
    sendNewsletter(formData);
  });


});



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
