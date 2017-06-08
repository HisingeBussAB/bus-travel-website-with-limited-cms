"use strict";

$(function() {


  var days = 1;
  var includes = 1;
  var addons = 1;
  var pictures = 1;
  var dates = 1;

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

    $('#trip-add-picture').click(function(event){
      event.preventDefault();
      pictures++;
      $('#pictures-list').append(
        "<p id='picture-" + pictures + "'>"
        + "<input type='file' name='trip-bild[" + pictures + "]'' id='trip-picture-" + pictures + "'>"
        + "</p>");
      });

    $('#trip-remove-picture').click(function(event){
      event.preventDefault();
      $('#picture-' + pictures).remove();
      pictures--;
      });

  $('#trip-add-addon').click(function(event){
    event.preventDefault();
    addons++;
    $('#addons-list').append(
      "<p id='addon-" + addons + "'>"
      + "<input type='text' maxlength='80' name='trip-tillagg[" + addons + "]' id='trip-tillagg-" + addons + "' placeholder='Tillägg'>"
      + "<input type='text' maxlength='80' name='trip-tillagg-pris[" + addons + "]' id='trip-tillagg-" + addons + "-pris' placeholder='100'>:-"
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
        + "<input type='date' name='trip-date[" + dates + "]' id='trip-date-" + dates + "' pattern='[0-9]{4}-[0-9]{2}-[0-9]{2}' title='YYYY-MM-DD' placeholder='YYYY-MM-DD'>"
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
