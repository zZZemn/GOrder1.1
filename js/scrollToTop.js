// // Scroll to the bottom of the container on page load
// $("#message-container").scrollTop($("#message-container")[0].scrollHeight);
$("#message-container").scrollTop(0);
// Function to scroll to the top of the container and show content from top
function scrollToTop() {
    $("#message-container").scrollTop(0);
}
// Example of adding a new message to the container
function addMessage(sender, messageBody) {
    var newMessage = '<div><article>' + sender + '</article><p>' + messageBody + '</p></div>';
    $("#message-container").append(newMessage); // Use append to add the new message at the bottom
    scrollToTop(); // Scroll to the top after adding a new message
}
