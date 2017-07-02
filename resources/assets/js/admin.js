console.log('admin.js dispatched');
$().ready(function(){

});

window.mImage = {
    open: function(el){
        $('#imagePreview').attr('src', $(el).attr('data-href'));
        $('#loading-view').hide();
    }
};
