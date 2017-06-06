<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-18
 * Time: 下午1:31
 */

namespace Admin\Controller;


use Think\Controller;

class CommentManageController extends Controller
{
    public function index()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $number = trim(I('get.number', null));
        $name = trim(I('get.name', null));
        $date = trim(I('get.date', null));
        $page = I('get.p', 1, 'intval');


        $arr['number'] = $number;
        $arr['name'] = $name;
        $arr['date'] = $date;

        $count = 10;
        $totalpage = 1;
        $cConn = D('Comment');
        $rows = $cConn->getRows($number, $name, $date);
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
        $ul = 'number=' . $number . '&name=' . $name . '&date=' . $date;

        $res = $cConn->getAllComm($page - 1, $count,$number,$name,$date);
//        dump($cConn->getLastSql());
//        dump($res);
//        exit();

        $this->assign('lo', $lo);
        $this->assign('ul', $ul);
        $this->assign($arr);
        $this->assign('p', $p);
        $this->assign('pages', $totalpage);
        $this->assign('cInfo', $res);

        $this->display('Index/manageComment');
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

    public function delC()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $cid = I('get.cid', 1, 'intval');
        $cConn = D('Comment');
        $res = $cConn->delComById($cid);

        if ($res === false) {
            $this->error('删除出错！');
        } else if ($res === 0) {
            $this->error('没有删除任何项！');
        } else {
            $this->success('删除成功！', __MODULE__ . '/CommentManage', 1);
        }
    }

    public function delCs()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $ids = I('post.', 1, 'intval');
        $cConn = D('Comment');
        $cids = $ids['id'];

        $res = $cConn->delComById($cids);
        if ($res === false) {
            $this->error('删除出错！');
        } else if ($res === 0) {
            $this->error('没有删除任何项！');
        } else {
            $this->success('删除成功！', __MODULE__ . '/CommentManage', 1);
        }
    }
}