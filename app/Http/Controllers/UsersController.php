<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
    //如果数据库中找不到对应的模型实例，会自动生成 HTTP 404 响应
    public function show(User $user)
    {
        //待处理：1.必须登录；2.设置为只能看自己的个人资料
        return view('users.show', compact('user'));
    }

    public function edit(User $user){
        return view('users.edit', compact('user'));
    }

    //使用了表单请求验证
    //只有当验证通过时，才会执行 控制器 update()
    public function update(UserRequest $request,ImageUploadHandler $uploader,User $user){
        $data = $request->all();
        if ($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
