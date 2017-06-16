"use strict";

$(function() {


  // Listeners
  $('#form-new-category').submit(function(event){
    event.preventDefault();
    $( "#category-list" ).hide();
    $( "#category-list-loading" ).show();
    $("#form-new-category-submit").prop("disabled",true);
    newItem("category");
    $("#form-new-category-name").val('');
  });

  $('#form-new-roomopt').submit(function(event){
    event.preventDefault();
    $( "#roomopt-list" ).hide();
    $( "#roomopt-list-loading" ).show();
    $("#form-new-roomopt-submit").prop("disabled",true);
    newItem("roomopt");
    $("#form-new-roomopt-name").val('');
  });

  $('#form-new-stop').submit(function(event){
    event.preventDefault();
    $( "#stop-list" ).hide();
    $( "#stop-list-loading" ).show();
    $("#form-new-stop-submit").prop("disabled",true);
    newItem("stop");
    $("#form-new-stop-name").val('');
  });

  //Load content

  loadItem("category");
  loadItem("roomopt");
  loadItem("stop");


});


function loadItem(item) {
  $.getJSON({
    type: 'POST',
    cache: false,
    url: '/adminajax/get' + item,
    dataType: "json",
  })
    .done(function(response) {
      if (response.length > 0) {
        renderItem(item, response);
      } else {
        $( "#" + item + "-list-content" ).html('');
        $( "#" + item + "-list-loading" ).hide();
        $( "#" + item + "-list" ).show();
      }
    })
    .fail(function(data) {
      if (data.status == 404)
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Error: 404." )
      else
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Error: " + data.responseText + "." );
      $( "#" + item + "-list-loading" ).hide();
      $( "#" + item + "-list" ).show();
    });
}

function newItem(item) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#form-new-" + item).attr('action'),
    data: $("#form-new-" + item).serialize(),
    dataType: "json",
  })
    .done(function() {
      resettoken(false);
      loadItem(item);
      $("#form-new-" + item + "-submit").prop("disabled",false);
    })
    .fail(function(data) {
      if (data.status == 404)
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Error: 404." )
      else
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Fel: " + data.responseText + "." );
      $("#form-new-" + item + "-submit").prop("disabled",false);
      resettoken(item);
    });
}

function itemChange(item, method) {
  item = $( item ).attr("data").split(",");
  var dataObj = {};
  dataObj["id"] = item[0];
  dataObj["table"] = item[1];
  if (item[2] !== undefined)
    dataObj["direction"] = item[2];
  else
    dataObj["direction"] = "none";
  dataObj["method"] = method;
  dataObj["token"] = $( '.form-token' ).first().val();

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/adminajax/' + method + 'item',
    data: dataObj,
    dataType: "json",
  })
    .done(function() {
        resettoken(false);
        loadItem(item[1]);
    })
    .fail(function(data) {
      resettoken(item[1]);
      $( "#" + item[1] + "-list-error" ).html( "Något har gått fel. Fel: " + data.responseText + "." );

    });

}

function resettoken(item) {
  $.getJSON({
    type: 'POST',
    cache: false,
    url: '/ajax/resettoken',
    dataType: "json",
  })
    .done(function(response) {
      $( '.form-token' ).each(function(){
        $( this ).val(response.token);
      });
      if (item !== false) {
        $( "#" + item + "-list-loading" ).hide();
        $( "#" + item + "-list" ).show();
      }
    })
    .fail(function(data) {
      alert("Fel token, provar ladda om sidan!");
      location.reload(true);
    });
}


function renderItem(item, response) {
  var line = "<table><tbody>";
  jQuery.each(response, function() {
    line += "<tr><th scope='row'>";
    if (item == "category")
      line +=  this.kategori;
    if (item == "roomopt")
      line +=  this.boende;
    if (item == "stop")
      line +=  this.plats;
    line += "</th>";
    if (this.aktiv == "1")
      line += "<td class='aktiv'><a href='#' class='" + item + "item-toggle' data='" + this.id + "," + item + "'>AKTIV</a></td>";
    else
      line += "<td class='inaktiv'><a href='#' class='" + item + "item-toggle' data='" + this.id + "," + item + "'>INAKTIV</a></td>";

    //If we are rendering categories we want order buttons
    if (item == "category" || item == "stop") {
      line += "<td><a href='#' class='" + item + "item-reorder' data='" + this.id + "," + item + ",up'><i class='fa fa-long-arrow-up' aria-hidden='true'></i></a></td>";
      line += "<td><a href='#' class='" + item + "item-reorder' data='" + this.id + "," + item + ",down'><i class='fa fa-long-arrow-down' aria-hidden='true'></i></a></td>";
    }

    line += "<td><a href='#' class='" + item + "item-delete' data='" + this.id + "," + item + "'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>";
  });
  line += "</tbody></table>";
  $( "#" + item + "-list-content" ).html( line );

  //BIND LISTENERS TO NEW LINKS
  $( "." + item + "item-toggle").each(function(){
    $( this ).one("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "toggle");
    });
  });

  $( "." + item + "item-reorder").each(function(){
    $( this ).one("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "reorder");
    });
  });

  $( "." + item + "item-delete").each(function(){
    $( this ).one("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "delete");
    });
  })


  //DISABLE LOAD SCREEN AFTER RENDER
  $( "#" + item + "-list-loading" ).hide();
  $( "#" + item + "-list" ).show();

}
