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
    var token = grecaptcha.getResponse(bottomCaptchaWidget);
    if (!token) {
      grecaptcha.execute(bottomCaptchaWidget);
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

var bottomCaptchaWidget;
var bodyCaptchaWidget;

var CaptchaCallback = function() {
  bottomCaptchaWidget = grecaptcha.render('recaptcha-footer');
  if ($('#recaptcha-body').length) {
    bodyCaptchaWidget = grecaptcha.render('recaptcha-body');
  }
};

function onUserVerified(token) {
  sendNewsletterForm(token);
}

function sendNewsletterForm(token) {
  event.preventDefault();
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

      dataLayer.push({
        'event': 'newsletter-sent',
        'form_result': 'success',
        'visitorType': 'high-value',
        'visitor_email': $("input[name=email]").val()});

      setTimeout(function(){
        $( "#newsletter-form-send" ).prop("disabled",false);
        $( "#newsletter-form :input").prop("disabled", false);
        $("#newsletter-loader").hide();
        $("#newsletter-form-send-default").show();
      }, 200);

      document.getElementById("newsletter-form").reset();
      grecaptcha.reset(bottomCaptchaWidget)
    })
    .fail(function(data) {
      dataLayer.push({
        'event': 'newsletter-sent',
        'form_result': 'fail',
        'visitorType': 'high-value',
        'visitor_email': $("input[name=email]").val()});
      if (data.status == 404)
        $( "#newsletter-response" ).html( "Något har gått fel. Kunde inte hitta svarssidan." );
      else
        $( "#newsletter-response" ).html( "Något har gått fel. " + data.responseText );
      $( "#newsletter-form-send" ).prop("disabled",false);
      $( "#newsletter-form :input" ).prop("disabled", false);
      $("#newsletter-loader").hide();
      $("#newsletter-form-send-default").show();
      grecaptcha.reset(bottomCaptchaWidget)
    });
}
