<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 8:59
 */

namespace Admin\Model;


use Org\Util\Date;
use Think\Model;

class TopicModel extends Model
{
    /**
     * @param $topic_id
     * @return mixed 删除个人动态
     */
    public function delOwn($ids)
    {
        $res = true;
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        $cConn = D('Admin/Comment');
        $c_res = $cConn->delComByTid($ids);
        if ($c_res === false) {
            return false;
        } else {
            $c_res += $c_res;
        }
        for ($i = 0; $i < sizeof($ids); $i++) {
            $img = $this->where(['topic_id' => $ids[$i]])->field('topic_play_image')->find();
            if ($img != null) { //删除发表过的图片
                $filePath = _SPORTIMGPATH_ . 'sportImg/' . $img['topic_play_image'];
                unlink($filePath);
            }
            $res = $this->where(['topic_id' => $ids[$i]])->delete();
            if ($res === false) {
                return $res;
            } else {
                $res += $res;
            }
        }
        return $res + $c_res;
    }

    /**
     * 查询得到运动数据
     */
    public function getSportList($page = 1, $number = 5)
    {
        $userU = D('Admin/User');
        $commConn = D('Admin/Comment');
        $commCntList = $commConn->getCountComm();
        $list = $this->order('topic_posted_time desc')->page($page, $number)->select();
        foreach ($list as $key => $value) {
            $user_id = $value['topic_user_id'];
            $info = $userU->field('user_name,user_image')->where(['user_id' => $user_id])->find();
            $value['user_name'] = $info['user_name'];
            $value['user_image'] = $info['user_image'];
            foreach ($commCntList as $ckey => $cvalue) {
                if ($cvalue['comment_topic_id'] === $value['topic_id']) {
                    $value['comm_cnt'] = (integer)$cvalue['cnt'];
                    break;
                } else {
                    $value['comm_cnt'] = 0;
                }
            }
            $list[$key] = $value;
        }
        return $list;
    }

    /**
     * 传入当前页及要查询的条数
     * @param $page $number
     * 查询得到个人运动记录
     */
    public function getSportOwnList($page = 1, $number = 5, $uid)
    {
        $userU = D('Admin/User');
        //$where['topic_user_id'] = $_SESSION['user']['user_id'];
        $where['topic_user_id'] = $uid;
        $commConn = D('Admin/Comment');
        $commCntList = $commConn->getCountComm();

        $list = $this->where($where)->order('topic_posted_time desc')->page($page, $number)->select();
        foreach ($list as $key => $value) {
            $user_id = $value['topic_user_id'];
            $info = $userU->field('user_name,user_image')->where(['user_id' => $user_id])->find();
            $value['user_name'] = $info['user_name'];
            $value['user_image'] = $info['user_image'];
            foreach ($commCntList as $ckey => $cvalue) {
                if ($cvalue['comment_topic_id'] === $value['topic_id']) {
                    $value['comm_cnt'] = (integer)$cvalue['cnt'];
                    break;
                } else {
                    $value['comm_cnt'] = 0;
                }
            }
            $list[$key] = $value;
        }
        return $list;
    }

    public function getSportInfoById()
    {
        $topic_id = I('get.tid', -1, 'intval');
        if ($topic_id === -1) {
            $topic_id = 1;
        }
        $where['topic_id'] = $topic_id;
        $res = $this->where($where)->find();
        return $res;
    }

    public function getSportInfoByType($type, $p, $row)
    {
        $sql = "SELECT topic_id,topic_user_id,topic_play_type,sum(topic_play_data) topic_play_data FROM topic WHERE topic_play_type = '" . $type . "' AND to_days(topic_posted_time) = to_days(now()) GROUP BY topic_user_id ORDER BY topic_play_data desc LIMIT " . ($p - 1) . "," . $row;
        return $this->query($sql);
    }

    //返回按类型查询的总条数
    public function getTotalpages($type)
    {
        $sql = "SELECT topic_id,topic_user_id,topic_play_type,sum(topic_play_data) topic_play_data FROM topic WHERE topic_play_type = '" . $type . "' AND to_days(topic_posted_time) = to_days(now()) GROUP BY topic_user_id ORDER BY topic_play_data desc";
        return $this->query($sql);
    }

    public function getAllTopicInfo($page, $count, $number, $name, $type)
    {
        if ($number === '') {
            $number = null;
        }
        if ($name === '') {
            $name = null;
        }
        if ($type === null || $type === '') {
            $type = 'All';
        }

        if ($type === 'All') {
            if ($number === null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            } else if ($number !== null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;

            } else if ($number === null && $name !== null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_name='" . $name . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            } else {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' AND user.user_name='" . $name . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            }
        } else {
            if ($number === null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            } else if ($number !== null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;

            } else if ($number === null && $name !== null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_name='" . $name . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            } else {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "AND user.user_name='" . $name . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC LIMIT " . $page . "," . $count;
            }
        }
        return $this->query($sql);
    }

    public function getRows($number, $name, $type)
    {
        if ($number === '') {
            $number = null;
        }
        if ($name === '') {
            $name = null;
        }
        if ($type === null || $type === '') {
            $type = 'All';
        }

        if ($type === 'All') {
            if ($number === null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            } else if ($number !== null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";

            } else if ($number === null && $name !== null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_name='" . $name . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            } else {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' AND user.user_name='" . $name . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            }
        } else {
            if ($number === null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            } else if ($number !== null && $name === null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";

            } else if ($number === null && $name !== null) {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_name='" . $name . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            } else {
                $sql = "SELECT topic_id,user.user_number,user.user_name,user.user_class,topic_play_type,topic_play_data,topic_posted_time,topic_content,topic_play_image FROM user,topic WHERE topic.topic_user_id=user.user_id AND user.user_number='" . $number . "AND user.user_name='" . $name . "' AND topic.topic_play_type='" . $type . "' ORDER BY topic.topic_play_type DESC,topic.topic_posted_time DESC ";
            }
        }
        return $this->query($sql);
    }

    //得到各类型运动人数
    public function sport_distribution()
    {
        //  $sql = "SELECT COUNT(*) sval,topic_play_type stype FROM topic GROUP BY topic_play_type";
        $sql = "SELECT COUNT(*) sval,topic_play_type stype FROM (SELECT topic_play_type FROM topic GROUP BY topic_play_type,topic_user_id) as tb GROUP BY topic_play_type";
        return $this->query($sql);
    }

    //得到每天运动统计
    public function day_info($page, $rows, $date)
    {
        $stime = null;
        $sdata = null;
        $user = null;
        $data = null;

        $sql = "SELECT user_name,user_id,sum(topic_play_data) stime FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND topic.topic_posted_time LIKE '" . $date . "%' ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id limit " . $page . "," . $rows;
        $sql1 = "SELECT user_name,user_id,sum(topic_play_data) sdata FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type  = '跑步' AND topic.topic_posted_time LIKE '" . $date . "%' ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id limit " . $page . "," . $rows;
        $get_times = $this->query($sql);
        $get_datas = $this->query($sql1);
        foreach ($get_times as $key => $value) {
            $stime[$key] = round($value['stime'] / 60, 2);
            $user[$key] = $value['user_name'];
        }
        foreach ($get_datas as $key => $value) {
            $sdata[$key] = round($value['sdata'] / 1000, 2);
        }

        $nConn = D('Admin/Notice');
        $item = $nConn->getGoals();
        $data = array(
            'user' => $user,
            'stime' => $stime,
            'sdata' => $sdata,
            'itemData' => $item['notice_run_data'] / 1000,
            'itemTime' => $item['notice_play_time'] / 60
        );
        return $data;
    }


    //得到近七天的运动统计
    public function weekSport($number)
    {
        $sql = "SELECT user_name,user_id,date(topic_posted_time) posted_date,sum(topic_play_data) stime FROM ((SELECT * FROM user WHERE user_status = 1 AND user_number = '" . $number . "' ) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND date_sub(curdate(),INTERVAL 7 DAY) <= date(topic_posted_time)) AS tb1) on us.user_id = topic_user_id GROUP BY posted_date ORDER BY posted_date";
        $sql1 = "SELECT user_name,user_id,date(topic_posted_time) posted_date,sum(topic_play_data) sdata FROM ((SELECT * FROM user WHERE user_status = 1 AND user_number = '" . $number . "' ) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND date_sub(curdate(),INTERVAL 7 DAY) <= date(topic_posted_time)) AS tb1) on us.user_id = topic_user_id GROUP BY posted_date ORDER BY posted_date";

        $getSportT = $this->query($sql);
        $getSportD = $this->query($sql1);

        $name = $getSportD[0]['user_name'];

        //生成近七天的日期数组
        $j = -1;
        $data = null;
        $date = null; //要返回的日期
        $stime = null; //要返回的时间
        $sdata = null; //要返回的距离
        $Tlength = sizeof($getSportT);
        $Dlength = sizeof($getSportD);
        for ($i = -6; $i <= 0; $i++) {
            $cnt = ++$j;
            $de = null;
            $st = 0;
            $sd = 0;
            $n = 0;
            $m = 0;
            $de = date('Y-m-d', strtotime($i . ' day'));
            for ($k = 0; $k < $Tlength; $k++) {
                if ($getSportT[$k]['posted_date'] == $de) {
                    $st = round((Integer)($getSportT[$k]['stime']) / 60, 2);
                    break;
                }
            }
            for ($s = 0; $s < $Dlength; $s++) {
                if ($getSportD[$s]['posted_date'] == $de) {
                    $sd = round($getSportD[$s]['sdata'] / 1000, 2);
                    break;
                }
            }
            $stime[$cnt] = $st;
            $sdata[$cnt] = $sd;
            $date[$cnt] = $de;
        }
        $data = array(
            'stime' => $stime,
            'sdata' => $sdata,
            'name' => $name,
            'date' => $date
        );
        return $data;
    }

    /**
     * @param $para //参数 按周统计或者按月统计
     */
    public function sportCount($para, $page, $nu)
    {
        $sql = null;
        if ($para === 'week') {
            //$sql = "SELECT * FROM  (SELECT user_number,user_name,user_class,user_id,sum(topic_play_data) stime FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND YEARWEEK(date_format(topic.topic_posted_time,'%Y-%m-%d')) = YEARWEEK(now()) ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id ORDER BY stime DESC) AS tbs1,(SELECT user_id,sum(topic_play_data) sdata FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND YEARWEEK(date_format(topic.topic_posted_time,'%Y-%m-%d')) = YEARWEEK(now()) ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id) AS tbs2 WHERE tbs1.user_id = tbs2.user_id LIMIT ".$page.",".$nu;
            $sql = "SELECT user_number,user_name,user_class,tbs1.user_id,stime,sdata,(IFNULL(str,0)+IFNULL(std,0)) rate FROM  (SELECT user_number,user_name,user_class,user_id,sum(topic_play_data) stime,sum(topic_rate) str FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND YEARWEEK(date_format(topic.topic_posted_time,'%Y-%m-%d')) = YEARWEEK(now()) ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id ORDER BY stime DESC) AS tbs1,(SELECT user_id,sum(topic_play_data) sdata,sum(topic_rate) std FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND YEARWEEK(date_format(topic.topic_posted_time,'%Y-%m-%d')) = YEARWEEK(now()) ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id) AS tbs2 WHERE tbs1.user_id = tbs2.user_id ORDER BY rate DESC LIMIT " . $page . "," . $nu;
        } else if ($para === 'month') {
            //$sql = "SELECT * FROM  (SELECT user_number,user_name,user_class,user_id,sum(topic_play_data) stime FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND date_format(topic_posted_time,'%Y-%m')=date_format(now(),'%Y-%m')) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id ORDER BY stime DESC) AS tbs1,(SELECT user_id,sum(topic_play_data) sdata FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND date_format(topic_posted_time,'%Y-%m')=date_format(now(),'%Y-%m') ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id) AS tbs2 WHERE tbs1.user_id = tbs2.user_id LIMIT ".$page.",".$nu;
            $sql = "SELECT user_number,user_name,user_class,tbs1.user_id,stime,sdata,(IFNULL(str,0)+IFNULL(std,0)) rate FROM  (SELECT user_number,user_name,user_class,user_id,sum(topic_play_data) stime,sum(topic_rate) str FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND date_format(topic_posted_time,'%Y-%m')=date_format(now(),'%Y-%m') ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id ORDER BY stime DESC) AS tbs1,(SELECT user_id,sum(topic_play_data) sdata,sum(topic_rate) std FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND date_format(topic_posted_time,'%Y-%m')=date_format(now(),'%Y-%m') ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id) AS tbs2 WHERE tbs1.user_id = tbs2.user_id ORDER BY rate DESC LIMIT " . $page . "," . $nu;
        } else {
            $sql = "SELECT user_number,user_name,user_class,tbs1.user_id,stime,sdata,(IFNULL(str,0)+IFNULL(std,0)) rate FROM  (SELECT user_number,user_name,user_class,user_id,sum(topic_play_data) stime,sum(topic_rate) str FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type != '跑步' AND date_format(topic.topic_posted_time,'%Y-%m-%d') = '".$para."' ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id ORDER BY stime DESC) AS tbs1,(SELECT user_id,sum(topic_play_data) sdata,sum(topic_rate) std FROM ((SELECT * FROM user WHERE user_status = 1) AS us) left join ((SELECT * FROM topic WHERE topic_play_type = '跑步' AND date_format(topic.topic_posted_time,'%Y-%m-%d') = '".$para."' ) AS tb1) on us.user_id = topic_user_id  GROUP BY us.user_id) AS tbs2 WHERE tbs1.user_id = tbs2.user_id ORDER BY rate DESC LIMIT " . $page . "," . $nu;
        }
        return $this->query($sql);
    }
}