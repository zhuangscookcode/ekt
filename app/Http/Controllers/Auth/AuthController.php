<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
     	$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

    public function getLogin(Request $request){
        if(session_status()==1){
            session_start();
        }
        if(!Auth::guest()){
            return redirect('/');
        }

        return view()->exists('auth.authenticate') ? view('auth.authenticate') :view('auth.login');
    }

    /*public function postLogin(Request $request)
    {
        $this->validate($request, [
            $this->loginUsername() => 'required', 'password' => 'required',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles) {
            $this->incrementLoginAttempts($request);
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getFailedLoginMessage(),
            ]);
    }*/

    public function postLogin(Request $request){
        $credentials = $this->getCredentials($request);
        $errors = array();
        if(!$credentials["email"]){
            //$errors["email"] = "「メール　アドレス」を入力してください。";
            $errors["email"] = "「社員番号」を入力してください。";
        }
        if(!$credentials["password"]){
            $errors["password"] = "「パスワード」を入力してください。";
        }

        if(sizeof($errors)==0){
            if($this->attempt($credentials, $request->has('remember'))){
                return redirect('/');
            }
            //$errors["check"] = "「メール　アドレス」と「パスワード」は正しくない。";
            $errors["check"] = "「社員番号」と「パスワード」は正しくない。";
        }

        return redirect($this->loginPath())
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout(){
        if(session_status()==1){
            session_start();
        }
        $this->auth->logout();
        $_SESSION["userinfo"] = null;
        session_destroy();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/auth/login');
    }

    private function attempt(array $credentials = [], $remember = false, $login = true)
    {
        //$this->auth->attempt($credentials, $remember, $login);


        /*
        $userinfo = DB::select("select "
            ."u.id, u.name, u.email, u.password "
            .",r.id rid,r.name rname"
            .",p.id pid,p.name pname"
            ." from users u"
            ." left join role_user ru on u.id = ru.id"
            ." left join roles r on r.id = ru.role_id"
            ." left join permission_role pr on r.id = pr.role_id"
            ." left join permissions p on p.id = pr.permission_id"
            ." where u.email='".$credentials["email"]."'"
            ." and u.password='".$credentials["password"]."'"
        );
        */

        $userinfo = DB::select("select "
            ."users.id, users.name, users.email, users.password "
            .",userinfo.permission_id pid"
            .",userinfo.DIVISION UD"
            ." from users"
            ." left join userinfo on users.email = userinfo.id"
            ." where users.email='".$credentials["email"]."'"
            ." and users.password='".$credentials["password"]."'"
        );

        if(session_status()==1){
            session_start();
        }


        if(sizeof($userinfo)>0){
            $_SESSION["userinfo"] = get_object_vars($userinfo[0]);
            Auth::loginUsingId(get_object_vars($userinfo[0])["id"]);
            return true;
        }

        return false;
    }

}
