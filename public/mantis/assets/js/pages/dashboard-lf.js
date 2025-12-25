'use strict';
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        floatchart();
    }, 500);
});

function floatchart() {
    (function () {
        // Data dari controller yang di-pass ke JavaScript
        const chartData = window.chartData || {
            pendapatan_shopee: [1342319476,0,0,0,0,0,0,0,0,0,0,0],
            pendapatan_tiktok: [0,0,0,0,0,0,0,0,0,0,0,0],
            penghasilan_shopee: [0,0,0,0,0,0,0,0,0,0,0,0],
            penghasilan_tiktok: [0,0,0,0,0,0,0,0,0,0,0,0],
            bulan_labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        };

        // Chart untuk Pendapatan
        var options = {
            chart: {
                height: 450,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#1890ff', '#13c2c2'],
            series: [{
                name: 'Shopee',
                data: chartData.pendapatan_shopee
            }, {
                name: 'Tiktok',
                data: chartData.pendapatan_tiktok
            }],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: chartData.bulan_labels,
            },
            yaxis: {
                title: {
                    text: 'Juta Rupiah'
                },
                labels: {
                    formatter: function(value) {
                        return value.toFixed(2);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID', {minimumFractionDigits: 2}) + ' juta';
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector('#visitor-chart-1'), options);
        chart.render();

        // Chart untuk Penghasilan
        var options1 = {
            chart: {
                height: 450,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#1890ff', '#13c2c2'],
            series: [{
                name: 'Shopee',
                data: chartData.penghasilan_shopee
            }, {
                name: 'Tiktok',
                data: chartData.penghasilan_tiktok
            }],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: chartData.bulan_labels,
            },
            yaxis: {
                title: {
                    text: 'Juta Rupiah'
                },
                labels: {
                    formatter: function(value) {
                        return value.toFixed(2);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID', {minimumFractionDigits: 2}) + ' juta';
                    }
                }
            }
        };

        var chart1 = new ApexCharts(document.querySelector('#visitor-chart'), options1);
        chart1.render();
    })();

    (function () {
        var options = {
            chart: {
                type: 'bar',
                height: 365,
                toolbar: {
                    show: false
                }
            },
            colors: ['#13c2c2'],
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 4
                }
            },
            dataLabels: {
                enabled: false
            },
            series: [{
                data: [80, 95, 70, 42, 65, 55, 78]
            }],
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                show: false
            },
            grid: {
                show: false
            }
        };
        var chart = new ApexCharts(document.querySelector('#income-overview-chart'), options);
        chart.render();
    })();


    (function () {
        var options = {
            chart: {
                type: 'line',
                height: 340,
                toolbar: {
                    show: false
                }
            },
            colors: ['#faad14'],
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 4
                }
            },
            stroke: {
                curve: 'smooth',
                width: 1.5
            },
            grid: {
                strokeDashArray: 4
            },
            series: [{
                data: [58, 90, 38, 83, 63, 75, 35, 55]
            }],
            xaxis: {
                type: 'datetime',
                categories: [
                    '2018-05-19T00:00:00.000Z',
                    '2018-06-19T00:00:00.000Z',
                    '2018-07-19T01:30:00.000Z',
                    '2018-08-19T02:30:00.000Z',
                    '2018-09-19T03:30:00.000Z',
                    '2018-10-19T04:30:00.000Z',
                    '2018-11-19T05:30:00.000Z',
                    '2018-12-19T06:30:00.000Z'
                ],
                labels: {
                    format: 'MMM'
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                show: false
            },
        };
        var chart = new ApexCharts(document.querySelector('#analytics-report-chart'), options);
        chart.render();
    })();
    (function () {
        var options = {
            chart: {
                type: 'bar',
                height: 430,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    columnWidth: '30%',
                    borderRadius: 4
                }
            },
            stroke: {
                show: true,
                width: 8,
                colors: ['transparent']
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                show: true,
                fontFamily: `'Public Sans', sans-serif`,
                offsetX: 10,
                offsetY: 10,
                labels: {
                    useSeriesColors: false
                },
                markers: {
                    width: 10,
                    height: 10,
                    radius: '50%',
                    offsexX: 2,
                    offsexY: 2
                },
                itemMargin: {
                    horizontal: 15,
                    vertical: 5
                }
            },
            colors: ['#faad14', '#1890ff'],
            series: [{
                name: 'Net Profit',
                data: [180, 90, 135, 114, 120, 145]
            }, {
                name: 'Revenue',
                data: [120, 45, 78, 150, 168, 99]
            }],
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
            },
        }
        var chart = new ApexCharts(document.querySelector('#sales-report-chart'), options);
        chart.render();
    })();
}
