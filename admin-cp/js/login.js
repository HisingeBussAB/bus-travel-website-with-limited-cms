window.addEventListener('load', function() {
  $('#login-form').addEventListener('submit', function(e){
    sendLoginForm(e);
  });
});

function sendLoginForm(event) {
  event.preventDefault();
  $('#login-submit').disabled = true;

  var formData = $("#login-form").serialize();
  var formMessages = $("#login-response");

  $.ajax({
    type: 'POST',
    cache: false,
    url: $("#login-form").attr('action'),
    data: formData,
    statusCode: {
      404:function(){
        $(formMessages).text('Kan inte acceptera inloggning. 404');
      }
    }
  })
    .done(function(response) {
      console.log(response);
      document.location.href=response;

    })
    .fail(function(data) {
      console.log(data);

      //exit prevent doubleclick
      setTimeout(function(){
        document.getElementById("contact-submit").disabled = false;
      }, 2000);

      // Set the message text.
      if (data.responseText !== '') {
          $(formMessages).text(data.responseText);
        } else {
          $(formMessages).text('Okänt fel, går inte att logga in. AJAX failed with empty response');
        };
      });
};
