<div class="w-full bg-white border border-gray-200 dark:border-gray-700 rounded-md shadow-sm dark:bg-gray-800 p-4 ">
    <div class="flex justify-between">
        <div class="flex justify-center items-center">
            <h5 class="text-lg font-semibold leading-none text-gray-700 dark:text-white pe-1">
                {{ __('Active Workload') }}
            </h5>
        </div>
    </div>

    <div class="" id="donut-chart"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const workloadLabels = @json($workload_chart_data['labels'] ?? []);
            const workloadData = @json($workload_chart_data['data'] ?? []);

            const getChartOptions = () => {
                return {
                    series: workloadData,
                    colors: ["#4f46e5", "#0ea5e9", "#f59e0b", "#10b981", "#8b5cf6", "#ef4444"],
                    chart: {
                        height: 320,
                        width: "100%",
                        type: "donut",
                    },
                    stroke: {
                        colors: ["transparent"],
                        lineCap: "",
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontFamily: "var(--font-sans)",
                                        offsetY: 20,
                                    },
                                    total: {
                                        showAlways: true,
                                        show: true,
                                        fontFamily: "var(--font-sans)",
                                        label: "{{ __('Total') }}",
                                        formatter: function(w) {
                                            const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                                            return sum + " {{ __('items') }}"
                                        },
                                    },
                                    value: {
                                        show: true,
                                        fontFamily: "var(--font-sans)",
                                        offsetY: -20,
                                        formatter: function(value) {
                                            return value + " {{ __('items') }}"
                                        },
                                    },
                                },
                                size: "80%",
                            },
                        },
                    },
                    grid: {
                        padding: {
                            top: -2,
                        },
                    },
                    labels: workloadLabels,
                    dataLabels: {
                        enabled: false,
                    },
                    legend: {
                        position: "bottom",
                        fontFamily: "var(--font-sans)",
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return value + " items"
                            },
                        },
                    },
                    xaxis: {
                        labels: {
                            formatter: function(value) {
                                return value + " items"
                            },
                        },
                        axisTicks: {
                            show: false,
                        },
                        axisBorder: {
                            show: false,
                        },
                    },
                }
            }

            if (document.getElementById("donut-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("donut-chart"), getChartOptions());
                chart.render();
            }
        });
    </script>
</div>
