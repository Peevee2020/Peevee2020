<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class MainController extends Controller
{
    function login(){
        return view('auth.login');
    }
    function register(){
        return view('auth.register');
    }
    function save(Request $request) {
        $request->validate([
            'username'=>'required|unique:users',
            'password'=>'required|min:4|max:20'
        ]);

        $user = new User;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $save = $user->save();

        if($save) {
            return back()->with('success', 'New User has been successfuly added ti database');
        }
        else {
            return back()->with('fail', 'Something went wrong, try again later');
        }
    }
    function check(Request $request) {
        $request->validate([
            'username'=>'required',
            'password'=>'required|min:4|max:20'
       ]);

       $userInfo = User::where('username','=', $request->username)->first();

       if(!$userInfo){
           return back()->with('fail','We do not recognize your email address');
       }else{
           if(Hash::check($request->password, $userInfo->password)){
               $request->session()->put('LoggedUser', $userInfo->id);
               return redirect('admin/dashboard');

           }else{
               return back()->with('fail','Incorrect password');
           }
       }
    }
    function logout(){
        if(session()->has('LoggedUser')){
            session()->pull('LoggedUser');
            return redirect('/auth/login');
        }
    }
}
