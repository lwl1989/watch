<?php
namespace App\Http\Controllers\Auth;

use App\Exceptions\ErrorConstant;
use App\Http\Controllers\Controller;
use App\Library\Auth\Encrypt;
use App\Library\Constant\Common;
use App\Library\RedisFacade;
use App\Library\StrParse;
use App\Models\Model;
use App\Services\AdminService;
use App\Services\VerifyCodeService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
	public function showLoginForm()
	{
	    if (empty(Cookie::get('device_uuid'))) {
	        Cookie::queue('device_uuid', Uuid::uuid4()->toString());
        }
        echo '<pre>';
        RedisFacade::pipeline(function($pipe) {
            $pipe->set('a', '123');
        });
	    var_dump(RedisFacade::exec());

	    $pipe = RedisFacade::pipeline();
	    var_dump($pipe);
	    $pipe->set('b', 'ccc');
	    var_dump($pipe->exec());
	    exit();
		return view('admin.login');
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		return 'account';
	}

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'verify'   => 'required|string',
            'device_uuid'   =>  'required|string'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
	public function login(Request $request)
	{
        $data = $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
            'verify'   => 'required|string',
            'device_uuid'   =>  'required|string'
        ]);

		$uuid = $request->post('device_uuid', Cookie::get('device_uuid'));

		if (!VerifyCodeService::verify($uuid, $request->post('verify'))) {
		    throw new \Exception('Unauthenticated. Verify code error!', ErrorConstant::USER_INPUT_VERIFY);
        }

		try{
            unset($data['verify']);
            unset($data['device_uuid']);

			if (Auth::attempt($data)) {
				return $this->sendLoginResponse($request);
			} else {
			    throw new \Exception('', ErrorConstant::USER_ACCOUNT_OR_PASSWORD_ERROR);
            }
		} catch (\Exception $exception) {
			throw $exception;
		}
	}

    /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages(array_merge($request->all(),[
            $this->username() => [trans('auth.failed')],
        ]));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
	public function sendLoginResponse(Request $request) : JsonResponse
    {
	    //這裏可以寫個多的邏輯
        /** @var Model $user */
        //$user = $this->guard()->user();
        $user = Auth::user();

        if ($user->getAttribute('deleted') != Common::NO_DELETE) {
            $request->session()->flush();

            throw new \Exception('The account has been deleted', ErrorConstant::USER_ACCOUNT_DELETED);
        }

        if ($user->getAttribute('status') != Common::STATUS_NORMAL) {
            $request->session()->flush();

            $code = ErrorConstant::USER_ACCOUNT_DISABLED;
            if ($user->getAttribute('role') == Common::ADMIN_ROLE_SHOP) {
                $code = ErrorConstant::USER_SHOP_DISABLED;
            }

            throw new \Exception('Account disabled', $code);
        }

        if(strpos($request->path(),'api/')===0) {
            unset($user->password);
            $user = $user->toArray();
            $permissions = StrParse::parseJsonDecode($user['permissions']);

            /**! 管理員對活動沒有管理權限 !**/
            if (!in_array('message_activity', $permissions)) {
                throw new \Exception('', ErrorConstant::USER_NO_HAS_PERMISSION);
            }

            return response()->json(['token'=> Encrypt::generateToken(['uid'=>$user['id'],'time'=>time()]),'user'=>$user]);
        }else{
            $router = AdminService::getUserAcl(
                $user->getAttributeValue('role'),
                $user->getAttribute('permissions')
            );

            $request->session()->regenerate();

            //$this->clearLoginAttempts($request);

            return response()->json(['router'=>$router, 'google_map_key'=> env('GOOGLE_MAP_KEY')])
                ->withCookie(Cookie::forever('router', json_encode($router),null,null,false,false))
                ->withCookie(Cookie::forever('google_map_key', env('GOOGLE_MAP_KEY'),null,null,false,false));
        }
	}

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        if(! $request->secure()) {
            return redirect()->secure('/');
        }
        return redirect('/');
    }
}
