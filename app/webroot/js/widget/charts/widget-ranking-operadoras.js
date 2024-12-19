"use strict";

// Class definition
var KTChartsWidget6 = function() {
    // Private methods
    var initChart = function(response) {
        var element = document.getElementById("kt_charts_widget_6");

        if (!element) {
            return;
        }

        var labelColor = KTUtil.getCssVariableValue('--bs-gray-800');
        var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
        var maxValue = 18;

        var options = {
            series: response.data,
            labels: response.header,
            chart: {
                fontFamily: 'inherit',
                type: 'donut',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: { // Docs: https://apexcharts.com/docs/options/datalabels/
                enabled: true,
                textAnchor: 'start',
                offsetX: 0,
                formatter: function(val, opts) {
                    // var val = val * 1000;
                    var Format = wNumb({
                        //prefix: '$',
                        suffix: '%',
                        thousand: '.',
                    });

                    return Format.to(val);
                },
                style: {
                    fontSize: '14px',
                    fontWeight: '600',
                    align: 'left',
                }
            },
            colors: ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA'],
            tooltip: {
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function(val) {
                        var Format = wNumb({
                            prefix: 'R$ ',
                            thousand: '.',
                            mark: ','
                        });

                        return Format.to(val);
                    }
                }
            }
        };

        var chart = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.render();
        }, 200);
    }

    // Public methods
    return {
        init: function(response) {
            initChart(response);
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTChartsWidget6;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    $.ajax({
        type: "GET",
        url: base_url + '/financeiro_report/getRankingOperadoras',
        data: {
            de: $("#de").val(),
            ate: $("#ate").val()
        },
        dataType: "json",
        success: function(response) {
            KTChartsWidget6.init(response);
        }
    });
});