
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('bootstrap');
require('bootstrap-material-design');

const WOW = require('wowjs');
window.wow = new WOW.WOW({ live: false });
wow.init();

$().ready(function () {
    $.material.init();
    $.material.ripples();
    $('[data-toggle="tooltip"]').tooltip();
    console.log("libs.js dispatched");
});