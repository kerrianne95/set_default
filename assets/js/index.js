var tempCtx = document.getElementById("temp-chart").getContext('2d');
var tempChart = new Chart(tempCtx, {
    type: 'line',
    data: {
        labels: ["0000", "0100", "0200", "0300", "0400", "0500", "0600", "0700",
                 "0800", "0900", "1000", "1100", "1200", "1300", "1400", "1500",
                 "1600", "1700", "1800", "1900", "2000", "2100", "2200", "2300"],
        datasets: [{
            label: 'Average Temperature by Hour',
            data: temperatureData,
            borderColor:
              'rgba(255, 159, 64, 1)',
            fill: false
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        },
        responsive: false
    }
});

var soundCtx = document.getElementById("sound-chart").getContext('2d');
var soundChart = new Chart(soundCtx, {
    type: 'line',
    data: {
        labels: ["0000", "0100", "0200", "0300", "0400", "0500", "0600", "0700",
                 "0800", "0900", "1000", "1100", "1200", "1300", "1400", "1500",
                 "1600", "1700", "1800", "1900", "2000", "2100", "2200", "2300"],
        datasets: [{
            label: 'Average Sound by Hour',
            data: soundData,
            borderColor:
                'rgba(255,99,132,1)',
            fill: false
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        },
        responsive: false
    }
});

function update(jscolor) {
  document.getElementById("color").value = jscolor;
}

function apply() {
  document.getElementById("smt").click();
}

// function updateDefault(jscolor, collection) {
//    <?php
//        $db = connectMongo();
//        $collect = $db->collection;
//        $collect->insert(jscolor)
//    ?>
// }