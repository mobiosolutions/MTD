<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ChangePasswordRequest;

class UserController extends Controller
{
    /**
     * user constructor.
     * @param  $user
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * create user detail
     * @param request
     * @return userData
     */
    public function createUser(UserRequest $request)
    {

        $validate = $request->validated();
        if ($validate == false) {
            return false;
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ]);
    }

    /**
     * find user detail
     * @param $request
     * @return userDetail
     */
    public function findUser(Request $request)
    {
        $userDetail = User::findOrFail($request->id);
        if ($userDetail) {
            return $userDetail;
        }
    }

    /**
     * find user detail
     * 
     * @return userDetail
     */
    public function getUsers()
    {
        $userDetail = User::get();
        if ($userDetail) {
            return $userDetail;
        }
    }

    /**
     * update user detail
     * @param request
     * @return updated
     */
    public function updateUser(Request $request)
    {
        $userDetail = User::findOrFail($request->id);
        $userDetail->name = $request->name;
        $userDetail->email = $request->email;
        $updated = $userDetail->save();
    }

    /**
     * delete user detail
     * @param request
     * @return deleted
     */
    public function deleteUser(Request $request)
    {
        $deleteUserDetail = User::findOrFail($request->id);
        $deleted = $deleteUserDetail->delete();
    }


    /**
     * Change password
     * @param request
     * @return array
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $request->validated();

        $user = Auth::user();
        $user->password = bcrypt($request->new_password);
        $user->save();

        return $request->all();
    }
}
