$(document).ready(function () {
  // Function to close the tab or window
  function closeTab() {
    window.location.href = "print.php";
  }

  // Add an event listener for the afterprint event
  window.addEventListener("afterprint", closeTab);

  window.print();
});
