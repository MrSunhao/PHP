<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 8:59
 */

namespace Admin\Model;


use Think\Model;

class NoticeModel extends Model
{
    /**
     * 添加公告 存入数据库
     * @param $data
     * @return mixed
     */
    public function addNotice($data)
    {
        return $this->data($data)->add();
    }

    /**
     * 按最新时间得到目标并按类型筛选
     * @param $type
     * @return mixed
     */
    public function getGoalByNewDate($type)
    {
        $maxId = $this->field('max(notice_id) id')->find();
        $where['notice_id'] = (Integer)$maxId['id'];
        if ($type === '跑步') {
            $res = $this->field('notice_run_data')->where($where)->find();
            return $res['notice_run_data'];
        } else {
            $res = $this->field('notice_play_time')->where($where)->find();
            return $res['notice_play_time'];
        }
    }

    /**
     * 得到目标 运动距离和运动时间
     * @return mixed
     */
    public function getGoals()
    {
        $maxId = $this->field('max(notice_id) id')->find();
        $where['notice_id'] = (Integer)$maxId['id'];
            $res = $this->field('notice_run_data,notice_play_time')->where($where)->find();
            return $res;
    }

    /**
     * 获得最近的公告信息
     * @return mixed
     */
    public function getNoticeInfo()
    {
        $maxId = $this->field('max(notice_id) id')->find();
        $where['notice_id'] = (Integer)$maxId['id'];
        $res = $this->where($where)->find();
        return $res;
    }

    /**
     *
     * @param $nid
     * @return mixed
     */
    public function getNoticeInfoById($nid)
    {
        $where['notice_id'] = $nid;
        return $this->where($where)->find();
    }

    /**
     * 根据user_id 获得公告信息
     * @param $page
     * @param $count
     * @param $notice_user_id
     * @return mixed
     */
    public function getNoticeByUid($page, $count, $notice_user_id)
    {
        $where['notice_user_id'] = $notice_user_id;
        return $this->page($page, $count)->where($where)->order('notice_time desc')->select();
    }

    /**
     * 获得所有的公告记录
     * @param $page
     * @param $count
     * @return mixed
     */
    public function getNotice($page, $count)
    {
        return $this->page($page, $count)->order('notice_time desc')->select();
    }

    /**
     * 根据user_id 获得公告的条数
     * @param $notice_user_id
     * @return mixed
     */
    public function getNoticeRowsByUid($notice_user_id)
    {
        $where['notice_user_id'] = $notice_user_id;
        return $this->where($where)->select();
    }

    /**
     * 修改公告
     * @param $updata
     * @return bool
     */
    public function upNotice($updata)
    {
        $where['notice_id'] = $updata['notice_id'];
        return $this->where($where)->save($updata);
    }

    /**
     * 根据id 删除公告
     * @param $ids
     * @return bool|mixed
     */
    public function delNoticeById($ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $res = true;
        for ($i = 0; $i < sizeof($ids); $i++) {
            $where['notice_id'] = $ids[$i];
            $res = $this->where($where)->delete();
            if ($res === false) {
                return false;
            } else {
                $res += $res;
            }
        }
        return $res;
    }

}