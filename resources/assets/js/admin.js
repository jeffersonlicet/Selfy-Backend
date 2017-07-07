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

window.hashtag = {
    working: false,
    update:function(el){
        if(this.working) return;
        var selector = $('#update_hashtag_id');
        var newId = selector.val();

        this.working = true;
        var context = this;

        if(newId.length === 0)
        {
            selector.focus();
            context.working = false;
            return;
        }

        $('#form-update-play-hashtag').hide();
        $('#loading-update-play-hashtag').show();

        var _data = {
            hashtag_id: newId,
            play_id: $('#play_id').val()
        };

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: APP_URL +'/admin/ajax/play/update_hashtag',
            data : _data,
            dataType: 'json',
            success: function(data) {

                if(data.status)
                {
                    window.sMessage.show('Play hashtag updated',
                        'Hashtag id #'+newId,
                        'primary',
                        15000);

                    $('#changePlayHashtagModal').find('.close').click();
                    $('#form-update-play-hashtag').show();
                    $('#loading-update-play-hashtag').hide();
                    $('#hashtag_text').val('');

                } else {
                    window.sMessage.show('Ouch!',
                        'Error updating hashtag',
                        'error',
                        3000);

                    $('#form-update-play-hashtag').show();
                    $('#loading-update-play-hashtag').hide();
                }

                context.working = false;
            }
        });
    },
    create: function(el){
        if(this.working) return;

        this.working = true;
        var context = this;

        var textInput = $('#hashtag_text');

        if(textInput.val().length === 0)
        {
            textInput.focus();
            context.working = false;
            return;
        }

        $('#form-create-hashtag').hide();
        $('#loading-create-hashtag').show();

        var _data = {
            hashtag_text: textInput.val(),
            hashtag_status: $('#hashtag_status').is(':checked'),
            hashtag_group: $('#hashtag_group').is(':checked')
        };

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: APP_URL +'/admin/ajax/hashtag/create',
            data : _data,
            dataType: 'json',
            success: function(data) {
                if(data.status)
                {
                    window.sMessage.show('Hashtag created',
                        'Hashtag #'+data.id,
                        'primary',
                        15000);

                    $('#createHashtagModal').find('.close').click();
                    $('#form-create-hashtag').show();
                    $('#loading-create-hashtag').hide();
                    $('#hashtag_text').val('');

                } else {
                    window.sMessage.show('Ouch!',
                        'Error creating hashtag',
                        'error',
                        3000);

                    $('#form-create-hashtag').show();
                    $('#loading-create-hashtag').hide();
                }

                context.working = false;
            }
        });
    }
};

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
            $('#state-label-'+$(el).data('id')).removeClass('label-success').addClass('label-danger').html('Disabled');
            console.log('new status: 1');

        } else if(status === "1") {
            $(el).attr('data-status', 0);
            $('#state-label-'+$(el).data('id')).removeClass('label-danger').addClass('label-success').html('Active');
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

window.play = {
    working:false,
    appendObject: function(el){
        if(this.working) return;

        this.working = true;
        var context = this;

        var objectId = $('#object_id');
        var playId = $('#play_id');

        if(objectId.val().length === 0){
            objectId.focus();
            context.working = false;
            return;
        }

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: APP_URL +'/admin/ajax/play/associate_object',
            data : {object_id :objectId.val(), play_id: playId.val() },
            dataType: 'json',
            success: function(data) {

                $('#loadingModal').find('.close').click();

                if(data.status)
                {
                    window.sMessage.show('Done!',
                        'Object associated with challenge',
                        'primary',
                        15000);
                } else {
                    window.sMessage.show('ow!',
                        'Error associating object :(',
                        'primary',
                        15000);
                }

                context.working = false;
            }
        });
    },
    removeObject: function(el){
        if(this.working) return;

        this.working = true;
        var _play_id = $(el).data('play');
        var _object_id = $(el).data('object');
        var context = this;

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#object_'+_object_id).hide();

        $.ajax({
            type: 'POST',
            url: APP_URL +'/admin/ajax/play/remove_object',
            data : {play_id :_play_id, object_id: _object_id},
            dataType: 'json',
            success: function(data) {
                $('#loadingModal').find('.close').click();

                if(data.status)
                {
                    window.sMessage.show('Done',
                        'Association deleted',
                        'primary',
                        15000);
                } else {
                    window.sMessage.show('ow!',
                        'Error deleting assoc :(',
                        'primary',
                        15000);
                }

                context.working = false;
            }
        });
    },
    appendObjectGenerated: function(el){
        if(this.working) return;

        this.working = true;
        var context = this;

        var objectId = $(el).attr('data-id');
        var playId = $('#play_id').val();

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: APP_URL +'/admin/ajax/play/associate_object',
            data : {object_id :objectId, play_id: playId},
            dataType: 'json',
            success: function(data) {

                $('#loadingModal').find('.close').click();

                if(data.status)
                {
                    var parent = $(el).closest('.tr');

                    parent.find('.associated').html('<span class="label label-success">Yes</span>');
                    parent.find('.associate').html('-');

                    window.sMessage.show('Done!',
                        'Object associated',
                        'primary',
                        15000);

                } else {
                    window.sMessage.show('ow!',
                        'Error associating object :(',
                        'primary',
                        15000);
                }

                context.working = false;
            }
        });
    }
};
