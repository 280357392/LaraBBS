<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

//此控制器处理所有邮件认证相关逻辑
class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     * 构建函数里使用了三个中间件
     *
     * @return void
     */
    public function __construct()
    {
        //设定了所有的控制器动作都需要登录后才能访问
        $this->middleware('auth');
        //只有 verify 动作使用 signed 中间件进行认证
        $this->middleware('signed')->only('verify');
        //对 verify 和 resend 动作做了频率限制，限定了这两个动作访问频率是 1 分钟内不能超过 6 次
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
