<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 9:00
 */

namespace Admin\Model;


use Think\Model;

class ReplyModel extends Model
{
    /**
     * 添加回复
     * @param $reply_comment_id //评论id
     * @param $reply_end_user_id //被回复人id
     * @param $reply_content //回复内容
     */
    public function addReply($reply_comment_id, $reply_end_user_id, $reply_content)
    {
        $reply_start_user_id = $_SESSION['user']['user_id'];
        $reply_time = date('Y-m-d H:i:s');
        $data = array(
            'reply_comment_id' => $reply_comment_id,
            'reply_start_user_id' => $reply_start_user_id,
            'reply_end_user_id' => $reply_end_user_id,
            'reply_content' => $reply_content,
            'reply_time' => $reply_time
        );
        return $this->data($data)->add();
    }

    public function getRepList()
    {
        $list = $this->order('reply_time')->select();
        if (sizeof($list) === 0) {
            return null;
        }
        $UserConn = D('User');
        foreach ($list as $key => $value) {
            $comment_id = $value['reply_comment_id'];
            $reply_start_user_id = $value['reply_start_user_id'];
            $reply_end_user_id = $value['reply_end_user_id'];
            $comment_content = $this->table('comment')->field('comment_content')->where(['comment_id' => $comment_id])->find();
            $startU = $UserConn->field('user_number,user_name,user_class')->where(['user_id' => $reply_start_user_id])->find();
            $endName = $UserConn->field('user_name')->where(['user_id' => $reply_end_user_id])->find();
            $value['comment_content'] = $comment_content['comment_content'];
            $value['user_number'] = $startU['user_number'];
            $value['user_name'] = $startU['user_name'];
            $value['user_class'] = $startU['user_class'];
//            $value['reply_start_user_name'] = $startU['user_name'];
            $value['reply_end_user_name'] = $endName['user_name'];
            $list[$key] = $value;
        }
        return $list;
    }

    /**
     * 删除回复
     * @param $repId
     */
    public function delRepById($rId)
    {
        if ($rId === -1) {
            return -1;
        }
        if (!is_array($rId)) {
            $rId = array($rId);
        }
        $res = true;
        for ($i = 0; $i < sizeof($rId); $i++) {
            $res = $this->where(['reply_id' => $rId[$i]])->delete();
            if($res === false){
                return false;
            }else{
                $res +=$res;
            }
        }
        return $res;
    }

    public function delRepBycId($cId)
    {
        if ($cId === -1) {
            return -1;
        }
        if (!is_array($cId)) {
            $cId = array($cId);
        }
        $res = true;
        for ($i = 0; $i < sizeof($cId); $i++) {
            $res = $this->where(['reply_id' => $cId[$i]])->delete();
            if($res === false){
                return false;
            }else{
                $res +=$res;
            }
        }
        return $res;
    }
}