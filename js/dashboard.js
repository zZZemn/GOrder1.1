$(document).ready(function () {
  function loadXMLDoc(containerData) {
    containerData.forEach(function (data) {
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          var responseHTML = document.createElement("div");
          responseHTML.innerHTML = this.responseText;
          var specificElement = responseHTML.querySelector(
            data.elementSelector
          );
          if (specificElement && specificElement instanceof Node) {
            var container = document.getElementById(data.containerId);
            container.innerHTML = ""; // Clear the container's current contents
            container.appendChild(specificElement);
          }
        }
      };

      var url = "../server/dashboard-update.php";
      xhttp.open("GET", url, true);
      xhttp.send();
    });
  }

  var containers = [
    {
      containerId: "f-inv-status-container",
      elementSelector: ".specific-element1",
    },
    {
      containerId: "s-inv-status-container",
      elementSelector: ".specific-element2",
    },
    {
      containerId: "t-inv-status-container",
      elementSelector: ".specific-element3",
    },
    // Add more container IDs, IDs, and element selectors as needed
  ];

  setInterval(function () {
    loadXMLDoc(containers);
  }, 1000);


  // second container
  const getSecondData = () => {
    $.ajax({
      type: "GET",
      url: "../server/dashboard-update-2nd-row.php",
      success: function (response) {
        console.log(response);
        var data = JSON.parse(response);
        $('#cust_tot').text(data.customer[1]);
        $('#freqBItem').text(data.customer[0]);

        $('#ret_items').text(data.product[1]);
        $('#freqRItem').text(data.product[0]);
      },
    });
  };

  getSecondData();

//   setInterval(function () {
//     getSecondData();
//   }, 1000);

  
  //chart
  function chartLoad() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState === 4 && this.status === 200) {
        var salesData = JSON.parse(this.responseText);
        updateChart(salesData);
      }
    };
    xhttp.open("GET", "../server/chart-update.php", true);
    xhttp.send();
  }

  // chart
  function updateChart(salesData) {
    var currentYear = new Date().getFullYear();
    var dataPoints = [];
    for (var month in salesData) {
      var monthIndex = parseInt(month); // Convert month string to integer
      var xValue = new Date(currentYear, monthIndex);
      var yValue = salesData[month];

      var dataPoint = { x: xValue, y: yValue };
      dataPoints.push(dataPoint);
    }

    var options = {
      animationEnabled: true,
      title: {
        text: "Golden Gate Drugstore Total Sales This Year",
      },
      axisY: {
        title: "2023",
        prefix: "₱",
      },
      data: [
        {
          type: "area",
          fillOpacity: 0.5,
          markerSize: 5,
          prefix: "₱",
          xValueFormatString: "M",
          yValueFormatString: "#,##0",
          dataPoints: dataPoints,
        },
      ],
    };
    $("#chartContainer").CanvasJSChart(options);
  }

  window.onload = chartLoad();

  setTimeout(function () {
    $(".dash-board-container").animate({ opacity: 1 }, 500);
    $(".loading-overlay").hide();
  }, 500);
});
