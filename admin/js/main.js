"use strict";

$(function() {


  // Listeners
  $('#form-new-category').submit(function(event){
    event.preventDefault();
    $( "#category-list" ).hide();
    $( "#category-list-loading" ).show();
    $("#form-new-category-submit").prop("disabled",true);
    newCategory();
    $("#form-new-category-name").val('');
  });



  //Load content

  loadItems("category");
  loadItems("roomopt");
  loadItems("stop");


});


function loadItems(item) {
  $.getJSON({
    type: 'POST',
    cache: false,
    url: '/adminajax/get' + item,
    dataType: "json",
    statusCode: {
      404:function(){
        $( "#" + item + "-list" ).append( "<li>Något har gått fel. Error: 404.</li>" );
        $( "#" + item + "-list-loading" ).hide();
        $( "#" + item + "-list" ).show();
        console.log("FLAG");
      }
    }
  })
    .done(function(response) {
      if (response.length > 0) {
        renderItems(item, response);
      } else {
        $( "#" + item + "-list-loading" ).hide();
        $( "#" + item + "-list" ).show();
      }
    })
    .fail(function(data) {
      $( "#" + item + "-list" ).append( "<li>Något har gått fel. Error: " + data.responseText + ".</li>" );
      $( "#" + item + "-list-loading" ).hide();
      $( "#" + item + "-list" ).show();
    });
}

function newCategory() {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#form-new-category").attr('action'),
    data: $("#form-new-category").serialize(),
    dataType: "json",
    statusCode: {
      404:function(){
        $( "#category-list" ).append( "<li>Något har gått fel. Error: 404.</li>" );
        $("#form-new-category-submit").prop("disabled",false);
        $( "#category-list-loading" ).hide();
        $( "#category-list" ).show();
      }
    }
  })
    .done(function() {
      loadItems("category");
      $("#form-new-category-submit").prop("disabled",false);
    })
    .fail(function(data) {
      $( "#categories-list" ).append( "<li>Något har gått fel. Error: " + data.responseText + ".</li>" );
      $("#form-new-category-submit").prop("disabled",false);
      $( "#category-list-loading" ).hide();
      $( "#category-list" ).show();
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

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/adminajax/' + method + 'item',
    data: dataObj,
    dataType: "json",
  })
    .done(function() {
        loadItems(item[1]);


    })
    .fail(function(data) {

      $( "#" + item[1] + "-list" ).append( "<li>Något har gått fel. Error: " + data.responseText + ".</li>" );
      $( "#" + item[1] + "-list-loading" ).hide();
      $( "#" + item[1] + "-list" ).show();
    });

}

function renderItems(item, response) {
  $('#' + item + '-list li:not(:first)').remove();
  var line = "<li><table><tbody>";
  jQuery.each(response, function() {
    line += "<tr><th scope='row'>";
    if (item == "category")
      line +=  this.kategori;
    if (item == "roomopt")
      line +=  this.namn;
    if (item == "stops")
      line +=  this.plats + ", " + this.ort;
    line += "</th>";
    if (this.aktiv == "1")
      line += "<td class='aktiv'><a href='#' class='item-toggle' data='" + this.id + "," + item + "'>AKTIV</a></td>";
    else
      line += "<td class='inaktiv'><a href='#' class='item-toggle' data='" + this.id + "," + item + "'>INAKTIV</a></td>";

    //If we are rendering categories we want order buttons
    if (item == "category") {
      line += "<td><a href='#' class='item-reorder' data='" + this.id + "," + item + ",up'><i class='fa fa-long-arrow-up' aria-hidden='true'></i></a></td>";
      line += "<td><a href='#' class='item-reorder' data='" + this.id + "," + item + ",down'><i class='fa fa-long-arrow-down' aria-hidden='true'></i></a></td>";
    }

    line += "<td><a href='#' class='item-delete' data='" + this.id + "," + item + "'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>";
  });
  line += "</tbody></table></li>";
  $( "#" + item + "-list" ).append( line );

  //BIND LISTENERS TO NEW LINKS
  $( ".item-toggle").each(function(){
    $( this ).one("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "toggle");
    });
  });

  $( ".item-reorder").each(function(){
    $( this ).one("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "reorder");
    });
  });

  $( ".item-delete").each(function(){
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
