<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(){
        $data['title'] = 'Registration';
        return view('user/register', $data);
    }
    public function register_action(Request $request){
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:tb_user',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        $user = new User([
            'name'=>$request->name,
            'username'=>$request->username,
            'password'=>Hash::make($request->password)

        ]);
        $user->save();

        return redirect()->route('login')->with('success', 'Registration Success. Please Login...');
    }

    public function login(){
        $data['title'] = 'Login';
        return view('user/login', $data);
    }
    public function login_action(Request $request){
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){
            $request->session()->regenerate();
            return redirect('/');
        }
        $user->save();

        return back()->withError('password', 'Username and Password does not match.');
    }

    public function password(){
        $data['title'] = 'Change Password';
        return view('user/password', $data);
    }
    public function password_action(Request $request){
        $request->validate([
            'old_password' => 'required|current_password',
            'new_password' => 'required',
            'confirm_new_password' => 'required|same:new_password',
        ]);
        $user = User::find(Auth::id());
        $user->password = Hash::make($request->new_password);
        $user->save();
        $request->session()->regenerate();

        return back()->with('success', 'Password Change Successfully.');
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');

    }
}
