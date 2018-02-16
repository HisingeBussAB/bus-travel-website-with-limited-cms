"use strict";


$(function() {

  var heightAdjust = $('#main-header').outerHeight();
  var clickBlocker = false;
  //Scroll listner

  $(window).scroll(function() {
    var y = $(this).scrollTop();
    if (y > 500) {
      $('#to-top-chevron').fadeTo( 0 , 1);
      $('body').css('margin-top',heightAdjust);
      $('#main-header').addClass("docked");
      $('.hide-on-tiny-show-in-docked').each(function() {
        $( this ).removeClass("hidden-xs");
      });
    } else {
      $('#to-top-chevron').fadeTo( 0 , 0);
      $('body').css('margin-top',0);
      $('#main-header').removeClass("docked");
      $('.hide-on-tiny-show-in-docked').each(function() {
        $( this ).addClass("hidden-xs");
      });
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

  $('#main-navigation-toggle').mouseenter(function() {
    $('#main-navigation').addClass('shown-collapsable-nav');
    $('#main-navigation').removeClass('hidden-collapsable-nav');
    clickBlocker = true;
    setTimeout(function(){ clickBlocker = false; }, 500);
  });

  $('#main-navigation').mouseleave(function() {
    $('#main-navigation').removeClass('shown-collapsable-nav');
    $('#main-navigation').addClass('hidden-collapsable-nav');
    clickBlocker = true;
    setTimeout(function(){ clickBlocker = false; }, 500);
  });

  $('#main-navigation-toggle').click(function() {
    if (!clickBlocker) {
      $('#main-navigation').toggleClass('hidden-collapsable-nav');
      $('#main-navigation').toggleClass('shown-collapsable-nav');
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
