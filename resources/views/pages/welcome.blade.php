@extends('layouts.landing')
@section("content")
    <section class="section-1" id="home">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-6 col-xs-offset-3">
                    <img  src="{{ URL::asset('/images/view1_principal_r_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-md-offset-1 col-sm-offset-1 col-sm-6 col-xs-12">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>#Selfy</h1>
                            <h3>{{  __('app.slogan') }}</h3>
                            <a href="#purecamera" class="btn btn-raised btn-primary">{{  __('app.tour') }}</a>
                            <a href="#getselfy" class="btn btn-raised btn-success">{{  __('app.get_selfy') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/view1_principal_r_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
        </div>
    </section>
    <section class="section-2" id="purecamera">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-4 device-container wow fadeIn">
                    <img  src="{{ URL::asset('/images/view2_principal_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>

                <div class="col-md-5 col-md-offset-1 col-sm-offset-1 col-sm-5">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>{{ __('app.s2_title') }}</h1>
                            <h3>{{ __('app.s2_subtitle') }} </h3>
                            <a href="#challenges" class="btn btn-success btn-fab wow fadeInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
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
                            <h1>{{ __('app.s3_title') }}</h1>
                            <h3>{{ __('app.s3_subtitle') }} </h3>
                            <a href="#duo" class="btn btn-default btn-fab wow fadeInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
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
                    <img  src="{{ URL::asset('/images/duo_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>{{ __('app.s4_title') }}</h1>
                            <h3>{!! __('app.s4_subtitle') !!} </h3>
                            <a href="#spot" class="btn btn-info btn-fab wow fadeInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/duo_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
        </div>
    </section>
    <section class="section-5" id="spot">
        <div class="container">
            <div class="row visible-xs">
                <div class="col-xs-12 device-container">
                    <img  src="{{ URL::asset('/images/spot_collage_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col-sm-5 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/spot_collage_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>

                <div class="col-md-5 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>{{ __('app.s5_title') }}</h1>
                            <h3>{!! __('app.s5_subtitle') !!} </h3>
                            <a href="#play" class="btn btn-default btn-fab wow fadeInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
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
                    <img  src="{{ URL::asset('/images/play_interlaced.png') }}" class="img-responsive" alt="home photo">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-md-offset-1 col-sm-offset-1">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>{{ __('app.s6_title') }}</h1>
                            <h3>{!! __('app.s6_subtitle') !!} </h3>
                            <a href="#getselfy" class="btn btn-danger btn-fab wow fadeInDown" id="continue2"><i class="material-icons">keyboard_arrow_down</i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-4 device-container hidden-xs wow fadeIn">
                    <img  src="{{ URL::asset('/images/play_interlaced.png') }}" class="img-responsive" alt="home photo">
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
                            <h1>{{ __('app.s7_title') }}</h1>
                            <h3>{{ __('app.s7_subtitle') }} </h3>
                            <a href="javascript:void(0)" class="btn btn-raised btn-primary">{{ __('app.s7_button')}}</a>
                            </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="header-content">
                        <div class="header-content-inner social-wrapper">
                            <a href="https://www.facebook.com/getselfy/" target="_blank" class="social-link">
                                    <img data-toggle="tooltip" data-placement="left" title="{{__('app.follow_fb')}}"
                                         src="{{URL::asset('/images/social-1_logo-facebook.svg')}}">
                            </a>
                            <a href="https://twitter.com/getselfy/" target="_blank" class="social-link">
                                    <img data-toggle="tooltip" data-placement="right" title="{{__('app.follow_tw')}}"
                                         src="{{URL::asset('/images/social-1_logo-twitter.svg')}}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop