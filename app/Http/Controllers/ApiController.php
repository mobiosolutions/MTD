<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class ApiController extends Controller
{

    /**
     * get all user detail
     * 
     * @return userDetail
     */
    public function getAllUsers()
    {
        $users = User::get();
        return response()->json(["data" => $users, "message" => "all user detail fetched"], 200);
    }

    /**
     * create user
     * @param $request
     * @return json
     */
    public function createUser(UserRequest $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $saved = $user->save();
        if ($saved) {
            return response()->json(["data" => $user, "message" => "user created successfully"], 201);
        } else {
            return response()->json(["data" => $user, "message" => "user not created"], 404);
        }
    }

    /**
     * get user detail
     * @param $id
     * @return json
     */
    public function getUser($id)
    {
        $userExists = User::where('id', $id)->get();
        if (count($userExists) != 0) {
            return response()->json(["data" => $userExists, "message" => "user detail fetched"], 200);
        } else {
            return response()->json(["data" => $userExists, "message" => "user not found"], 404);
        }
    }

    /**
     * update user detail
     * @param $request, $id
     * @return json
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $updated = $user->save();
        if ($updated) {
            return response()->json(["data" => $user, "message" => "user updated successfully"], 200);
        } else {
            return response()->json(["data" => $user, "message" => "user not found"], 404);
        }
    }

    /**
     * delete user detail
     * @param $id
     * @return json
     */
    public function deleteUser($id)
    {
        $user = User::find($id);
        $deleted = $user->delete();
        if ($deleted) {
            return response()->json(["message" => "user deleted successfully"], 200);
        } else {
            return response()->json(["message" => "user not found"], 404);
        }
    }
}
