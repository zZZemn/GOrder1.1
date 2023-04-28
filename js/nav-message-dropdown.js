var messageDropdown = document.getElementsByClassName("message-dropdown");

var avatarDropdown = document.getElementsByClassName("avatar-dropdown");
var avatarDropdownContainer = document.getElementsByClassName(
    "avatar-dropdown-container"
);

var notificationDropdown = document.getElementsByClassName(
    "notification-dropdown"
);
var notificationDropdownContainer = document.getElementsByClassName(
    "notification-dropdown-container"
);

var i;

for (i = 0; i < messageDropdown.length; i++) {
    messageDropdown[i].addEventListener("click", function () {

        var dropdownContent = this.nextElementSibling;

        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            this
                .classList
                .remove("message-active")

        } else {
            dropdownContent.style.display = "block";
            this
                .classList
                .add("message-active")

            avatarDropdownContainer[0].style.display = "none";
            notificationDropdownContainer[0].style.display = "none";

            avatarDropdown[0]
                .classList
                .remove("avatar-active");
            notificationDropdown[0]
                .classList
                .remove("notification-active");
        }
    });
}

const messageDropdownContainers = document.querySelector('.message-dropdown-container');
const messageLinks = messageDropdownContainers.querySelectorAll('a');

messageLinks.forEach(link => {
  link.addEventListener('click', () => {
    messageDropdownContainers.style.display = 'none';
    const messageDropdown = document.querySelector('.message-dropdown');
    messageDropdown.classList.remove("message-active");
  });
});


document.addEventListener('click', (event) => {
    // check if the click event target is outside of the nav element
    const messageDropdown = document.querySelector('.message-dropdown');
    if (!messageDropdownContainers.contains(event.target) && !messageDropdown.contains(event.target)) {
        messageDropdownContainers.style.display = 'none';
        const messageDropdown = document.querySelector('.message-dropdown');
        messageDropdown.classList.remove("message-active");
    }
  });