console.log('admin.js dispatched');

$().ready(function(){
    $('.toast-message').each(function(i, e){
        window.sMessage.show(
            $(e).attr('data-title'),
            $(e).attr('data-text'),
            $(e).attr('data-type'),
            $(e).attr('data-duration'));
    });
});

window.challenge = {
    working: false,
    toggle: function (el) {
        if(this.working) return;

        this.working = true;
        console.log('Toggling challenge status');

        var status = $(el).attr('data-status');

        console.log('Current status: ' + status);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var context = this;

        if(status === "0")
        {
            $(el).attr('data-status', 1);
            $('#state-label').removeClass('label-success').addClass('label-danger').html('Disabled');
            console.log('new status: 1');

        } else if(status === "1") {
            $(el).attr('data-status', 0);
            $('#state-label').removeClass('label-danger').addClass('label-success').html('Active');
            console.log('new status: 0');
        }

        $.ajax({
            type: 'POST' ,
            url: APP_URL+ '/admin/ajax/challenge/toggle',
            data:{ currentStatus: status, challengeId : $(el).data('id')},
            dataType: 'json',
            success:function(data) {
                if(data.status)
                {
                    window.sMessage.show('Yes!',
                        'Status toggled',
                        'primary',
                        3000);
                } else {
                    window.sMessage.show('Ouch!',
                        'Error toggling status',
                        'error',
                        3000);
                }

                context.working = false;
            }
        });

  }  
};
window.mImage = {
    open: function(el){
        $('#imagePreview').attr('src', $(el).attr('data-href'));
        $('#loading-view').hide();
    }
};
