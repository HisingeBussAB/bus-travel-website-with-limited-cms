"use strict";


$(function() {

  //Random background DESIGN TESTER
  var r = Math.floor((Math.random() * 6) + 1);
  $("body:first").addClass("body" + r);
  //Random background DESIGN TESTER


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

});
