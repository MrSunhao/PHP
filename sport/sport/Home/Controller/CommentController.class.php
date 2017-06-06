<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-9
 * Time: 下午7:19
 */

namespace Home\Controller;


use Think\Controller;

class CommentController extends Controller
{
    public function index()
    {
        $CommConn = D('Admin/Comment');
        $RepConn = D('Admin/Reply');
        $res = $CommConn->getTopicInfo();
        if ($res === false) {
            $this->error('查找错误！');
        }
        if ($res === null) {
            $this->error('没有这条动态！');
        }

        $commList = $CommConn->getCommListByTid();
        $repList = $RepConn->getRepList();
//        dump($commList);
//        dump($repList);
//        exit();
        $this->assign('Info', $res);
        $this->assign('commList', $commList);
        $this->assign('repList',$repList);
        $this->display('Index/comment');
    }

    /**
     * 发表评论
     */
    public function comm()
    {
        $user_id = $_SESSION['user']['user_id'];
        $tid = I('post.topic_id', 0, 'intval');
        if ($tid === 0) {
            $this->error('操作错误！');
        }
        if ($user_id === null) {
            $this->error('请登陆...', __MODULE__ . '/Login', 3);
        }
        $CommConn = D('Admin/Comment');
        $res = $CommConn->addComm();
        if ($res === 0) {
            $this->error('非法操作！');
        } else if ($res === false) {
            $this->error('评论失败！');
        } else if ($res === '') {
            $this->error('评论为空！');
        } else {
            $this->success('评论成功！', __MODULE__ . '/Comment/index/tid/' . $tid, 3);
        }
    }

    /**
     * 删除评论
     */

    public function del()
    {
        $CommConn = D('Admin/Comment');
        $commId = I('get.cid',0,'intval');
        if($commId === 0){
            $this->error('操作错误！');
        }
        $userId = $CommConn->field('comment_user_id')->where(['comment_id'=>$commId])->find();
        $tid = I('get.tid',0,'intval');

        if($userId['comment_user_id'] === $_SESSION['user']['user_id']){
            $res = $CommConn->delComById($commId);
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