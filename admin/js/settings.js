"use strict";

$(function() {
    $("#new-password").on("keypress keyup keydown", function() {
        var pass = $(this).val();
        $("#new-password-strength_human").text(checkPassStrength(pass));
        $("#new-password-strength_score").text("(" + scorePassword(pass) + ")");
        $("#new-password-strength_human").css("display", "inline");
        $("#new-password-strength_score").css("display", "inline");
        if (scorePassword(pass) > 80) {
          $("#new-password-strength_human").css("color", "green");
          $("#new-password-strength_score").css("color", "green");
        }
        else if (scorePassword(pass) > 60) {
          $("#new-password-strength_human").css("color", "orange");
          $("#new-password-strength_score").css("color", "orange");
        }
        else if (scorePassword(pass) <= 60) {
          $("#new-password-strength_human").css("color", "red");
          $("#new-password-strength_score").css("color", "red");
        }
    });

    $('#send-pwd').click(function(event){
      event.preventDefault();
      $("#send-pwd").prop("disabled",true);
      var formData = $("#pwd-form").serialize()
      $("#pwd-form :input").prop("disabled", true);
      sendForm(formData, "#password-reply", "#send-pwd", "#pwd-form", "password");
    });

    $('#send-settings').click(function(event){
      event.preventDefault();
      $("#send-settings").prop("disabled",true);
      var formData = $("#settings-form").serialize()
      $("#settings-form :input").prop("disabled", true);
      sendForm(formData, "#settings-reply", "#send-settings", "#settings-form", "settings");
    });



});

function sendForm(formData, reply, button, form, type) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $( form ).attr('action'),
    data: formData,
    dataType: "json",
  })
    .done(function(data) {
      $( reply ).html( data.responseText );
      newtoken(type, reply);
      setTimeout(function(){ $( form + " :input").prop("disabled", false); }, 1000);
      setTimeout(function(){ $( button ).prop("disabled",false); }, 1000);
    })
    .fail(function(data) {
      if (data.status == 404)
        $( reply ).html( "Något har gått fel. Error: 404." )
      else
        $( reply ).html( "Något har gått fel. Fel: " + data.responseText );

      $( button ).prop("disabled",false);
      $( form + " :input").prop("disabled", false);
    });
}



/* Password strenght functions from tm_lv
https://stackoverflow.com/questions/948172/password-strength-meter
*/
function scorePassword(pass) {
    var score = 0;
    if (!pass)
        return score;

    // award every unique letter until 5 repetitions
    var letters = new Object();
    for (var i=0; i<pass.length; i++) {
        letters[pass[i]] = (letters[pass[i]] || 0) + 1;
        score += 5.0 / letters[pass[i]];
    }

    // bonus points for mixing it up
    var variations = {
        digits: /\d/.test(pass),
        lower: /[a-z]/.test(pass),
        upper: /[A-Z]/.test(pass),
        nonWords: /\W/.test(pass),
    }

    var variationCount = 0;
    for (var check in variations) {
        variationCount += (variations[check] == true) ? 1 : 0;
    }
    score += (variationCount - 1) * 10;

    return parseInt(score);
}

function checkPassStrength(pass) {
    var score = scorePassword(pass);
    if (score > 80)
        return "Starkt";
    if (score > 60)
        return "Bra";
    if (score <= 60)
        return "Svagt";

    return "";
}


function newtoken(type, reply) {
  var dataObj = {};
  dataObj["form"] = type;
  dataObj["expiration"] = 5400;
  dataObj["unique"] = true;
  $.ajax({
    type: 'POST',
    cache: false,
    url: '/ajax/gettoken',
    data: dataObj,
    dataType: "json",
  })
    .done(function(data) {
      $( "#tokenid-" + type ).val( data.token.id );
      $( "#token-" + type ).val( data.token.token );
    })
    .fail(function() {
      $( reply ).append( "<p>Kunde inte generera ny säkerhetoken. <a href='javascript:window.location.href=window.location.href'>Ladda om sidan.</a></p>" );
    });
}
