@extends('layouts.withsidebar')
@section('pageTitle')
{{isset($pageTitle) ? $pageTitle : 'Roles'}}
@endsection
@section('content')

<div class="container-fluid admin">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans('selfy-admin.rolesListTitle')}}</h3>
            <div class="box-tools">
                <a href="{{route('SelfyAdminRolesCreate')}}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i> {{trans('selfy-admin.createRoleTitle')}}</a>
            </div>
        </div>
        <div class="box-body">
            <table id="roles-table" class="table table-condensed">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(function() {
            $('#roles-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('SelfyAdminRoles') }}',
                columns: [
                    {data: 0, name: 'id'},
                    {data: 1, name: 'name'},
                    {data: 2, name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endsection
