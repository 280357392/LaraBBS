<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

//表单请求 UserRequest
//$ php artisan make:request UserRequest
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 权限验证
     *
     * @return bool
     */
    public function authorize()
    {
        //本课程中我们不会使用此功能，关于用户授权，我们将会在后面章节中使用更具扩展性的方案，此处我们 return true;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 规则规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            //唯一：在 users 数据表里的字段 name，自己排除在外
            'name' => 'required|between:3,25|regex:/^[A-Za-z0-9\-\_]+$/|unique:users,name,' . Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => '用户名已被占用，请重新填写',
            'name.regex' => '用户名只支持英文、数字、横杠和下划线。',
            'name.between' => '用户名必须介于 3 - 25 个字符之间。',
            'name.required' => '用户名不能为空。',
        ];
    }
}
