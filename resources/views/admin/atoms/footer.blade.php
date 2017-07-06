<div class="modal" id="imagePreviewModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Photo</h4>
            </div>
            <div class="modal-body">
                <div class="loader bounceIn" id="loading-view">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <img src="" id="imagePreview" class="img-responsive">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="createHashtagModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">New Hashtag</h4>
            </div>
            <div class="modal-body">
                <div class="loader wow bounceIn" id="loading-create-hashtag">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <form class="form-horizontal" id="form-create-hashtag">
                    <fieldset>
                        <div class="col-md-10 col-md-offset-1">

                            <div class="form-group">
                                <label for="hashtag_text" class="control-label">Hashtag text</label>
                                <input autocomplete="off" class="form-control" id="hashtag_text" placeholder="#Selfie" type="text">
                            </div>

                            <div class="form-group">
                                <div class="togglebutton">
                                    <label>
                                        <input id="hashtag_status" type="checkbox" checked> Enabled
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="togglebutton">
                                    <label>
                                        <input id="hashtag_group" type="checkbox"> Promoted
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-raised btn-primary" onclick="window.hashtag.create(this)">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="changePlayHashtagModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Change hashtag</h4>
            </div>
            <div class="modal-body">
                <div class="loader wow bounceIn" id="loading-update-play-hashtag">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <form class="form-horizontal" id="form-update-play-hashtag">
                    <fieldset>
                        <div class="col-md-10 col-md-offset-1">
                            <div class="form-group">
                                <label for="hashtag_id" class="control-label">Hashtag id</label>
                                <input autocomplete="off" class="form-control" id="update_hashtag_id" placeholder="#000" type="text">
                                <button type="button" class="btn btn-raised btn-primary" onclick="window.hashtag.update(this)">Save</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="loadingModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Loading</h4>
            </div>
            <div class="modal-body">
                <div class="loader" style="display:block!important">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>




