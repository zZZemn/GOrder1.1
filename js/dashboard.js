$(document).ready(function () {
    function loadXMLDoc(containerData) {
        containerData.forEach(function (data) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var responseHTML = document.createElement('div');
                    responseHTML.innerHTML = this.responseText;

                    var specificElement = responseHTML.querySelector(data.elementSelector);
                    if (specificElement && specificElement instanceof Node) {
                        var container = document.getElementById(data.containerId);
                        container.innerHTML = ''; // Clear the container's current contents
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
        { containerId: "f-inv-status-container", elementSelector: ".specific-element1" },
        { containerId: "s-inv-status-container", elementSelector: ".specific-element2" },
        { containerId: "t-inv-status-container", elementSelector: ".specific-element3" },
        // Add more container IDs, IDs, and element selectors as needed
    ];

    window.onload = function () {
        loadXMLDoc(containers);
    };

    setInterval(function () {
        loadXMLDoc(containers);
    }, 1000);


    // ----------

    function chartLoad() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            
        };
        xhttp.open("GET", "../server/address-update.php", true);
        xhttp.send();
    }

    window.onload = loadXMLDoc;

    setInterval(function () {
        chartLoad();
    }, 1000);

    // chart
    window.onload = function () {
        var options = {
            animationEnabled: true,
            title: {
                text: "Golden Gate Drugstore Revenue This Year"
            },
            axisY: {
                title: "Revenue in PHP",
                prefix: "₱",
            },
            data: [{
                type: "area",
                fillOpacity: .5,
                markerSize: 5,
                prefix: "₱",
                xValueFormatString: "M",
                yValueFormatString: "#,##0",
                dataPoints: [
                    { x: new Date(2023, 0), y: 222890 },
                    { x: new Date(2023, 1), y: 58300 },
                    { x: new Date(2023, 2), y: 10090 },
                    { x: new Date(2023, 3), y: 18400 },
                    { x: new Date(2023, 4), y: 13960 },
                    { x: new Date(2023, 5), y: 26130 },
                    { x: new Date(2023, 6), y: 18210 },
                    { x: new Date(2023, 7), y: 10000 },
                    { x: new Date(2023, 8), y: 13970 },
                    { x: new Date(2023, 9), y: 15060 },
                    { x: new Date(2023, 10), y: 17980 },
                    { x: new Date(2023, 11), y: 23860 }
                ]
            }]
        };
        $("#chartContainer").CanvasJSChart(options);

    }
});
