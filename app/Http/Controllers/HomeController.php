<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Redirect;
use Validator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function changePassword(request $request)
    {

        $rules = array(
            'old_password' => 'required|min:8|Different:new_password|max:15',
            'new_password' => 'required|min:8|Same:confirm_password|max:15',
            'confirm_password' => 'required|min:8|max:15'
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect('changePassword')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        if (Hash::check($request->old_password, Auth::user()->password)) {

            $user = User::find(Auth::user()->id);
            $user->password = bcrypt($models['new_password']);
            $user->updated_by = Auth::user()->id;
            $user->updated_at = date('Y-m-d H:i:s');
            $userId = $user->save();
        }  

    }
}
