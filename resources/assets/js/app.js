console.log('app.js dispatched');
$().ready(function(){
    $('a[href*="#"]').click(function(e) {
        e.preventDefault();
        var target = this.hash;
        $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top
        }, 900, 'swing', function () {
            window.location.hash = target;
        });
    });
});
window.contact =  {
    button: null,
    buttonDisabled: false,

    send: function (button) {
        this.button = button;
        this.toggleButton();
        this.buttonDisabled = true;
        this.buildAndSend();
    },
    toggleButton: function() {
        this.buttonDisabled ? $(this.button).prop('disabled', false) : $(this.button).prop('disabled', true);
        this.buttonDisabled = !this.buttonDisabled;
    },
    validateEmail: function(email) {
        var re = /\S+@\S+/;
        return re.test(email);
    },
    buildAndSend: function()
    {
        var loader = $('#loading-form');
        var formBody = $('#form-section');

        var name = $('#inputName');
        var email = $('#inputEmail');
        var text = $('#inputBody');

        var inputs = ['#inputName', '#inputEmail', '#inputBody'];

        for (var i in inputs)
        {
            if($(inputs[i]).val().length === 0) {
                $(inputs[i]).focus();
                this.toggleButton();
                return false;
            }
        }

        if(!this.validateEmail(email.val())) {
            $(email).focus();
            this.toggleButton();
            return false;
        }

        loader.show();
        formBody.slideUp();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var formData = {
            name: name.val(),
            email: email.val(),
            message: text.val()
        };
        var context = this;
        $.ajax({
            type: "POST",
            url: APP_URL+"/index.php/ajax/contact",
            data: formData,
            dataType: 'json',
            success: function (data) {
                if(data.status){
                    formBody.html('<h1>'+CONTACT_SENT_LANG+'</h1>').slideDown();
                    loader.slideUp();
                    $(context.button).hide();

                    name.val("");
                    email.val("");
                    text.val("");

                } else {
                    formBody.html('<h1>'+CONTACT_ERROR_LANG+'</h1>').slideDown();
                    loader.slideUp();
                }
            },
            error: function (data) {
                formBody.html('<h1>'+CONTACT_ERROR_LANG+'</h1>').slideDown();
                loader.slideUp();
                console.log('Error:', data);
            }
        });

    }
};