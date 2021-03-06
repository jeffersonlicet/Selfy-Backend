@extends('layouts.withsidebar')
@section('pageTitle')
{{isset($pageTitle) ? $pageTitle : 'Roles'}}
@endsection
@section('content')

<div class="container-fluid admin">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">{{trans('selfy-admin.editRoleTitle').' '.$role->name}}</h3>
		</div>
		<div class="box-body">
			<div class="row">
	            <div class="col-lg-10 col-md-10 col-xs-12 col-lg-offset-1 col-md-offset-1">

                	<div class="row">
						@if ($errors->any())
							<div class="alert alert-danger">
								<ul>
									@foreach ($errors->all() as $error)
										<li>{{ $error }}</li>
									@endforeach
								</ul>
							</div>
						@endif
                		{!! Form::model($role, ['route' => ['SelfyAdminRolesUpdate', $role->id]]) !!}
	                        <div class="col-md-6 slugable">
	                            <div class="form-group">
	                                {!! Form::label('display_name', trans('selfy-admin.roleName')) !!}
	                                {!! Form::text('display_name', null, ['class' => 'form-control slug-source']) !!}
	                                @if($errors->has('display_name'))
	                                    <span class="text-danger">{{$errors->first('display_name')}}</span>
	                                @endif
	                            </div>
	                            <div class="form-group">
	                                {!! Form::label('name', trans('selfy-admin.roleSlug')) !!}
	                                {!! Form::text('name', null, ['class' => 'form-control slug-target', 'readonly' => 'readonly']) !!}
	                                @if($errors->has('display_name'))
	                                    <span class="text-danger">{{$errors->first('display_name')}}</span>
	                                @endif
	                            </div>
	                            <div class="form-group">
	                                {!! Form::label('description', trans('selfy-admin.roleDescription')) !!}
	                                {!! Form::textarea('description', null,['class' => 'form-control']) !!}
	                                @if($errors->has('description'))
	                                    <span class="text-danger">{{$errors->first('description')}}</span>
	                                @endif
	                            </div>
	                            <div class="form-group">
	                                {!! Form::submit(trans('selfy-admin.save'), ['class' => 'btn btn-primary']) !!}
	                            </div>
	                        </div>
                        {!! Form::close() !!}
                    </div>
	            </div>
	        </div>	
		</div>
	</div>
</div>
@endsection