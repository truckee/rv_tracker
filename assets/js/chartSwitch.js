$(document).ready(function () {
    $('#priceC').show;
    $('#countC').hide;
    $('#priceB').hide;
    $('#countB').hide;
    $('#histoC').hide;
    $('#histoB').hide;
    var i = 1;
    $('#charts').on('click', function () {
        $('#countC').toggle(1 === i % 6);
        $('#priceB').toggle(2 === i % 6);
        $('#countB').toggle(3 === i % 6);
        $('#histoC').toggle(4 === i % 6);
        $('#histoB').toggle(5 === i % 6);
        $('#priceC').toggle(0 === i % 6);
        i++;
    });
});
