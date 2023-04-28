$("#send-message").on("submit", function(event) {
  event.preventDefault(); // Prevent form submission

  // Get form data
  var senderId = $("input[name='sender_id']").val();
  var messageId = $("input[name='message_id']").val();
  var message = $("input[name='message']").val();

  // Create AJAX request
  $.ajax({
    url: "../ajax-url/send-message.php", // Specify the URL of your PHP script
    type: "POST",
    data: {
      sender_id: senderId,
      message_id: messageId,
      message: message
    },
    dataType: "text",
    success: function(response) {
      // Handle the response from the server, if needed
      console.log(response);
      var newMessage = '<div><article>GOrder</article><p>' +
                    message + '</p></div>';
      // Append the new message to the message container
      $("#message-container").append(newMessage);
      // Scroll to the top of messageContainer
      const messageContainer = document.getElementById('message-container');
      messageContainer.scrollTop = messageContainer.scrollHeight;
      // Clear input text
      $("input[name='message']").val('');
    },
    error: function(xhr, status, error) {
      // Handle errors, if needed
      console.error(xhr.responseText);
    }
  });
});
