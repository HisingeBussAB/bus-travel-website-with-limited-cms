"use strict";

$(function() {

  var days = $('.trip-text').length
  var includes = $('.include-item').length
  var addons =  $('.addon-item').length
  var pictures = 1;
  var dates = $('.date-item').length

  // Listeners
  $('#trip-add-paragraph').click(function(event){
    event.preventDefault();
    days++;
    $('#trip-text').append(
      "<fieldset id='trip-text-" + days + "'>"
      + "<label for='trip-text-heading[" + days + "]'>Dag " + days + "</label>"
      + "<input type='text' maxlength='80' name='trip-text-heading[" + days + "]' id='trip-text-" + days + "-heading' placeholder='Dag " + days + "'>"
      + "<textarea type='text' name='trip-text[" + days + "]' id='trip-text-" + days + "-text'></textarea>"
      + "</fieldset>");
    });

  $('#trip-remove-paragraph').click(function(event){
    event.preventDefault();
    $('#trip-text-' + days).remove();
    days--;
    });


  $('#trip-add-includes').click(function(event){
    event.preventDefault();
    includes++;
    $('#includes-list').append(
      "<p id='include-" + includes + "'>"
      + "<input type='text' maxlength='80' name='trip-ingar[" + includes + "]'' id='trip-tillagg-" + includes + "'>"
      + "</p>");
    });

  $('#trip-remove-includes').click(function(event){
    event.preventDefault();
    $('#include-' + includes).remove();
    includes--;
    });

  $('#trip-add-addon').click(function(event){
    event.preventDefault();
    addons++;
    $('#addons-list').append(
      "<p id='addon-" + addons + "'>"
      + "<input type='text' maxlength='80' name='trip-tillagg[" + addons + "]' id='trip-tillagg-" + addons + "' placeholder='Tillägg'>"
      + "<input type='number' name='trip-tillagg-pris[" + addons + "]' id='trip-tillagg-pris-" + addons + "-pris' placeholder='100'>:-"
      + "</p>");
    });

  $('#trip-remove-addon').click(function(event){
    event.preventDefault();
    $('#addon-' + addons).remove();
    addons--;
    });

    $('#trip-add-date').click(function(event){
      event.preventDefault();
      dates++;
      $('#dates-list').append(
        "<p id='date-" + dates + "'>"
        + "<input type='date' name='trip-date[" + dates + "]' id='trip-date-" + dates + "' placeholder='YYYY-MM-DD'>"
        + "</p>");
      });

    $('#trip-remove-date').click(function(event){
      event.preventDefault();
      $('#date-' + dates).remove();
      dates--;
      });


  $(".stop-input").on('input', function(event){
    $(this).parent().parent().children(":first").children(".stop-checkbox").prop('checked', true);
  });

});
