<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;

class TopicsController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function index(Request $request, Topic $topic)
    {
        //$topics = Topic::paginate(30);
        //解决 N + 1 问题
        //我们可以通过 Eloquent 提供的 预加载功能 来解决此问题
        //方法 with() 提前加载了我们后面需要用到的关联属性 user 和 category，并做了缓存
        //$topics = Topic::with('user', 'category')->paginate(30);

        $topics = $topic->withOrder($request->order)
            ->with('user', 'category')// 预加载防止 N+1 问题
            ->paginate(20);

        return view('topics.index', compact('topics'));
    }

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

    public function create(Topic $topic)
    {
        $categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        /*
        store() 方法的第二个参数，会创建一个空白的 $topic 实例
        $topic->fill($request->all()); fill 方法会将传参的键值数组填充到模型的属性中
        Auth::id() 获取到的是当前登录的 ID；
        $topic->save() 保存到数据库中。
         */
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
        return redirect()->route('topics.show', $topic->id)->with('success', '帖子创建成功！');

//		$topic = Topic::create($request->all());
//		return redirect()->route('topics.show', $topic->id)->with('message', 'Created successfully.');
    }

    public function edit(Topic $topic)
    {
        $this->authorize('update', $topic);
        return view('topics.create_and_edit', compact('topic'));
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return redirect()->route('topics.show', $topic->id)->with('message', 'Updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
    }

    public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success' => false,
            'msg' => '上传失败aaa!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['success'] = true;
                $data['msg'] = "上传成功!";
                $data['file_path'] = $result['path'];
                //http://larabbs.test/uploads/images/topics/201910/28/1_1572257054_yfR6n85di3.png
            }
        }
        return $data;
    }
}