<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-18
 * Time: 下午1:32
 */

namespace Admin\Controller;


use Think\Controller;

class ReplyManageController extends Controller
{
    public function index()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $page = I('get.p', 1, 'intval');


        $count = 10;
        $totalpage = 1;
        $rConn = D('Reply');
        $rows = sizeof($rConn->getRepList());
//        dump($rows);
//        exit();

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
        $res = $rConn->getRepList();
//        dump($cConn->getLastSql());
//        dump($res);
//        exit();

//        dump($sport_type);
        $this->assign('lo', $lo);
        $this->assign('p', $p);
        $this->assign('pages', $totalpage);
        $this->assign('rInfo', $res);

        $this->display('Index/manageReply');
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

    public function delR()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $rid = I('post.rid', 1, 'intval');
        $rConn = D('Reply');
        $res = $rConn->delRepById($rid);

        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
           echo '没有删除任何项！';
        } else {
            echo '删除成功！';
        }
    }

    public function delRs()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $json = $_POST['json'];
        $rids = json_decode($json,true);
            unset($rids['length']);
        $rConn = D('Reply');
//        dump($rids);
//        exit();

        $res = $rConn->delRepById($rids);
        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
            echo '没有删除任何项！';
        } else {
            echo '删除成功！';
        }
    }

}