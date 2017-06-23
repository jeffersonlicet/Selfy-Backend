<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->createAdminRole();
         $this->createAclPermissions();
         $this->createUserPermission();
    }


    private function createAdminRole()
    {
        $owner = new \App\Models\Role();
        $owner->name = 'system-administrator';
        $owner->display_name = 'System Administrator';
        $owner->description = 'The General admin user';
        $owner->save();

        $owner = new \App\Models\Role();
        $owner->name = 'system-moderator';
        $owner->display_name = 'System Moderator';
        $owner->description = 'The General moderator user';
        $owner->save();


        return $owner;
    }

    private function createUserPermission()
    {
        /** Edit User **/
        $editUser = new \App\Models\Permission();
        $editUser->name = 'edit-user';
        $editUser->display_name = 'Edit User';
        $editUser->description = 'Edit existing users';
        $editUser->save();
        /** Create User **/
        $createUser = new \App\Models\Permission();
        $createUser->name = 'create-user';
        $createUser->display_name = 'Create Users';
        $createUser->description = 'Create new Users';
        $createUser->save();
        /** Delete User **/
        $deleteUser = new \App\Models\Permission();
        $deleteUser->name = 'delete-user';
        $deleteUser->display_name = 'Delete Users';
        $deleteUser->description = 'Delete Users';
        $deleteUser->save();
        /** list Users **/
        $listUser = new \App\Models\Permission();
        $listUser->name = 'list-users';
        $listUser->display_name = 'List Users';
        $listUser->description = 'List Users and Manage them';
        $listUser->save();

        $editPhoto = new \App\Models\Permission();
        $editPhoto->name = 'edit-photo';
        $editPhoto->display_name = 'Edit Photo';
        $editPhoto->description = 'Edit existing photo';
        $editPhoto->save();

        /** Create User **/
        $createPhoto = new \App\Models\Permission();
        $createPhoto->name = 'create-photo';
        $createPhoto->display_name = 'Create Photos';
        $createPhoto->description = 'Create new Photos';
        $createPhoto->save();

        /** Delete User **/
        $deletePhoto = new \App\Models\Permission();
        $deletePhoto->name = 'delete-photo';
        $deletePhoto->display_name = 'Delete Photos';
        $deletePhoto->description = 'Delete Photos';
        $deletePhoto->save();

        /** list Users **/
        $listPhoto = new \App\Models\Permission();
        $listPhoto->name = 'list-photos';
        $listPhoto->display_name = 'List Photos';
        $listPhoto->description = 'List Photos and Manage them';
        $listPhoto->save();

        return [
            $editUser,
            $createUser,
            $deleteUser,
            $listUser,
            $editPhoto,
            $createPhoto,
            $deletePhoto,
            $listPhoto,
            $editPhoto
        ];
    }


    private function createAclPermissions()
    {
        /** roles crud **/
        $rolesCrud = new \App\Models\Permission();
        $rolesCrud->name = 'roles-crud';
        $rolesCrud->display_name = 'Roles Crud';
        $rolesCrud->description = 'Create, update and delete roles';
        $rolesCrud->save();
        return [
            $rolesCrud
        ];
    }


}
