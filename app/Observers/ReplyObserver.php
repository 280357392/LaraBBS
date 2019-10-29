<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{

    public function updating(Reply $reply)
    {
        //
    }

    //话题下每新增一条回复+!
    public function created(Reply $reply)
    {
        $reply->topic->updateReplyCount();
        // 通知话题作者有新的评论
        $reply->topic->user->notify(new TopicReplied($reply));
    }

    //解决 XSS 安全威胁
    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content, 'user_topic_body');
    }

    //当回复被删除后，评论数已变更，话题的 reply_count 也需要更新
    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
    }


}