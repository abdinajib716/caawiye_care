import ApexCharts from "apexcharts";

const dashboardBarChartData_Data = typeof dashboardBarChartData !== 'undefined' ? dashboardBarChartData : [];
const dashboardBarChartLabels_Labels = typeof dashboardBarChartLabels !== 'undefined' ? dashboardBarChartLabels : [];
const dashboardBarChartSeries_Name = typeof dashboardBarChartSeriesName !== 'undefined' ? dashboardBarChartSeriesName : 'Series';

const userGrowthChart = () => {
  const chartOneOptions = {
    series: [
      {
        name: dashboardBarChartSeries_Name,
        data: dashboardBarChartData_Data,
      },
    ],
    colors: ["#465fff"],
    chart: {
      fontFamily: "Outfit, sans-serif",
      type: "bar",
      height: 180,
      toolbar: {
        show: false,
      },
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: "39%",
        borderRadius: 5,
        borderRadiusApplication: "end",
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 4,
      colors: ["transparent"],
    },
    xaxis: {
      categories: dashboardBarChartLabels_Labels,
      axisBorder: {
        show: false,
      },
      axisTicks: {
        show: false,
      },
    },
    legend: {
      show: true,
      position: "top",
      horizontalAlign: "left",
      fontFamily: "Outfit",

      markers: {
        radius: 99,
      },
    },
    yaxis: {
      title: false,
    },
    grid: {
      yaxis: {
        lines: {
          show: true,
        },
      },
    },
    fill: {
      opacity: 1,
    },

    tooltip: {
      x: {
        show: false,
      },
      y: {
        formatter: function (val) {
          return `$${Number(val).toFixed(2)}`;
        },
      },
    },
  };

  const chartSelector = document.querySelectorAll("#user-growth-chart");

  if (chartSelector.length) {
    const chartFour = new ApexCharts(
      document.querySelector("#user-growth-chart"),
      chartOneOptions,
    );
    chartFour.render();
  }
};

export default userGrowthChart;
