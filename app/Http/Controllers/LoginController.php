<?php

namespace App\Http\Controllers;

use App\Models\AdminService;
use App\Models\Auth\StaffService;
use Illuminate\Http\Request;

use Auth;
use Session;

class LoginController extends Controller {

    public function index(Request $request) {
        return view('login');
    }

    public function login(Request $request) {
        $user = $request->input('user', '');
        $password = $request->input('password', '');

        $staff = new StaffService();
        $res = $staff->loginClick($user, $password);
        if (!empty($res['name'])) {
            Session::put('user', ['id' => $res['id'], 'name' => $res['name']]);
            Auth::login($res);
            return redirect('/');
        }
        return redirect('/login');
    }

    public function logout(Request $request) {
        $request->session()->reflash();
        return redirect('/login');
    }
}

