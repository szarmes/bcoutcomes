<?php

class UserController extends BaseController {

    /**
     * Show the profile for the given user.
     */
    public function login()
    {
       return View::make('users.login');
    }
    public function handleLogin(){
    	$data = Input::only(['email', 'password']);
    	$validator = Validator::make(
            $data,
            [
                'email' => 'required|email|min:5',
                'password' => 'required',
            ]
        );
        $remember = Input::get('remember');

        if($validator->fails()){
            return Redirect::route('login')->withErrors($validator)->withInput();
        }

        if(Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)){
            return Redirect::to('/');
        }
        $errors = 'Login Failed';
        return Redirect::route('login')->withErrors($errors)->withInput();
    }

    public function profile(){
    	return View::make('users.profile');

    }

    public function logout(){
    	if(Auth::check()){
		  Auth::logout();
		}
		 return Redirect::route('login');
    }

    public function create(){
    	return View::make('users.create');
    }

    public function store(){
    	$data = Input::only(['email','password','password_confirmation','user_type']);

    	$validator = Validator::make(
            $data,
            [
               
                'email' => 'required|email|min:5|unique:users',
                'password' => 'required|min:5|confirmed',
                'password_confirmation'=> 'required|min:5',
                'user_type'=>'not_in:0',
            ]
        );

		if($validator->fails()){
            return Redirect::route('user.create')->withErrors($validator)->withInput();
        }
        else{

	        $user = new User;
		    $user->email = Input::get('email');
		    $user->password = Hash::make(Input::get('password'));
		    $user->role = Input::get('user_type');
		    $user->save();

            // log the user in then redirect

            Auth::login($user);

            return Redirect::to('')->with('message', 'Thanks for registering! Refer to the tutorial link at the top if needed.');
	 
	    	//return Redirect::to('login')->with('message', 'Thanks for registering!');
	    }
    }

    public function admin(){

        if(Auth::user()->isSysAdmin())
        {
            //$users = User::all();
            //return View::make('users.admin', array('users' => $users));
            return View::make('users.admin');
        }
        else{
            return Redirect::to('');
        }
    }


}