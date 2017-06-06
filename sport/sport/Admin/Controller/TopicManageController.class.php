<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-17
 * Time: 下午3:41
 */

namespace Admin\Controller;


use Think\Controller;

class TopicManageController extends Controller
{
    public function index()
    {

        $user = $this->is_Login();
        $role = $this->is_role($user);

        $number = trim(I('get.number', null));
        $name = trim(I('get.name', null));
        $type = trim(I('get.type', null));
        $page = I('get.p', 1, 'intval');

        if ($type === '' || $type === null) {
            $type = 'All';
        }

        $arr['number'] = $number;
        $arr['name'] = $name;
        $arr['type'] = $type;

        $count = 10;
        $totalpage = 1;
        $tConn = D('Topic');
        $rows = sizeof($tConn->getRows($number, $name, $type));
        if ($rows === 0) {
            $rows = 1;
        }
        if ($rows % $count !== 0) {
            $totalpage = floor($rows / $count) + 1;
        } else {
            $totalpage = $rows / $count;
        }

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalpage) {
            $page = $totalpage;
        }

        $p = $page;
        $lo = $p;

//        dump($page);
//        exit();
        if ($lo > 1) {
            $lo = ($lo - 1) * $count + 1;
        }
        $ul = 'number=' . $number . '&name=' . $name . '&type=' . $type;

        $res = $tConn->getAllTopicInfo($page - 1, $count, $number, $name, $type);
//        dump($tConn->getLastSql());
//        dump($res);
//        exit();
        $sport_type = $tConn->field('distinct topic_play_type')->order('topic_play_type desc')->select();

//        dump($sport_type);
        $this->assign('lo', $lo);
        $this->assign('ul', $ul);
        $this->assign($arr);
        $this->assign('p', $p);
        $this->assign('pages', $totalpage);
        $this->assign('tInfo', $res);
        $this->assign('stype', $sport_type);
        $this->display('Index/manageTopic');
    }

    public function is_Login()
    {
        $user = $_SESSION['user'];
        if ($user === null) {
            $this->error('请登录...', __ROOT__ . '/index.php/Home/Login');
        }
        return $user;
    }

    public function is_role($user)
    {
        $role = (Integer)$user['user_role'];
        if ($role === 2) {
            $this->error('权限不够！');
        }
        return $role;
    }

    public function delT()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $tid = I('post.tid', -1, 'intval');
        $tConn = D('Topic');
        $res = $tConn->delOwn($tid);
        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
            echo '没有删除任何项！';
        } else {
            echo'删除成功！';
        }
    }

    public function delTs()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $json = $_POST['ids'];
        $tids = json_decode($json,true);
        unset($tids['length']);

        $tConn = D('Topic');
        $res = $tConn->delOwn($tids);
        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
            echo '没有删除任何项！';
        } else {
            echo '删除成功！';
        }
    }
}