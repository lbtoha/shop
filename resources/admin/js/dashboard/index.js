"use strict";
import ApexCharts from "apexcharts";
import $ from "jquery";

$(async function () {
    async function fetchOverviewData(url, filters) {
        try {
            if (filters) {
                url += `?${new URLSearchParams(filters).toString()}`;
            }
            const response = await fetch(url);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Error fetching overview data:", error);
            return null;
        }
    }

    // Daily Login Overview (Last 15 days)
    function LoginOverviewChartOptions(data) {
        return {
            series: data?.series,
            chart: {
                type: "area",
                height: 330,
                width: "100%",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 2,
            },
            markers: {
                size: 4,
                strokeColors: "#404A60",
                shape: "circle",
            },
            legend: {
                show: true,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                type: "datetime",
                categories: data?.labels,
                labels: {
                    format: "dd MMM",
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    color: "#EBECEF",
                },
            },
            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
            tooltip: {
                x: {
                    format: "dd MMM yyyy",
                },
            },
        };
    }
    if (document.getElementById("dailyLoginChart")) {
        setComponentLoading("dailyLoginChart", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/login_log_by_day");
        setComponentLoading("dailyLoginChart", false);
        new ApexCharts(document.getElementById("dailyLoginChart"), LoginOverviewChartOptions(data)).render();
    }

    // Transaction Report Chart
    function transactionChartOptions(data) {
        return {
            series: data?.series,
            chart: {
                width: "100%",
                height: 330,
                type: "area",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: window.innerWidth < 768 ? "6" : window.innerWidth < 1400 ? "10" : "12",
                    dataLabels: {
                        position: "center",
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [0, 1],
                colors: [null, "#ffab00"],
                dashArray: [0, 3],
                curve: ["straight", "smooth"],
            },

            legend: {
                show: true,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                categories: data?.categories || [],
                labels: {},
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    color: "#EBECEF",
                },
            },
            colors: data?.colors,
            fill: {
                colors: data?.colors,
                opacity: Array.from({ length: data?.colors?.length }, () => 1),
            },
            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
        };
    }
    if (document.getElementById("transactionReportChartRef")) {
        renderTransactionChart();
    }
    async function renderTransactionChart(filters = {}) {
        setComponentLoading("transactionReportChartRef", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/transactions", filters);

        setComponentLoading("transactionReportChartRef", false);
        new ApexCharts(document.getElementById("transactionReportChartRef"), transactionChartOptions(data)).render();
    }

    // Withdraw Chart
    function withdrawChartOptions(data) {
        return {
            series: data?.series,
            chart: {
                width: "100%",
                height: 330,
                type: "line",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: window.innerWidth < 768 ? "8" : window.innerWidth < 1400 ? "8" : "14",
                    dataLabels: {
                        position: "center",
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [0, 0, 3],
                colors: [null, null, "#fd5333"],
                dashArray: [0, 0, 0],
                curve: ["straight", "straight", "straight"],
            },
            markers: {
                radius: 50,
                shape: "circle",
                size: 5,
                strokeColors: "#404A60",
            },
            legend: {
                show: false,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                categories: data?.categories || [],
                labels: {},
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    color: "#EBECEF",
                },
            },
            colors: ["#2C7BE5", "#23c55e", "#fd5333"],
            fill: {
                colors: ["#2C7BE5", "#23c55e", "#fd5333"],
                opacity: [1, 1, 1],
            },
            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
        };
    }
    if (document.getElementById("withdrawRef")) {
        setComponentLoading("withdrawRef", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/withdrawals");
        setComponentLoading("withdrawRef", false);
        new ApexCharts(document.getElementById("withdrawRef"), withdrawChartOptions(data)).render();
    }

    // Deposit Chart
    function depositChartOptions(data) {
        return {
            series: data?.series,
            chart: {
                width: "100%",
                height: 330,
                type: "line",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            plotOptions: {
                bar: {
                    columnWidth: window.innerWidth < 768 ? "6" : window.innerWidth < 1600 ? "8" : "12",
                    dataLabels: {
                        position: "center",
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [0, 0, 0, 0, 0],
                colors: [null, null, null, null, null],
                dashArray: [0, 0, 0, 0, 0],
                curve: Array(data?.series?.length).fill("straight"),
            },
            markers: {
                radius: 50,
                shape: "circle",
                size: 5,
                strokeColors: "#404A60",
            },
            legend: {
                show: false,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                categories: data?.categories,
            },
            colors: ["#23c55e", "#fd5333", "#fbbc04", "#00bcd4", "#9c27b0"],
            fill: {
                colors: ["#23c55e", "#fd5333", "#fbbc04", "#00bcd4", "#9c27b0"],
                opacity: [1, 1, 1, 1, 1],
            },
            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
        };
    }
    if (document.getElementById("depositRef")) {
        setComponentLoading("depositRef", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/deposits");
        setComponentLoading("depositRef", false);
        new ApexCharts(document.getElementById("depositRef"), depositChartOptions(data)).render();
    }

    // Contest Participant Chart
    function contestParticipantChartOptions(data) {
        return {
            series: data?.series,
            chart: {
                type: "area",
                height: 330,
                width: "100%",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 2,
            },
            markers: {
                size: 4,
                strokeColors: "#404A60",
                shape: "circle",
            },
            legend: {
                show: true,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                type: "datetime",
                categories: data?.labels.map((date) => new Date(date).getTime()),
                labels: {
                    format: "dd MMM",
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    color: "#EBECEF",
                },
            },

            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
            tooltip: {
                x: {
                    format: "dd MMM yyyy",
                },
            },
            colors: ["#23c55e", "#fd5333", "#fbbc04", "#00bcd4", "#9c27b0", "#3f51b5", "#ff5722"],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100],
                },
            },
        };
    }
    if (document.getElementById("contestParticipantChart")) {
        setComponentLoading("contestParticipantChart", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/contest_participants");
        setComponentLoading("contestParticipantChart", false);
        new ApexCharts(document.getElementById("contestParticipantChart"), contestParticipantChartOptions(data)).render();
    }

    // Quiz Participant Chart
    function quizParticipantChartOptions(data) {
        return {
            series: [
                {
                    name: "Participants",
                    data: data?.series || [],
                },
            ],
            chart: {
                type: "area",
                height: 330,
                width: "100%",
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 800,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                curve: "smooth",
                width: 2,
            },
            markers: {
                size: 4,
                strokeColors: "#404A60",
                shape: "circle",
            },
            legend: {
                show: true,
                offsetY: 10,
                markers: {
                    width: 6,
                    height: 6,
                    offsetX: -4,
                },
            },
            xaxis: {
                type: "datetime",
                categories: data?.labels,
                labels: {
                    format: "dd MMM",
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    color: "#EBECEF",
                },
            },
            responsive: [
                {
                    breakpoint: 570,
                    options: {
                        chart: {
                            height: 250,
                        },
                    },
                },
            ],
            grid: {
                borderColor: "#EBECEF",
                padding: {
                    left: -20,
                },
            },
            tooltip: {
                x: {
                    format: "dd MMM yyyy",
                },
            },
            colors: ["#23c55e"], // single series color
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100],
                },
            },
        };
    }

    if (document.getElementById("quizParticipantChart")) {
        setComponentLoading("quizParticipantChart", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/quiz_participants");
        setComponentLoading("quizParticipantChart", false);
        new ApexCharts(document.getElementById("quizParticipantChart"), quizParticipantChartOptions(data)).render();
    }

    // OS Chart
    function osChartOptions(data) {
        return {
            series: data?.series?.map((s) => Number(s)),
            labels: data.labels,
            chart: {
                type: "donut",
                height: 280,
                width: "100%",
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 90,
                    offsetY: 10,
                    donut: {
                        size: "80%",
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: "22px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 600,
                                color: undefined,
                                offsetY: -10,
                                formatter: function (val) {
                                    return val;
                                },
                            },
                            value: {
                                show: true,
                                fontSize: "16px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 400,
                                color: undefined,
                                offsetY: -50,
                                formatter: function (val) {
                                    return val;
                                },
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: "Total",
                                fontSize: "22px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 600,
                                color: "#373d3f",
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b;
                                    }, 0);
                                },
                            },
                        },
                    },
                },
            },
            stroke: {
                width: 1,
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.globals.labels[opts.seriesIndex];
                },
            },
            legend: {
                show: true,
                position: "bottom",
                horizontalAlign: "center",
                fontSize: "14px",
                fontFamily: "Helvetica, Arial, sans-serif",
                fontWeight: 400,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 5,
                    offsetX: -4,
                },
            },
            grid: {
                padding: {
                    bottom: -80,
                },
            },
            fill: {
                colors: data?.colors,
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: "bottom",
                        },
                    },
                },
            ],
        };
    }
    if (document.getElementById("osChartRef")) {
        setComponentLoading("osChartRef", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/login_log");
        setComponentLoading("osChartRef", false);
        new ApexCharts(document.getElementById("osChartRef"), osChartOptions(data)).render();
    }

    // OS Chart
    function browserChartOptions(data) {
        return {
            series: data?.series?.map((s) => Number(s)),
            labels: data.labels,
            chart: {
                type: "donut",
                height: 280,
                width: "100%",
            },
            plotOptions: {
                pie: {
                    startAngle: -90,
                    endAngle: 90,
                    offsetY: 10,
                    donut: {
                        size: "80%",
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: "22px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 600,
                                color: undefined,
                                offsetY: -10,
                                formatter: function (val) {
                                    return val;
                                },
                            },
                            value: {
                                show: true,
                                fontSize: "16px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 400,
                                color: undefined,
                                offsetY: -50,
                                formatter: function (val) {
                                    return val;
                                },
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: "Total",
                                fontSize: "22px",
                                fontFamily: "Helvetica, Arial, sans-serif",
                                fontWeight: 600,
                                color: "#373d3f",
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => {
                                        return a + b;
                                    }, 0);
                                },
                            },
                        },
                    },
                },
            },
            stroke: {
                width: 1,
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opts) {
                    return opts.w.globals.labels[opts.seriesIndex];
                },
            },
            legend: {
                show: true,
                position: "bottom",
                horizontalAlign: "center",
                fontSize: "14px",
                fontFamily: "Helvetica, Arial, sans-serif",
                fontWeight: 400,
                markers: {
                    width: 10,
                    height: 10,
                    radius: 5,
                    offsetX: -4,
                },
            },
            grid: {
                padding: {
                    bottom: -80,
                },
            },
            fill: {
                colors: data?.colors,
            },
            responsive: [
                {
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: "bottom",
                        },
                    },
                },
            ],
        };
    }
    if (document.getElementById("browserChartRef")) {
        setComponentLoading("browserChartRef", true);
        const data = await fetchOverviewData("/admin/dashboard/overview/login_log_browser");
        setComponentLoading("browserChartRef", false);
        new ApexCharts(document.getElementById("browserChartRef"), browserChartOptions(data)).render();
    }

    // Transaction Report Period Change
    $("#transaction_report_period").on("change", function () {
        const period = $(this).val();
        renderTransactionChart({ overview_by: period });
    });

    // Set Component Loading
    function setComponentLoading(id, isSetting = true) {
        if (isSetting) {
            $(`#${id}`).html(`<div class="flex items-center justify-center h-[300px]">
                <span class="flex items-center gap-2">
                    <i class="ph ph-spinner gap-2 animate-spin text-slate-40  text-3xl"></i>
                    <span class="text-slate-40  text-lg font-semibold">Loading...</span>
                </span>
            </div>`);
        } else {
            $(`#${id}`).html("");
        }
    }
});
