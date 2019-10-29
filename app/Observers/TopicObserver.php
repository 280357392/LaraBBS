<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    /*
     Eloquent 模型会触发许多事件（Event），我们可以对模型的生命周期内多个时间点进行监控：
     creating, created, updating, updated, saving,
    saved, deleting, deleted, restoring, restored。
    事件让你每当有特定的模型类在数据库保存或更新时，执行代码。
    当一个新模型被初次保存将会触发 creating 以及 created 事件。
    如果一个模型已经存在于数据库且调用了 save 方法，将会触发 updating 和 updated 事件。
    在这两种情况下都会触发 saving 和 saved 事件。
     */
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    //观察者类里的方法名对应 Eloquent 想监听的事件
    public function saving(Topic $topic)
    {
        //XSS过滤
        $topic->body = clean($topic->body, 'user_topic_body');
        //make_excerpt() 是我们自定义的辅助方法，我们需要在 helpers.php 文件中添加
        $topic->excerpt = make_excerpt($topic->body);
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {
            $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
        }
    }

    //当话题被删除的时候，数据库里的回复信息没有存在的价值，
    //只会占用空间。所以接下来我们将监听话题删除成功的事件，
    //在此事件发生时，我们会删除此话题下所有的回复
    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }

}