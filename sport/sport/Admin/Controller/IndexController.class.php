<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $user = $this->is_Login();
        $role = $this->is_role();

        $page = I('get.p', 1, 'intval');
        $nConn = D('Notice');
        $notice_user_id = $user['user_id'];

        $totalpage = 1;
        $count = 10;

        $rows = sizeof($nConn->getNoticeRowsByUid($notice_user_id));
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
        $nlist = $nConn->getNoticeByUid($page, $count, $notice_user_id);

        $this->assign('p', $page);
        $this->assign('lo', $lo);
        $this->assign('pages', $totalpage);
        $this->assign('nlist', $nlist);
        $this->display('Index/index');
    }


    public function openIssueN()
    {
        $this->display('Index/issueN');
    }

    /**
     * 发表公告
     */
    public function issueNotice()
    {
        $user = $this->is_Login();
        $role = $this->is_role();

        $data = null;
        $notice_user_id = $user['user_id'];
        $notice_user_name = $user['user_name'];
        $notice_title = trim(I('post.title', null));
	$hour = I('post.hour', null, 'intval');
        $minute = I('post.minute', null, 'intval');
        $notice_run_data = trim(I('post.data', null));	
	
	if ($notice_title === null || $notice_title === '') {
            echo '主题不能为空！';
        }
        else if ($notice_run_data === null || $notice_run_data === '') {
            echo '目标距离数据不能为空！';
        }
        else if ($hour === null || $hour === '') {
            echo '小时不能为空！';
        }
        else if ($minute === null || $minute === '') {
            echo '分钟不能为空！';
        }
	else{
	 $notice_time = date('Y-m-d H:i:s');

         $notice_run_data = $notice_run_data * 1000;
         $notice_play_time = $hour * 60 + $minute;
         $notice_content = trim(I('post.content', null));	

         $data['notice_user_id'] = $notice_user_id;
         $data['notice_title'] = $notice_title;
         $data['notice_time'] = $notice_time;
         $data['notice_run_data'] = $notice_run_data;
         $data['notice_play_time'] = $notice_play_time;
         $data['notice_content'] = $notice_content;
         $data['notice_user_name'] = $notice_user_name;

         $nConn = D('Notice');

         $res = $nConn->addNotice($data);
         if ($res === false) {
            echo '发表失败！请重试...';
        } else {
            echo '发表成功！';
        }
	}
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

    public function upN()
    {
        $user = $this->is_Login();
        $role = $this->is_role();
        $nid = I('get.nid', -1, 'intval');

        $nConn = D('Notice');
        $res = $nConn->getNoticeInfoById($nid);
        $hour = floor($res['notice_play_time'] / 60);
        $minute = $res['notice_play_time'] - floor($res['notice_play_time'] / 60) * 60;

        $this->assign('nInfo', $res);
        $this->assign('hour', $hour);
        $this->assign('minute', $minute);
        $this->display('Index/upN');
    }

    public function doupN()
    {
        $user = $this->is_Login();
        $role = $this->is_role();

        $id = I('post.id', null, 'intval');
        $title = trim(I('post.title', null));
        if ($title === null || $title === '') {
            $this->error('主题不能为空！');
        }
        $content = trim(I('post.content', null));
        $data = I('post.data', null, 'intval');
        $data = $data * 1000;
        $hour = I('post.hour', null, 'intval');
        $minute = I('post.minute', null, 'intval');
        $time = $hour * 60 + $minute;

        $updata = null;
        $updata = array(
            'notice_id' => $id,
            'notice_title' => $title,
            'notice_content' => $content,
            'notice_run_data' => $data,
            'notice_play_time' => $time
        );
        $nConn = D('Notice');
        $uid = $nConn->field('notice_user_id')->where(['notice_id' => $id])->find();
        if ((Integer)$uid !== (Integer)$user['user_id']) {
            $this->error('权限不够！');
        }
        $res = $nConn->upNotice($updata);

        if ($res === false) {
            echo '更新失败！';
        } else if ($res === 0) {
            echo '没有更新数据！';
        } else {
            echo '更新成功！';
        }
    }

    public function delN()
    {
        $user = $this->is_Login();
        $role = $this->is_role();

        $ids = I('post.nid', -1, 'intval');
        $nConn = D('Notice');
        $res = $nConn->delNoticeById($ids);
        if ($res === false) {
            echo '操作有误！';
        } else if ($res === 0) {
            echo '没有删除任何数据！';
        } else {
            echo '删除成功！';
        }
    }

    public function delNs()
    {
        $user = $this->is_Login();
        $role = $this->is_role();

        $json = $_POST['ids'];
        $ids = json_decode($json, true);
        unset($ids['length']);
        $nConn = D('Notice');
        $res = $nConn->delNoticeById($ids);
        if ($res === false) {
            echo '操作有误！';
        } else if ($res === 0) {
            echo '没有删除任何数据！';
        } else {
            echo '删除成功！';
        }
    }
}
