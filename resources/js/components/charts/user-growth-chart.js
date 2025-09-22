import ApexCharts from "apexcharts";

const userGrowthData_Data = typeof userGrowthData !== 'undefined' ? userGrowthData : [];
const userGrowthLabels_Labels = typeof userGrowthLabels !== 'undefined' ? userGrowthLabels : [];

// ===== User Growth Chart.
const userGrowthChart = () => {
  console.log('User Growth Chart initializing...', {
    data: userGrowthData_Data,
    labels: userGrowthLabels_Labels
  });
  const chartOneOptions = {
    series: [
      {
        name: "User Registered",
        data: userGrowthData_Data,
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
      categories: userGrowthLabels_Labels, // Dynamically set labels
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
          return val;
        },
      },
    },
  };

  const chartSelector = document.querySelectorAll("#user-growth-chart");
  console.log('Chart selector found:', chartSelector.length, 'elements');

  if (chartSelector.length) {
    console.log('Rendering user growth chart...');
    const chartFour = new ApexCharts(
      document.querySelector("#user-growth-chart"),
      chartOneOptions,
    );
    chartFour.render();
  } else {
    console.log('No #user-growth-chart element found');
  }
};

export default userGrowthChart;
