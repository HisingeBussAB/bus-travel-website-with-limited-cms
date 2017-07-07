"use strict";


$(function() {
  var r = Math.floor((Math.random() * 6) + 1);

  $("body:first").addClass("body" + r);

});
