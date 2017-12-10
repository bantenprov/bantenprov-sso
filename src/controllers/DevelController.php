<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth, Redirect, Validator;
use App\User;
use Hash, Session;
use Bantenprov\BantenprovSso\BantenprovSso as BantenprovSso;


class DevelController extends Controller
{
    public function login()
    {
    	if(!Auth::check())
    	{
    		return view('pages.credential.login');
    	}
    	return Redirect::to('dashboard');
    }

    public function post_login(Request $request)
    {
    	$validator = Validator::make($request->all(), 
    		[
    			'email'			=> 'required|email',
    			'password'		=> 'required'
    		]);

    	if($validator->fails())
    	{
    		Session::flash('message', 'Data tidak boleh kosong');
    		return redirect()->back()
                ->withErrors($validator)
                ->withInput();
    	}
    	$credential = [
    	'email'			=> $request->input('email'), 
    	'password'		=> $request->input('password'),
    	'ipaddress'		=> $request->input('ip1').'-'.$request->input('ip2')
    	];

    	//set session 
    	Session(['ipaddress' => $request->input('ip1').'-'.$request->input('ip2')]);

    	if(!BantenprovSso::Attempt($credential))
    	{
    		//dd(BantenprovSso::message());
    		Session::flash('message', 'terjadi kesalah, login tidak berhasil');
    		return redirect()->back()
                ->withErrors(BantenprovSso::message())
                ->withInput();
    	}
    	//dd(BantenprovSso::data());
    	$data = BantenprovSso::data();
    	//check data user pada table user 
    	$user = User::where('email', $data->email)
    			->first();
    	if(count($user) == 0)
    	{
    		//return 'gak ada';
    		//insert data user
    		$create_user = new User;
    		$create_user->email 		= $data->email;
    		$create_user->name 			= $data->name;
    		$create_user->password 		= $data->password;
    		$create_user->save();

    		return Self::init_login($create_user);
    	}
    	else
    	{
    		return Self::init_login($user);
    	}

    }

    public function init_login($data)
    {
    	//login with id
    	//dd($data->id);
    	if(Auth::loginUsingId($data->id))
    	{
    		return redirect::to('dashboard');

    	}
    	else
    	{
    		//false
    		return Redirect::to('login');
    	}


    }

    public function check_logout(Request $request)
    {
    	if(BantenprovSso::check_logout(['ipaddress' => $request->input('ipaddress')]))
    	{
    		return 1;
    	}
    	else
    	{
    		return 0;
    	}
    }

    public function check_login(Request $request)
    {
    	$check = BantenprovSso::check_login(['ipaddress' => $request->input('ipaddress')]);
    	if(!$check)
    	{
    		return 0;
    	}
    	else
    	{
    		// cari atau simpan data baru
    		$teng = BantenprovSso::check_login_data();
    		$user_data = User::where('email', $teng->email)->first();
    		if(count($user_data) == 0)
    		{
    			//simpan data baru
    			$simpan = new User;
    			$simpan->email 			= $teng->email;
	    		$simpan->name 			= $teng->name;
	    		$simpan->password 		= 'bantenprov';
	    		$simpan->save();

	    		Auth::loginUsingId($simpan->id);
	    		return 1;
    		}
    		else
    		{
    			Auth::loginUsingId($user_data->id);
	    		return 1;
    		}
    	}
    }

    public function cas_logout()
    {
    	Auth::logout();
    	Session()->forget('ipaddress');
    	return 1;
    }

   	public function logout()
    {
    	Auth::logout();
    	BantenprovSso::Logout(['ipaddress' => Session::get('ipaddress')]);
    	Session()->forget('ipaddress');
    	return Redirect::to('/login');
    }

}
