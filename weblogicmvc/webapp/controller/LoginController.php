<?php
use ArmoredCore\WebObjects\View;
use ArmoredCore\WebObjects\Post;
use ArmoredCore\WebObjects\Redirect;
use ArmoredCore\WebObjects\Debug;

class LoginController extends \ArmoredCore\Controllers\BaseController
{
    public function getLoginForm()
    {
        return View::make('login.loginform');
    }

    public function doLogin()
    {
        $username = Post::get('username');
        $password = Post::get('password');
        $user = User::find_by_username_and_password($username,$password);

        if(!is_null($user))
        {
            $authmgr = new AuthManager();
            $authmgr->setLogin($user);

            $role = AuthManager::getRole();

            switch ($role)
            {

                case 'admin':
                    Redirect::toRoute('adminapp/index');
                    break;
                case 'passageiro':
                    Redirect::toRoute('passageiroapp/index');
                    break;
                case 'gestorvoo':
                    Redirect::toRoute('gestorvooapp/index');
                    break;
                case 'opcheckin':
                    Redirect::toRoute('opcheckinapp/index');
                    break;

                default:
                    Redirect::toRoute('login/login');
            }
        }else
        {
            Redirect::toRoute('login/login');
        }
    }

    public function getRegistrationForm()
    {
        return View::make('login.registrationform');
    }

    public function doRegistration()
    {
        //create new resource (activerecord/model) instance with data from POST
        //your form name fields must match the ones of the table fields
        $user = new User(Post::getAll());
        $user -> role='passageiro';

        if($user->is_valid()){
            $user->save();
            Redirect::toRoute('login/login');
        } else {
            //redirect to form with data and errors
            Redirect::flashToRoute('login/registration', ['user' => $user]);
        }
    }

    public function destroySession()
    {
        AuthManager::logout();
        Redirect::toRoute('home/index');
    }
}