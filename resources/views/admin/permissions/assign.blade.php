@extends('LaravelAdmin::layouts.withsidebar')
@section('pageTitle')
    {{isset($pageTitle) ? $pageTitle : 'Permissions'}}
@endsection
@section('content')

    <div class="container-fluid admin">
        <div class="box panel-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    {{trans('selfy-admin.managePermissions')}} <strong>{{$model->display_name}}</strong>
                </h3>
            </div>
            {!! Form::open(['route' => ['SelfyAdminRolesPermissionsUpdate', $model->id], 'method' => 'PUT']) !!}
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-xs-12">
                        <p>Select the permissions the role will have access to.</p>
                        <div class="row">
                            @foreach($permissionsList as $id => $name)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::checkbox('permissions[]', $id, in_array($id, $permissions), ['id' => 'perm_'.$id]) !!}
                                        <label for="perm_{{ $id }}">{{ $name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="row">
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block">
                            <i class="fa fa-save"></i> {{trans('selfy-admin.save')}}
                        </button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="modal fade" id="permissionsAddModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"
                        id="myModalLabel">{{trans('selfy-admin.AssignPermission')}}</h4>
                </div>
                {!! Form::open(['id' => 'AssignPermissionsForm']) !!}
                <div class="modal-body">
                    {{trans('LaravelAdmin::laravel-admin.AssignPermissionModalText')}}
                    <hr/>
                    <div class="form-group">
                        {!! Form::select('perms[]', [], null, ['id' => 'permissionsSelect', 'class' => 'form-control', 'multiple' => 'multiple']) !!}
                        <input type="hidden" name="type" id="Modeltype" value=""/>
                        <input type="hidden" name="model" id="Modelid" value=""/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger"
                            data-dismiss="modal">{{trans('selfy-admin.cancel')}}</button>
                    <button type="submit"
                            class="btn btn-primary">{{trans('selfy-admin.assign')}}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
