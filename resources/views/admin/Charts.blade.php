@extends('admin/master')
@section('content')
<div class="card">
    <h5 class="card-header">Thống kê</h5>
    <div class="row" style="margin-left:30px">
        <button class='btn btn-success m-2' style="max-width:100px" onclick="updateChart('week')">Tuần</button>
        <button class='btn btn-success m-2' style="max-width:100px" onclick="updateChart('month')">Tháng</button>
        <button class='btn btn-success m-2' style="max-width:100px" onclick="updateChart('year')">Năm</button>
    </div>
    <div class="p-3" id="line_top_x"></div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script type="text/javascript">
    google.charts.load('current', { 'packages': ['line'] });
    google.charts.setOnLoadCallback(function () {
        updateChart('year');
    });

    function updateChart(timeRange) {
        $.ajax({
            url: '/charts/' + timeRange,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                drawChart(response.chartData);
            },
            error: function (error) {
                console.error('Error fetching chart data:', error);
            }
        });
    }

    function drawChart(chartData) {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Thời gian');
        data.addColumn('number', 'Thực phẩm');
        data.addColumn('number', 'Giao dịch');
        data.addColumn('number', 'Tài khoản');
        for (var i = 0; i < chartData.length; i++) {
            data.addRow([chartData[i].date, chartData[i].foods, chartData[i].transactions, chartData[i].users]);
        }
        var options = {
            chart: {
                title: 'Thống kê Thực phẩm, giao dịch, tài khoản',
            },
            width: '100%',
        height: 500,   
        explorer: { 
            axis: 'horizontal', 
            keepInBounds: true, 
        },
            axes: {
                x: {
                    0: { side: 'top' }
                }
            }
        };

        var chart = new google.charts.Line(document.getElementById('line_top_x'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }


</script>
@endsection