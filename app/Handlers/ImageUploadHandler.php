<?php

namespace App\Handlers;

use  Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

//工具类（utility class）是指一些跟业务逻辑相关性不强的类，图片上传相关的逻辑
class ImageUploadHandler
{
    // 只允许以下后缀名的图片文件上传
    protected $allowed_ext = ["png", "jpg", "gif", 'jpeg'];

    /**
     * @param $file 文件对象：$request->avatar
     * @param $folder 自定义目录名称
     * @param $file_prefix 自定义文件前缀名
     * @return array|bool 图片访问的链接\false(后缀名不合法)
     */
    public function save($file, $folder, $file_prefix,$max_width = false)
    {

        //存储目录
        // 构建存储的文件夹规则，值如：uploads/images/avatars/201709/21/
        // 文件夹切割能让查找效率更高。
        $folder_name = "uploads/images/$folder/" . date("Ym/d", time());


        //public目录 +  存储目录 $file->move($upload_path, 文件名);
        // 文件具体存储的物理路径，`public_path()` 获取的是 `public` 文件夹的物理路径。
        // 值如：/home/vagrant/Code/larabbs/public/uploads/images/avatars/201709/21/
        $upload_path = public_path() . '/' . $folder_name;//public + 图片存储的目录



        //文件后缀
        // 获取文件的后缀名，因图片从剪贴板里黏贴时后缀名为空，所以此处确保后缀一直存在
        $extension = strtolower($file->getClientOriginalExtension()) ?: 'png';


        //文件名
        // 拼接文件名加前缀是为了增加辨析度，前缀可以是相关数据模型的 ID
        // 值如：1_1493521050_7BVc9v9ujP.png
        //文件前缀名 + 时间戳 + 随机字符 + 文件后缀名
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) . '.' . $extension;


        // 如果上传的不是图片将终止操作（文件后缀名，文件名数组）
        if ( ! in_array($extension, $this->allowed_ext)) {
            return false;
        }


        // 将图片移动到我们的目标存储路径中（图片存储的目录地址包含public目录路径，文件名包含后缀名）
        $file->move($upload_path, $filename);


        // 如果传参了表示要限制图片宽度，就进行裁剪
        if ($max_width && $extension != 'gif') {

            // 此类中封装的函数，用于裁剪图片， public+ 文件目录 + 文件名   最大宽度
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }


        return [
            // 'url' => env('APP_URL', 'http://localhost'),
            //http://larabbs.test    /uploads/images/avatars/201910/25      /6_1571998312_9QQmJQQbCS.jpg
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    //裁剪图片
    public function reduceSize($file_path, $max_width)
    {
        // 先实例化，传参是文件的磁盘物理路径
        $image = Image::make($file_path);

        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}