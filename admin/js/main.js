"use strict";

$(function() {


  // Listeners sort
  $('#sort-stop-name').click(function(event){
    event.preventDefault();
    loadItem("stop","plats");
    });
  $('#sort-stop-ort').click(function(event){
    event.preventDefault();
    loadItem("stop","ort");
    });

  // Listeners post buttons
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
    $("#form-new-stop-ort").val('');
  });

  $('#form-news').submit(function(event){
    event.preventDefault();
    $( "#form-news-submit-text" ).hide();
    $( "#form-news-submit-spinner" ).show();
    $("#form-news-submit").prop("disabled",true);
    var formdata = $("#form-news").serialize();
    $("#form-news").prop("disabled",true);
    newsUpdate(formdata);
  });

  //Load content
  loadItem("trip");
  loadItem("category");
  loadItem("roomopt");
  loadItem("stop");


});

function newsUpdate(formdata) {
  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#form-news").attr('action'),
    data: formdata,
    dataType: "json",
  })
    .done(function(data) {
      console.log(data);
      $( "#form-news-submit-spinner" ).hide();
      $( "#form-news-submit-text" ).show();
      $("#form-news-submit").prop("disabled",false);
      $("#form-news").prop("disabled",false);
      $( "#form-news-reply" ).html( data );
    })
    .fail(function(data) {
      console.log(data);
      if (data.status == 404)
        $( "#form-news-reply" ).html( "Något har gått fel. Error: 404." )
      else
        $( "#form-news-reply" ).html( "Något har gått fel. Fel: " + data.responseText + "." );
        $( "#form-news-submit-spinner" ).hide();
        $( "#form-news-submit-text" ).show();
        $("#form-news-submit").prop("disabled",false);
        $("#form-news").prop("disabled",false);
    });
}


function loadItem(item, sort = "sort") {
  $.getJSON({
    type: 'POST',
    cache: false,
    data: { 'sort' : sort },
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
      loadItem(item);
      $("#form-new-" + item + "-submit").prop("disabled",false);
    })
    .fail(function(data) {
      if (data.status == 404)
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Error: 404." )
      else
        $( "#" + item + "-list-error" ).html( "Något har gått fel. Fel: " + data.responseText + "." );
      $("#form-new-" + item + "-submit").prop("disabled",false);
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
  dataObj["tokenid"] = $( '.form-token-id' ).first().val();
  dataObj["token"] = $( '.form-token' ).first().val();

  $.ajax({
    type: 'POST',
    cache: false,
    url: '/adminajax/' + method + 'item',
    data: dataObj,
    dataType: "json",
  })
    .done(function() {
        loadItem(item[1]);
    })
    .fail(function(data) {
      $( "#" + item[1] + "-list-error" ).html( "Något har gått fel. Fel: " + data.responseText );
      $( "#" + item[1] + "-list-loading" ).hide();
      $( "#" + item[1] + "-list" ).show();
    });

}

function renderItem(item, response) {
  var line = "<table><tbody>";
  jQuery.each(response, function() {
    line += "<tr><td class='table-name'>";
    if (item == "category") {
      line += "<a href='" + window.location.href + "/kategori/";
      line += this.id;
      line += "'>";
      line += this.kategori;
      line += "</a>";
    }
    if (item == "roomopt")
      line += this.boende;
    if (item == "stop") {
      line += this.plats;
      line += ", ";
      line += this.ort;
    }
    if (item == "trip") {
      line += "<a href='" + window.location.href + "/nyresa/";
      line += this.id;
      line += "'>";
      line += this.namn;
      line += "</a></td><td>"
      line += this.datum;
    }
    line += "</td>";
    //If we are rendering categories we want order buttons
    if (item == "category" || item == "stop") {
      line += "<td class='table-reorder-up'><a href='#' class='" + item + "item-reorder' data='" + this.id + "," + item + ",up' title='Flytta uppåt'><i class='fa fa-long-arrow-up' aria-hidden='true'></i></a></td>";
      line += "<td class='table-reorder-up'><a href='#' class='" + item + "item-reorder' data='" + this.id + "," + item + ",down' title='Flytta nedåt'><i class='fa fa-long-arrow-down' aria-hidden='true'></i></a></td>";
    }

    if (this.aktiv == "1")
      line += "<td class='table-state-active'><a href='#' class='" + item + "item-toggle' data='" + this.id + "," + item + "' title='Inaktivera alternativet'><i class='fa fa-check-square-o' aria-hidden='true'></i></a></td>";
    else
      line += "<td class='table-state-inactive'><a href='#' class='" + item + "item-toggle' data='" + this.id + "," + item + "' title='Aktivera alternativet'><i class='fa fa-square-o' aria-hidden='true'></i></a></td>";

    line += "<td class='table-delete'><a href='#' class='" + item + "item-delete' data='" + this.id + "," + item + "' title='Ta bort permanent'><i class='fa fa-trash-o' aria-hidden='true'></i></a></td></tr>";
  });
  line += "</tbody></table>";
  $( "#" + item + "-list-content" ).html( line );

  //BIND LISTENERS TO NEW LINKS
  $( "." + item + "item-toggle").each(function(){
    $( this ).on("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "toggle");
    });
  });

  $( "." + item + "item-reorder").each(function(){
    $( this ).on("click", function(event){
      event.preventDefault();
      $( "#" + item + "-list" ).hide();
      $( "#" + item + "-list-loading" ).show();
      itemChange(this, "reorder");
    });
  });

  $( "." + item + "item-delete").each(function(){
    $( this ).on("click", function(event){
      event.preventDefault();
      event.target.offsetParent.parentNode.style.backgroundColor = "red";
      //.style.backgroundColor = "red";
      var me = this;
      $.confirm({
        title: 'Radera',
        content: 'Är du säker på att du vill radera posten permanent?',
        buttons: {
          ja: function() {
            //console.log(this);
            event.target.offsetParent.parentNode.style.backgroundColor = "transparent";
            $( "#" + item + "-list" ).hide();
            $( "#" + item + "-list-loading" ).show();
            itemChange(me, "delete");
          },
          nej: function() {
            event.target.offsetParent.parentNode.style.backgroundColor = "transparent";
          }

        }
      })
      //if (confirm("Är du säker på att du vill radera?")) {




    });
  })


  //DISABLE LOAD SCREENS AFTER RENDER
  $( "#" + item + "-list-loading" ).hide();
  $( "#" + item + "-list" ).show();

}
