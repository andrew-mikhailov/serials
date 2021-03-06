<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class LoginAdminController extends Controller
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;


    public function showLogin(Request $request)
    {
        if ($request->user() != null) {
            // validation is successful, send back to form
            return Redirect::to(route('admin.dashboard'));
        }
        return View::make('login');
    }

    public function adminLogin(Request $request)
    {
        // validate the info, create rules for the inputs
        $rules = [
            'email'    => 'required|email', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:5' // password can only be alphanumeric and has to be greater than 3 characters
        ];

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('admin/login')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            // create our user data for the authentication
            $userdata = array(
                'email'     => Input::get('email'),
                'password'  => Input::get('password')
            );

            // attempt to do the login
            if (Auth::attempt($userdata)) {
                // validation successful!
                // redirect them to the secure section or whatever
                // return Redirect::to('secure');
                // for now we'll just echo success (even though echoing in a controller is bad)
                return Redirect::to(route('admin.dashboard'));

            } else {
                // validation not successful, send back to form
                return Redirect::to(route('admin.login'));
            }
        }
    }

    public function adminLogout(Request $request) {
        Auth::logout();
        return Redirect::route('admin.login');
    }
}
