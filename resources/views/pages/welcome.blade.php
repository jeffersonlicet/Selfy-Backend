@extends('layouts.landing')

@section("content")
    <section class="section-1" id="home">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-6 col-xs-offset-3">
                    <img  src="{{ URL::asset('/images/view1_principal_r_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-md-offset-1 col-sm-offset-1 col-sm-6 col-xs-12">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>#Selfy</h1>
                            <h3>The smart and challenging photo sharing app.</h3>
                            <a href="javascript:void(0)" class="btn btn-raised btn-primary">Take the tour</a>
                            <a href="javascript:void(0)" class="btn btn-raised btn-success">Get Selfy</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/view1_principal_r_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
        </div>
    </section>
    <section class="section-2" id="purecamera">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 device-container wow fadeIn">
                    <img  src="{{ URL::asset('/images/view2_principal_s.png') }}" class="img-responsive" alt="home photo">
                </div>

                <div class="col-md-5 col-md-offset-1 col-sm-offset-1 col-sm-5">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Photos, friends and more</h1>
                            <h3>Discover a new way to share your photos on an intelligent platform that will take your photos to another level. </h3>
                            <a href="javascript:void(0)" class="btn btn-success btn-fab wow slideInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-3" id="challenges">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-5">
                </div>

                <div class="col-md-5 animated fadeIn">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Do you like challenges?</h1>
                            <h3>Challenges are the new way of interacting in Selfy. To complete them you must share photos with friends, with your pet or maybe in some great place.</h3>
                            <a href="javascript:void(0)" class="btn btn-default btn-fab wow slideInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-4" id="duo">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-12 device-container">
                    <img  src="{{ URL::asset('/images/duo_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Duo</h1>
                            <h3>A Duo is a face-based challenge. <br /><br /> Selfy uses face recognition technology to encourage people to complete Duo challenges that can be completed by taking photos with your family, friends or another Selfy user.</h3>
                            <a href="javascript:void(0)" class="btn btn-info btn-fab wow slideInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/duo_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
        </div>
    </section>
    <section class="section-5" id="spot">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-12 device-container">
                    <img  src="{{ URL::asset('/images/spot_collage.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-sm-5 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/spot_collage.png') }}" class="img-responsive" alt="home photo">
                </div>

                <div class="col-md-5 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Spot</h1>
                            <h3>A Spot is a place-based challenge.<br /><br /> To complete a Spot challenge you must visit the indicated place and take a picture there. We will use your location to detect if you meet the challenge.</h3>
                            <a href="javascript:void(0)" class="btn btn-default btn-fab wow slideInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-6" id="play">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-12 device-container">
                    <img  src="{{ URL::asset('/images/play_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">


                <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Play</h1>
                            <h3>The theme-based challenges. <br /> <br /> The Play challenges are different and fun, from taking photos with objects, a picture with your pet or maybe a photo eating a burger. <br />We use AI to process these challenges.</h3>
                            <a href="javascript:void(0)" class="btn btn-danger btn-fab wow slideInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/play_s.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
        </div>
    </section>
    <section class="section-7" id="getselfy">
        <div class="container">

            <div class="row">

                <div class="col-md-12">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Get Selfy now</h1>
                            <h3>And start sharing amazing photos in our community. What are you waiting?</h3>
                            <a href="javascript:void(0)" class="btn btn-raised btn-primary">Available soon</a>
                            </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="header-content">
                        <div class="header-content-inner social-wrapper">
                            <a href="https://www.facebook.com/getselfy/" target="_blank" class="social-link">
                                    <img data-toggle="tooltip" data-placement="left" title="Follow us on Facebook"
                                         src="{{URL::asset('/images/social-1_logo-facebook.svg')}}">
                            </a>
                            <a href="https://twitter.com/getselfy/" target="_blank" class="social-link">
                                    <img data-toggle="tooltip" data-placement="right" title="Follow us on Twitter"
                                         src="{{URL::asset('/images/social-1_logo-twitter.svg')}}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop