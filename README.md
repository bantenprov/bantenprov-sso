Welcome to BantentprovSso!
===================


Package ini adalah pakage yang di gunakan untuk proses development di beberapa aplikasi untuk kepetingan pemeritan provinsi banten, dimana konsep dari beberapa aplikasi yang akan di bangun adalah menggunakan single sign on (authentikasi terpusat).

----------

Documents
-------------

Baik sebelum menggunakan package ini ada beberapa yang harus di perhatikan dan bahkan harus sangat di perhatikan karena jika terjadi kesalahan atau kekurangan spesifkasi yang di minta hal ini bisa menimbulkan beberapa fiture tidak bisa berjalan dengan normal


> **Note:**

> - Konsep single sign on yang di kembangkan dalam package ini ada lah single singn on dengan mengunakan http request dengan satu domain profile yang bekerja sebagai server untuk client dan menyabukannya dengan server cas.
> - Package Sso memdeteksi client berdasarkan ip address dan local ip address, sehingga ketika ada client yang menggunakan sso dan di tab lain memanggi aplikasi sso lainnya maka package ini akan melakukan registrasi dan login secara otomatis.
> - Package BantenprovSso hanya compatibel dengan package **advancetrus** atau **laratrus** dan package **auth** bawaan laravel dan tidak compatible ke package authentikasi yang lainnya.
> Package ini HANYA bisa beroperasi dengan baik pada browser **Firefox** dan **Chrome**.
> - Harap baca dan jalan dokumentasi ini dengan sesama agar proses penerapan package bisa berjalan dengan baik.


#### <i class="icon-eye"></i> Demo Aplikasi

silahkan daftarkan accout anda di aplikasi bantenprov profile
http://profil-01.dev.bantenprov.go.id/ 

kemudia lakukan login di aplikasi ini

1. aplikasi client sso satu
http://client_satu.bangunbanten.com/login

2. Aplikasi client sso dua
http://client_dua.bangunbanten.com/login


#### <i class="icon-magic"></i> Install Pacakge

// jalankan perintah composer ini untuk menginsatall package pada project anda
```
composer require Bantenprov/BantenprovSso : dev-master
```

#### <i class="icon-wrench"></i> tambahkan file config di .env

Tambahkan beberapa baris code berikut pada file .env
```
APPID=YOUR_APP_ID
TOKEN=YOUR_TOKEN
SSO_LOGIN=http://profil-01.dev.bantenprov.go.id/cas/v1/login
CHECK_LOGIN=http://profil-01.dev.bantenprov.go.id/cas/v1/check_login
CHECK_LOGOUT=http://profil-01.dev.bantenprov.go.id/cas/v1/check_logout
SSO_LOGOUT=http://profil-01.dev.bantenprov.go.id/cas/v1/logout
```

#### <i class="icon-user"></i> Registrasi Applikasi dan dapatkan app token anda

appid dan token adalah creadential key untuk aplikasi anda supaya bisa melakukan request ke bantenprov sso server
untuk melakukan registrasi apps anda silahkan ada daftar di halaman 
http://profil-01.dev.bantenprov.go.id/
dan masuk ke page developer page dan daftarkan aplikasi anda disana maka anda akan segera mendapaktan appid dan token untuk aplikasi anda.


#### <i class="icon-edit"></i> Update file Config/app.php

Tambahkan beberapa baris code berikut pada file config/app.php
```
Bantenprov\BantenprovSso\BantenprovSsoServiceProvider::class,
```

#### <i class="icon-upload"></i> jalankan Perintah Artisan

jalankan perinta berikut pada teriminal anda untuk mempulish beberapa rote, controller dan js assest file
```
composer dump-autoload
php artisan vendor:publish
php artisan bantenprov-sso:add-route
```

#### <i class="icon-edit"></i> Tambahkan code meta

Tambahkan code meta dan javascript berikut pada halaman login atau halaman yang ada angga sebagai default page user ketika user dalam keadaan tidak login atau page yang berisi login form

```
  <meta name="ipaddress" id="locale" content="{{Session::get('ipaddress')}}" />
  <meta name="check_logout_url" id="locale" content="{{'cas/check_logout'}}" />
  <meta name="check_login_url" id="locale" content="{{'cas/check_login'}}" />
  <meta name="_token" id="locale" content="{{csrf_token()}}" />
  <meta name="logout_url" id="locale" content="{{url('logout')}}" />
  <meta name="auth_check" id="locale" content="{{(Auth::Check())?'1':'0'}}" />
  <meta name="current_url" id="locale" content="{{Request::fullUrl()}}" />
  <meta name="auth_page" id="locale" content="{{url('dashboard')}}" />

  <script type="text/javascript">
    var init_address = '{{ BantenprovSso::InitAddress() }}';
    //alert(init_address);
  </script>
```

#### <i class="icon-edit"></i> Include Data .js pada beberapa file anda.

Package BantenSso ini selain menggunakan beberapa module php juga sangat membutuhkan beberapa script javascript untuk menjalankan beberapa core modulenya.
jadi tambahkan script ini pada file login anda atau file default ketika user tidak login

```
<script type="text/javascript" src="{{ asset('js/init_ipaddress.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/check_login.js') }}"></script>
```

#### <i class="icon-file"></i>Satu hal penting lagi, tambahkan element ini pada form login anda

untuk menggrab data ip address customer sistem perlu mengirim data ip via input page, yang nantinya akan di store ke sso server dan di detect sebagai key untuk next login dari aplikasi lain, 


tambahkan ini pada top file login page anda.

```
<?php 
use Bantenprov\BantenprovSso\BantenprovSso as BantenprovSso;
?>
```
berikutnya tambahkan script berikut pada form login anda. sehingga element ini akan di post bersamaan dengan post credential data customer.
```
<div class="hide">
<input type="text" class="ip2" name="ip2" value="{{BantenprovSso::InitAddress()}}">
<input type="text" name="ip1" class="ip1" id="list"/>
</div>
```

#### <i class="icon-edit"></i>Update method controller aplikasi anda yang di ginakan untuk action login form

pada dasarnya penggunakaan package sso ini adalah mengalikan data credetial yang ada buat dengan menanyakan data credentialnya ke server sso yang telah di sediakan. jadi untuk metode adavatrust yang develop ada beberapa perubahan untuk handling proses data authentikasi
berikut contoh methode yang telah menggukanan bantenprovSso untuk authentikasi
```
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
                'email'         => 'required|email',
                'password'      => 'required'
            ]);

        if($validator->fails())
        {
            Session::flash('message', 'Data tidak boleh kosong');
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $credential = [
        'email'         => $request->input('email'), 
        'password'      => $request->input('password'),
        'ipaddress'     => $request->input('ip1').'-'.$request->input('ip2')
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
            $create_user->email         = $data->email;
            $create_user->name          = $data->name;
            $create_user->password      = $data->password;
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
                $simpan->email          = $teng->email;
                $simpan->name           = $teng->name;
                $simpan->password       = 'bantenprov';
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

    public function test()
    {
        return BantenprovSso::init();
    }
}

```

> **Tip:** Mohon baca dan jalankan dokumentasi ini dengan baik dan benar agar proses impementasi berjalan dengan sempurna.

----------


Happy Coding
-------------------
Selamat mencoba semoga berhasil

> **Note:**

> - Package ini masih dalam tahap pengembagan, mohon infonya jika tejadi bug atau kekurangan data lain.
> Pengembang bisa di hubungi di 085711511295 atau drop email ke ahmadorin@gmail.com



### Support StackEdit

[![](https://cdn.monetizejs.com/resources/button-32.png)](https://monetizejs.com/authorize?client_id=ESTHdCYOi18iLhhO&summary=true)

  [^stackedit]: [StackEdit](https://stackedit.io/) is a full-featured, open-source Markdown editor based on PageDown, the Markdown library used by Stack Overflow and the other Stack Exchange sites.


  [1]: http://math.stackexchange.com/
  [2]: http://daringfireball.net/projects/markdown/syntax "Markdown"
  [3]: https://github.com/jmcmanus/pagedown-extra "Pagedown Extra"
  [4]: http://meta.math.stackexchange.com/questions/5020/mathjax-basic-tutorial-and-quick-reference
  [5]: https://code.google.com/p/google-code-prettify/
  [6]: http://highlightjs.org/
  [7]: http://bramp.github.io/js-sequence-diagrams/
  [8]: http://adrai.github.io/flowchart.js/
