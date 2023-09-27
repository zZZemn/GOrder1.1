// Use event delegation to handle form submissions for all "send-message" forms
$(document).on("submit", ".send-message-form", function (event) {
  event.preventDefault(); // Prevent form submission

  // Get form data specific to the submitted form
  var $form = $(this);
  var senderId = $form.find("input[name='sender_id']").val();
  var messageId = $form.find("input[name='message_id']").val();
  var message = $form.find("input[name='message']").val();

  // Find the closest ".message-content" parent of the form
  var $messageContent = $form.closest(".message-content");

  // Find the specific ".message-text" container within the message content
  var $messageContainer = $messageContent.find(".message-text");

  // Create AJAX request
  $.ajax({
    url: "../ajax-url/send-message.php", // Specify the URL of your PHP script
    type: "POST",
    data: {
      sender_id: senderId,
      message_id: messageId,
      message: message,
    },
    dataType: "text",
    success: function (response) {
      // Handle the response from the server, if needed
      console.log(response);
      var newMessage =
        "<div><article>GOrder</article><p>" + message + "</p></div>";
      // Append the new message to the message container within the current form
      $messageContainer.append(newMessage);
      // Scroll to the top of messageContainer
      $messageContainer.scrollTop($messageContainer[0].scrollHeight);
      // Clear input text within the current form
      $form.find("input[name='message']").val("");
    },
    error: function (xhr, status, error) {
      // Handle errors, if needed
      console.error(xhr.responseText);
    },
  });
});
