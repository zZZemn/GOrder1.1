$(document).ready(function () {
  var currentId = 0;

  var isNavOpen = true;
  $("#open-nav").click(function (e) {
    e.preventDefault();
    if (isNavOpen) {
      $(".users-messages-container").css("transform", "translateX(-300px)");
    } else {
      $(".users-messages-container").css("transform", "translateX(0)");
    }
    isNavOpen = !isNavOpen;
  });

  const getUsersContentContainer = () => {
    $.ajax({
      type: "GET",
      url: "server/get-message-global.php",
      data: {
        data: "usersMessageContainer",
      },
      success: function (response) {
        $("#usersMessagesContainer").html(response);
      },
    });
  };

  const getMessages = (id) => {
    $.ajax({
      type: "GET",
      url: "server/get-message-global.php",
      data: {
        data: "usersMessageProfileContainer",
        id: id,
      },
      success: function (response) {
        $("#usersMessageProfileContainer").html(response);
      },
    });

    $.ajax({
      type: "GET",
      url: "server/get-message-global.php",
      data: {
        data: "messageContentContainer",
        id: id,
      },
      success: function (response) {
        $("#messageContentContainer").html(response);
      },
    });
  };

  $(document).on("click", ".btnViewMessage", function (e) {
    e.preventDefault();
    currentId = $(this).attr("data-id");
    getMessages(currentId);
    $(".users-messages-container").css("transform", "translateX(-300px)");
    isNavOpen = !isNavOpen;
  });

  // send message
  $("#sendMessageFrm").submit(function (e) {
    e.preventDefault();
    var message = $("#messageTextTxt").val();
    var messageId = $("#messageId").val();
    var senderId = $("#senderId").val();

    if (messageId == undefined || messageId == "") {
      console.log("You cant send this message.");
    } else {
      $.ajax({
        type: "POST",
        url: "server/get-message-global.php",
        data: {
          data: "sendMessage",
          message: message,
          messageId: messageId,
          senderId: senderId,
        },
        success: function (response) {
          console.log(response);
          getMessages(currentId);
          $("#messageTextTxt").val("");
        },
      });
    }
  });
  // end

  getMessages(currentId);

  setInterval(() => {
    getMessages(currentId);
  }, 2000);

  getUsersContentContainer();
});
