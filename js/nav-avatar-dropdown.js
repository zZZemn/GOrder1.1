var avatarDropdown = document.getElementsByClassName("avatar-dropdown");
var i;

var notificationDropdown = document.getElementsByClassName(
    "notification-dropdown"
);
var notificationDropdownContainer = document.getElementsByClassName(
    "notification-dropdown-container"
);

var messageDropdown = document.getElementsByClassName("message-dropdown");
var messageDropdownContainer = document.getElementsByClassName(
    "message-dropdown-container"
);

for (i = 0; i < avatarDropdown.length; i++) {
    avatarDropdown[i].addEventListener("click", function () {
        var dropdownContent = this.nextElementSibling;

        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            this
                .classList
                .remove("avatar-active");
        } else {
            dropdownContent.style.display = "block";
            notificationDropdownContainer[0].style.display = "none";
            this
                .classList
                .add("avatar-active");
            notificationDropdown[0]
                .classList
                .remove("notification-active");

            messageDropdownContainer[0].style.display = "none";
            messageDropdown[0]
                .classList
                .remove("message-active");
        }
    });
}

const avatarDropdownContainers = document.querySelector('.avatar-dropdown-container');

document.addEventListener('click', (event) => {
    // check if the click event target is outside of the nav element
    const avatarDropdown = document.querySelector('.avatar-dropdown');
    if (!avatarDropdownContainers.contains(event.target) && !avatarDropdown.contains(event.target)) {
        avatarDropdownContainers.style.display = 'none';
        avatarDropdown.classList.remove("avatar-active");
    }
  });