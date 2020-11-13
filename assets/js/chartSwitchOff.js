$(document).ready(function () {
    var i = 1;
    chartSwitch(i);
    $('#chartNext').on('click', function () {
        i++;
        if (7 === i) {
            i = 1;
        }
        chartSwitch(i);
    });
    $('#chartPrevious').on('click', function () {
        i--;
        if (0 === i) {
            i = 6;
        }
        chartSwitch(i);
    });
    function chartSwitch(i) {
        for (j = 1; j <= 6; j++) {
            $('#chart' + j).hide();
        }
        $('#chart' + i).show();
    }
});
