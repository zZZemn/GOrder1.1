const clicked_messages = document.querySelectorAll(".dropdown-message");
let current_message = null;

const showMessage = () => {
  clicked_messages.forEach((clicked_message) => {
    clicked_message.addEventListener("click", (event) => {
      message_dropdown_container = clicked_message.parentNode;
      event.preventDefault();
      const sender = "message" + clicked_message.classList[1] + "message";
      const senderClass = document.querySelectorAll("." + sender);
      if (current_message !== null && current_message !== clicked_message) {
        const current_sender =
          "message" + current_message.classList[1] + "message";
        const current_senderClass = document.querySelectorAll(
          "." + current_sender
        );
        current_senderClass.forEach((elem) => {
          elem.classList.remove("message-show");
        });
      }
      senderClass.forEach((elem) => {
        elem.classList.toggle("message-show");
      });
      current_message = clicked_message;

      // Scroll to the bottom of the container when a message is shown
      const messageContainer = document.getElementById("message-container");
      messageContainer.scrollTop = messageContainer.scrollHeight;
    });
  });

  const close_button = document.querySelectorAll(".close-message");
  close_button.forEach((closed_message) => {
    closed_message.addEventListener("click", (event) => {
      event.preventDefault();
      if (current_message !== null) {
        const sender = "message" + current_message.classList[1] + "message";
        const senderClass = document.querySelectorAll("." + sender);
        senderClass.forEach((elem) => {
          elem.classList.remove("message-show");
        });
        current_message = null;
      }
    });
  });
};

showMessage();
