<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 9:00
 */

namespace Admin\Model;


use Think\Model;

class CommentModel extends Model
{
    /**
     * 得到主题信息 组织用户信息（头像）
     */
    public function getTopicInfo()
    {
        $userConn = D('Admin/User');
        $TicConn = D('Admin/Topic');

        //获得主题信息
        $TopicInfo = $TicConn->getSportInfoById();

        if ($TopicInfo === false) {
            return false;
        }
        if ($TopicInfo === null) {
            return null;
        }

        $user_id = $TopicInfo['topic_user_id'];
        $where['user_id'] = $user_id;

        //获得名字和头像
        $userInfo = $userConn->where($where)->field('user_name,user_image')->find();

        //组织评论页面需要的数据
        $Info = array(
            'user_id' => $user_id,
            'user_name' => $userInfo['user_name'],
            'user_image' => $userInfo['user_image'],
            'topic_id' => $TopicInfo['topic_id'],
            'topic_posted_time' => $TopicInfo['topic_posted_time'],
            'topic_play_type' => $TopicInfo['topic_play_type'],
            'topic_play_data' => $TopicInfo['topic_play_data'],
            'topic_play_image' => $TopicInfo['topic_play_image'],
            'topic_content' => $TopicInfo['topic_content']
        );
        return $Info;
    }

    /**
     *添加评论  从客户端获得 comment_topic_id  comment_content
     * 通过session 获得评论人的id comment_user_id
     * 通过date函数获得评论时间 date('Y-m-d H:i:s') comment_time
     */
    public function addComm()
    {
        $comment_topic_id = I('post.topic_id', 0, 'intval');
        if ($comment_topic_id === 0) {
            return 0;
        }
        $comment_content = I('post.comment_content', '', 'strip_tags');
        if ($comment_content === '') {
            return '';
        }
        $comment_user_id = $_SESSION['user']['user_id'];
        $comment_time = date('Y-m-d H:i:s');

        $data = array(
            'comment_topic_id' => $comment_topic_id,
            'comment_user_id' => $comment_user_id,
            'comment_content' => $comment_content,
            'comment_time' => $comment_time
        );

        $res = $this->data($data)->add();
        return $res;
    }

    /**
     * 得到所有的评论 根据动态id
     */
    public function getCommListByTid()
    {
        $comment_topic_id = I('get.tid', 0, 'intval');
        $where['comment_topic_id'] = $comment_topic_id;
        $commList = $this->where($where)->select();
        if (sizeof($commList) === 0) {
            return null;
        }
        $UserConn = D('Admin/User');
        foreach ($commList as $key => $value) {
            $user_id = $value['comment_user_id'];
            $where['user_id'] = $user_id;
            $comment_user_name = $UserConn->field('user_name')->where($where)->find();
            $value['comment_user_name'] = $comment_user_name['user_name'];
            $commList[$key] = $value;
        }
        return $commList;
    }

    /**
     * 得到评论条数
     */
    public function getCountComm()
    {
        return $this->field('comment_topic_id,count(comment_id)cnt')->group('comment_topic_id')->select();
    }

    /**
     * 删除评论 根据comment_id
     */
    public function delComById($ids)
    {
        if ($ids === -1) {
            return -1;
        }
        $res = true;
        $rrs = true;
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $rConn = D('Admin/Reply');
        $rrs = $rConn->delRepBycId($ids);


        if ($rrs === false) {
            return $rrs;
        } else {
            $rrs += $rrs;
        }
        for ($i = 0; $i < sizeof($ids); $i++) {
            $where['comment_id'] = $ids[$i];
            $res = $this->where($where)->delete();
            if ($res === false) {
                return $res;
            } else {
                $res += $res;
            }
        }
        return $res;
    }

    /**
     * 删除评论根据topic_id
     * @param $ids
     */
    public function delComByTid($ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $res = true;
        for ($i = 0; $i < sizeof($ids); $i++) {
            $where['comment_topic_id'] = $ids[$i];
            $res = $this->where($where)->delete();
            if ($res === false) {
                return $res;
            } else {
                $res += $res;
            }
        }
        return $res;
    }

    public function delRepBycId($ids)
    {
        if ($ids === -1) {
            return -1;
        }
        $cids = null;
        $cidarr = null;
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $cConn = D('Admin/Comment');
        for ($i = 0; $i < sizeof($ids); $i++) {
            $cids = $cConn->field('comment_id')->where(['comment_topic_id' => $ids[$i]])->select();
            foreach ($cids as $key => $value) {
                $cidarr[$key] = $value['comment_id'];
            }
        }
        return $this->delComById($cidarr);
    }

    /**
     * 得到所有的评论 分页查询
     * @param $page
     * @param $count
     * @param $number
     * @param $name
     * @param $date
     */
    public function getAllComm($page, $count, $number, $name, $date)
    {
        if ($number === '') {
            $number = null;
        }
        if ($name === '') {
            $name = null;
        }
        if ($date === '') {
            $date = null;
        }

        $sql = null;
        if ($date === null) {
            if ($number === null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else if ($number === null && $name !== null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else if ($number !== null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_number = '" . $number . "' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND user.user_number = '" . $number . "' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            }
        } else {
            if ($number === null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else if ($number === null && $name !== null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else if ($number !== null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_number = '" . $number . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            } else {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND user.user_number = '" . $number . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time DESC LIMIT " . $page . "," . $count;
            }
        }
        return $this->query($sql);
    }

    /**
     * 得到评论条数
     * @param $number
     * @param $name
     * @param $date
     */
    public function getRows($number, $name, $date)
    {
        if ($number === '') {
            $number = null;
        }
        if ($name === '') {
            $name = null;
        }
        if ($date === '') {
            $date = null;
        }

        $sql = null;
        if ($date === null) {
            if ($number === null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id ORDER BY topic.topic_id,comment.comment_time ";
            } else if ($number === null && $name !== null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' ORDER BY topic.topic_id,comment.comment_time ";
            } else if ($number !== null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_number = '" . $number . "' ORDER BY topic.topic_id,comment.comment_time ";
            } else {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND user.user_number = '" . $number . "' ORDER BY topic.topic_id,comment.comment_time ";
            }
        } else {
            if ($number === null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time ";
            } else if ($number === null && $name !== null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time ";
            } else if ($number !== null && $name === null) {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_number = '" . $number . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time ";
            } else {
                $sql = "SELECT comment_id,user_number,user_name,user_class,topic_play_image,topic_content,comment_content,comment_time FROM user,topic,comment WHERE user.user_id = comment.comment_user_id AND comment.comment_topic_id=topic.topic_id AND user.user_name = '" . $name . "' AND user.user_number = '" . $number . "' AND comment.comment_time LIKE '" . $date . "%' ORDER BY topic.topic_id,comment.comment_time ";
            }
        }

        return sizeof($this->query($sql));
    }
}