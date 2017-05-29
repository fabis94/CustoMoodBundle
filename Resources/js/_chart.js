var chart = {
    init: function() {
        var $dataChart = $('#data-chart');
        if ($dataChart.length > 0 && typeof chart_data !== 'undefined') {
            var myLineChart = new Chart($dataChart, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Satisfaction',
                        backgroundColor: "rgb(54, 162, 235)",
                        borderColor: "rgb(54, 162, 235)",
                        fill: false,
                        data: chart_data.data.values,
                        spanGaps: true
                    }],
                    labels: chart_data.data.labels,
                },
                options: {
                    title: {
                        display: true,
                        text: chart_data.title
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Date range'
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Satisfaction evaluation'
                            },
                            ticks: {
                                min: -1.0,
                                max: 1.0
                            }
                        }]
                    }
                }
            });
        }
    }
};