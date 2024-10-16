// Class definition
var KTChartsWidget23 = (function() {
    // Private methods
    var initChart = function(response) {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("kt_charts_widget_23");

        if (!element) {
            return;
        }

        am5.ready(function() {
            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new(element);
            root._logo.dispose();

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(
                am5xy.XYChart.new(root, {
                    panX: false,
                    panY: false,
                    layout: root.verticalLayout,
                })
            );

            // Create axes
            // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var xAxis = chart.xAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "mesAno",
                    renderer: am5xy.AxisRendererX.new(root, {}),
                    //tooltip: am5.Tooltip.new(root, {}),
                })
            );

            for (let i = 0; i < response.data.length; i++) {
                response.data[i] = {
                    ...response.data[i],
                    columnSettings: {
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-primary"))
                    }
                };
            }

            xAxis.data.setAll(response.data);

            xAxis.get("renderer").labels.template.setAll({
                paddingTop: 20,
                fontWeight: "400",
                fontSize: 11,
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
            });

            xAxis.get("renderer").grid.template.setAll({
                disabled: true,
                strokeOpacity: 0,
            });

            var yAxis = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    extraMax: 0.1,
                    renderer: am5xy.AxisRendererY.new(root, {}),
                })
            );

            yAxis.get("renderer").labels.template.setAll({
                paddingTop: 0,
                fontWeight: "400",
                fontSize: 11,
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
            });

            yAxis.get("renderer").grid.template.setAll({
                stroke: am5.color(KTUtil.getCssVariableValue("--bs-gray-300")),
                strokeWidth: 1,
                strokeOpacity: 1,
                strokeDasharray: [3],
            });

            // Add series
            // https://www.amcharts.com/docs/v5/charts/xy-chart/series/

            var series1 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "Valor Pedidos",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "totalPedido",
                    categoryXField: "mesAno",
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "{name} em {categoryX}: {valueY} {info}",
                    }),
                })
            );

            series1.columns.template.setAll({
                tooltipY: am5.percent(10),
                templateField: "columnSettings",
            });

            series1.data.setAll(response.data);

            series1.columns.template.setAll({
                strokeOpacity: 0,
                cornerRadiusBR: 0,
                cornerRadiusTR: 6,
                cornerRadiusBL: 0,
                cornerRadiusTL: 6,
            });

            chart.set("cursor", am5xy.XYCursor.new(root, {}));

            chart.get("cursor").lineX.setAll({
                visible: false
            });
            chart.get("cursor").lineY.setAll({
                visible: false
            });

            // Add legend
            // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50,
                })
            );
            legend.data.setAll(chart.series.values);

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            chart.appear(1000, 100);
            series1.appear();
        }); // end am5.ready()
    };


    var initChartEconomia = function(response) {
        // Check if amchart library is included
        if (typeof am5 === "undefined") {
            return;
        }

        var element = document.getElementById("chart_economia");

        if (!element) {
            return;
        }

        am5.ready(function() {
            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new(element);
            root._logo.dispose();

            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            // https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(
                am5xy.XYChart.new(root, {
                    panX: false,
                    panY: false,
                    layout: root.verticalLayout,
                })
            );

            // Create axes
            // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var xAxis = chart.xAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "mesAno",
                    renderer: am5xy.AxisRendererX.new(root, {}),
                    //tooltip: am5.Tooltip.new(root, {}),
                })
            );

            for (let i = 0; i < response.data.length; i++) {
                response.data[i] = {
                    ...response.data[i],
                    columnSettings: {
                        fill: am5.color(KTUtil.getCssVariableValue("--bs-success"))
                    }
                };
            }

            xAxis.data.setAll(response.data);

            xAxis.get("renderer").labels.template.setAll({
                paddingTop: 20,
                fontWeight: "400",
                fontSize: 11,
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
            });

            xAxis.get("renderer").grid.template.setAll({
                disabled: true,
                strokeOpacity: 0,
            });

            var yAxis = chart.yAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    extraMax: 0.1,
                    renderer: am5xy.AxisRendererY.new(root, {}),
                })
            );

            yAxis.get("renderer").labels.template.setAll({
                paddingTop: 0,
                fontWeight: "400",
                fontSize: 11,
                fill: am5.color(KTUtil.getCssVariableValue("--bs-gray-500")),
            });

            yAxis.get("renderer").grid.template.setAll({
                stroke: am5.color(KTUtil.getCssVariableValue("--bs-gray-300")),
                strokeWidth: 1,
                strokeOpacity: 1,
                strokeDasharray: [3],
            });

            var series1 = chart.series.push(
                am5xy.ColumnSeries.new(root, {
                    name: "Valor Economia",
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: "totalEconomia",
                    fill: am5.color(KTUtil.getCssVariableValue("--bs-success")),
                    stroke: am5.color(KTUtil.getCssVariableValue("--bs-success")),
                    categoryXField: "mesAno",
                    tooltip: am5.Tooltip.new(root, {
                        pointerOrientation: "horizontal",
                        labelText: "{name} em {categoryX}: {valueY} {info}",
                    }),
                })
            );

            series1.columns.template.setAll({
                tooltipY: am5.percent(10),
                templateField: "columnSettings",
            });

            series1.data.setAll(response.data);

            series1.columns.template.setAll({
                strokeOpacity: 0,
                cornerRadiusBR: 0,
                cornerRadiusTR: 6,
                cornerRadiusBL: 0,
                cornerRadiusTL: 6,
            });

            chart.set("cursor", am5xy.XYCursor.new(root, {}));

            chart.get("cursor").lineX.setAll({
                visible: false
            });
            chart.get("cursor").lineY.setAll({
                visible: false
            });

            // Add legend
            // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50,
                })
            );
            legend.data.setAll(chart.series.values);

            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            chart.appear(1000, 100);
            series1.appear();
        }); // end am5.ready()
    };

    // Public methods
    return {
        init: function(response) {
            initChart(response);
            initChartEconomia(response);
        },
    };
})();

// Webpack support
if (typeof module !== "undefined") {
    module.exports = KTChartsWidget23;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    $.ajax({
        type: "GET",
        url: base_url + '/financeiro_report/getEvolucaoPedidos',
        data: {
            de: $("#de").val(),
            ate: $("#ate").val()
        },
        dataType: "json",
        success: function(response) {
            KTChartsWidget23.init(response);
        }
    });
});