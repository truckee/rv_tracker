$(document).ready(function () {
    var i = $('#currentChart').attr('data-chart');
    chartSwitch(i);
    $('#clickMe').on('click', function () {
        $('#chartNext').on('click', function () {
            i++;
            if (6 === i) {
                i = 0;
            }
            chartSwitch(i);
        });

        $('#chartPrevious').on('click', function () {
            i--;
            if (-1 === i) {
                i = 5;
            }
            chartSwitch(i);
        });

        function chartSwitch(i) {
            $('#currentChart').attr('data-chart', i);
            var url = "/js/" + i;
            $.getScript(url);
        }
    });
});
