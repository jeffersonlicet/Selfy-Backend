<div class="navbar navbar-transparent">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-warning-collapse">
                <i class="material-icons">menu</i>
            </button>
        </div>
        <ul class="nav navbar-nav navbar-right navbar-collapse collapse navbar-warning-collapse">
            <li style="display: none;"><a href="#">About us</a></li>
            <li><a href="javascript:void(0)" data-toggle="modal" data-target="#contactModal">{{ __('app.contact') }}</a></li>
            <li style="display: none;"><a href="#">Privacy</a></li>
            <li style="display: none;"><a href="#">Help</a></li>
        </ul>
    </div>
</div>

<!-- Modal -->
<div class="modal modal-contact animated fadeIn" id="contactModal" tabindex="-1" role="dialog" aria-labelledby="contactModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{__('app.contact_form_title')}}</h4>
            </div>
            <div class="modal-body">
                <div class="loader wow bounceIn" id="loading-form">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <form class="form-horizontal" id="form-section">
                    <fieldset>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group">
                                <label for="inputName" class="control-label">{{__('app.contact_form_name')}}</label>
                                <input autocomplete="off" class="form-control" id="inputName" placeholder="{{__('app.contact_form_name_input')}}" type="text">
                            </div>
                            <div class="form-group">
                                <label  class="control-label" for="inputEmail">{{__('app.contact_form_email')}}</label>
                                <input  class="form-control" id="inputEmail" placeholder="{{__('app.contact_form_email_input')}}" type="email">
                            </div>
                            <div class="form-group">
                                <label  class="control-label" for="inputBody">{{__('app.contact_form_body')}}</label>
                                <textarea autocomplete="off" class="form-control" rows="2" id="inputBody" placeholder="{{__('app.contact_form_body_input')}}"></textarea>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{__('app.close')}}</button>
                <button type="button" class="btn btn-raised btn-primary" onclick="contact.send(this)">{{__('app.send')}}</button>
            </div>
        </div>
    </div>
</div>