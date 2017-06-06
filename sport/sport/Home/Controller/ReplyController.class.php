<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-10
 * Time: 下午3:56
 */

namespace Home\Controller;


use Think\Controller;

class ReplyController extends Controller
{
    public function index()
    {
        $userId = $_SESSION['user']['user_id'];

        if ($userId === null) {
            $this->error('请登录...', __MODULE__ . '/Login', 3);
        } else {

            $reply_end_user_id = I('post.endUId', 0, 'intval');
            if ($reply_end_user_id === 0) {
                $this->error('非法操作！');
            }
            if ((integer)$userId === $reply_end_user_id) {
                $this->error('不能回复自己！');
            }
            $tid = I('post.tid', 0, 'intval');
            if($tid === 0){
                $this->error('非法操作！');
            }
            $reply_comment_id = I('post.commId', 0, 'intval');
            if ($reply_comment_id === 0) {
                $this->error('非法操作');
            }

            $reply_content = I('post.reply_content', '', 'strip_tags');
            if ($reply_content === '') {
                $this->error('回复不能为空！');
            }
            $RepConn = D('Admin/Reply');
            $res = $RepConn->addReply($reply_comment_id, $reply_end_user_id, $reply_content);
            if ($res === false){
                $this->error('操作错误！');
            }
            $this->redirect('/Home/Comment/index/tid/'.$tid,'页面跳转中...');
        }
    }

    public function del(){
        $CommConn = D('Admin/Reply');
        $repId = I('get.rid',0,'intval');
        if($repId === 0){
            $this->error('操作错误！');
        }
        $userId = $CommConn->field('reply_start_user_id')->where(['reply_id'=>$repId])->find();
        $tid = I('get.tid',0,'intval');

        if($userId['reply_start_user_id'] === $_SESSION['user']['user_id']){
            $res = $CommConn->delRepById($repId);
            if ($res === -1 || $tid === 0) {
                $this->error('操作错误！');
            } else if ($res === false) {
                $this->error('删除失败！');
            } else if ($res === 0) {
                $this->error('没有删除任何项');
            }else{
                $this->success('删除成功！',__MODULE__ . '/Comment/index/tid/' . $tid,3);
            }
        }else{
            $this->error('非法操作！');
        }
    }
}