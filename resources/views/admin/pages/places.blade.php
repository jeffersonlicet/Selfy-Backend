@extends('admin.layouts.default')
@section("content")
    <div class="section-title">
        <h2>Places management</h2>
        <h4>Here you can create Spots using Foursquare Places</h4>
    </div>
    <a href="{{ action('Admin\AdminController@createPlay') }}" class="btn btn-info btn-fab floating-button"><i class="material-icons">add</i></a>
    <div class="container-fluid">
        <div class="col-md-12">
            <table class="table table-striped table-hover ">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Create</th>
                </tr>
                </thead>
                <tbody>

                @foreach($places as $place)
                    <tr>
                        <td>{{ $place->place_id }}</td>
                        <td title="{{ $place->name }}" data-toggle="tooltip">{{ str_limit($place->name, 20, '...') }}</td>
                        <td title="{{ $place->category }}" data-toggle="tooltip">{{ str_limit($place->category, 20, '...') }}</td>
                        <td title="{{ $place->country }}" data-toggle="tooltip">{{ str_limit($place->country, 20, '...') }}</td>
                        <td title="{{ $place->state }}" data-toggle="tooltip">{{ str_limit($place->state, 20, '...') }}</td>
                        <td title="{{ $place->city }}" data-toggle="tooltip">{{ str_limit($place->city, 20, '...') }}</td>
                        <td data-toggle="tooltip" title="Create Spot"><a href="#" data-toggle="modal" onclick="window.place.createSpot(this)" data-target="#loadingModal" data-place="{{ $place->place_id }}"  ><i class="material-icons">edit_location</i></a> </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $places->links() }}
        </div>
    </div>
@stop