/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
//import './js/chartSwitch.js';
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
//import $ from 'jquery';

//console.log('Hello Webpack Encore! Edit me in assets/app.js');
const $ = require('jquery');
require('bootstrap');
$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
    $('#div_chart_c').toggle(true);
    $('#div_chart_b').toggle(false);
    $('#div_chart_histo').toggle(false);
    var i = 1;
    $('#charts').on('click', function () {
//        if (3 === i % 3) {
//            alert(i + ' True'); 
//        } else {
//            alert(i + ' False');
//        }
        $('#div_chart_b').toggle(1 === i % 3);
        $('#div_chart_histo').toggle(2 === i % 3);
        $('#div_chart_c').toggle(0 === i % 3);
        i ++;
    });
});