
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('bootstrap');
require('C:/lumus/htdocs/Seginus/node_modules/tasty-toast/dist/tasty-toast');
require('bootstrap-material-design');

const WOW = require('wowjs');
window.wow = new WOW.WOW({ live: false });
wow.init();

$().ready(function () {
    $.material.init();
    $.material.ripples();
    $('[data-toggle="tooltip"]').tooltip({container: 'body'});
    console.log("libs.js dispatched");
});

window.sMessage = {
    show: function(title, text, type, duration){
        Tasty.Toast({
            type: type, //['error','success','primary','secondary'] are possible values (or leave it empty for no theme)
            title: title,
            content: text,
            duration: duration,
            onclick: null
        });
    }
};