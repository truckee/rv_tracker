$(document).ready(function () {
    var i = 1;
    chartSwitchOn(i);

    function chartSwitchOn(i) {
        for (j = 1; j <= 6; j++) {
            $('#chart' + j).show();
        }
    }
});

