// Class definition
var KTChartsWidget23 = (function () {
  // Private methods
  var initChart = function () {
    // Check if amchart library is included
    if (typeof am5 === "undefined") {
      return;
    }

    var element = document.getElementById("kt_charts_widget_23");

    if (!element) {
      return;
    }

    am5.ready(function () {
      // Create root element
      // https://www.amcharts.com/docs/v5/getting-started/#Root_element
      var root = am5.Root.new(element);

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

      var data = [
        {
          year: "01",
          income: 100.5,
          expenses: 50,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "02",
          income: 120.2,
          expenses: 100,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "03",
          income: 130.1,
          expenses: 100,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "04",
          income: 128.5,
          expenses: 97,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "05",
          income: 130.6,
          expenses: 100,
          strokeSettings: {
            strokeWidth: 3,
            strokeDasharray: [5, 5],
          },
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "06",
          income: 180.6,
          expenses: 150,
          strokeSettings: {
            strokeWidth: 3,
            strokeDasharray: [5, 5],
          },
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "07",
          income: 168.1,
          expenses: 130,
          strokeSettings: {
            strokeWidth: 3,
            strokeDasharray: [5, 5],
          },
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "08",
          income: 150.2,
          expenses: 120,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "09",
          income: 200.2,
          expenses: 190,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "10",
          income: 195.2,
          expenses: 185,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "11",
          income: 196.2,
          expenses: 188,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
        {
          year: "12",
          income: 180.2,
          expenses: 178,
          columnSettings: {
            fill: am5.color(KTUtil.getCssVariableValue("--bs-primary")),
          },
        },
      ];

      // Create axes
      // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
      var xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
          categoryField: "year",
          renderer: am5xy.AxisRendererX.new(root, {}),
          //tooltip: am5.Tooltip.new(root, {}),
        })
      );

      xAxis.data.setAll(data);

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
          valueYField: "income",
          categoryXField: "year",
          tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "{name} in {categoryX}: {valueY} {info}",
          }),
        })
      );

      series1.columns.template.setAll({
        tooltipY: am5.percent(10),
        templateField: "columnSettings",
      });

      series1.data.setAll(data);

      var series2 = chart.series.push(
        am5xy.LineSeries.new(root, {
          name: "Qtde Beneficiários",
          xAxis: xAxis,
          yAxis: yAxis,
          valueYField: "expenses",
          categoryXField: "year",
          fill: am5.color(KTUtil.getCssVariableValue("--bs-success")),
          stroke: am5.color(KTUtil.getCssVariableValue("--bs-success")),
          tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "{name} in {categoryX}: {valueY} {info}",
          }),
        })
      );

      series2.strokes.template.setAll({
        stroke: am5.color(KTUtil.getCssVariableValue("--bs-success")),
      });

      series2.strokes.template.setAll({
        strokeWidth: 3,
        templateField: "strokeSettings",
      });

      series2.data.setAll(data);

      series2.bullets.push(function () {
        return am5.Bullet.new(root, {
          sprite: am5.Circle.new(root, {
            strokeWidth: 3,
            stroke: am5.color(KTUtil.getCssVariableValue("--bs-success")),
            radius: 5,
            fill: am5.color(KTUtil.getCssVariableValue("--bs-light-success")),
          }),
        });
      });

      series1.columns.template.setAll({
        strokeOpacity: 0,
        cornerRadiusBR: 0,
        cornerRadiusTR: 6,
        cornerRadiusBL: 0,
        cornerRadiusTL: 6,
      });

      chart.set("cursor", am5xy.XYCursor.new(root, {}));

      chart.get("cursor").lineX.setAll({ visible: false });
      chart.get("cursor").lineY.setAll({ visible: false });

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
    init: function () {
      initChart();
    },
  };
})();

// Webpack support
if (typeof module !== "undefined") {
  module.exports = KTChartsWidget23;
}

// On document ready
KTUtil.onDOMContentLoaded(function () {
  KTChartsWidget23.init();
});
