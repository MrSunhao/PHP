<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 21:35
 */

namespace Home\Controller;


use Think\Controller;

class UserController extends Controller
{
    /**
     * 暂时用作调用info方法
     */
    public function index()
    {
        $this->info();
    }

    /**
     * 用户信息查看
     */
    public function info()
    {
        $session = $_SESSION['user'];
        if ($session) {
            $userId = $session['user_id'];
            $userName = $session['user_name'];


            $user = D('Admin/User');
            $info = null;
            $id = $user_number = I('get.id', '0', 'htmlspecialchars');
            if ($id != 0) {
                $info = $user->findUserById($id);
            } else {
                $this->error('没有该用户！');
            }
            $this->assign('info', $info);
            $this->assign('userId', $userId);
            $this->assign('userName', $userName);
            $this->display('User/owner');
        } else {
            $this->error('请登录...', __MODULE__.'/Login');
        }
    }

    /**
     * 注销
     */
    public function logOut()
    {
        if (!isset($_SESSION['user'])) {
            $this->error('您还没有登录...');
        } else {
            session('username', null);
            session('[destroy]');
            $this->success('退出成功！', __ROOT__.'/index.php');

        }
    }

    /**
     * 用户更新信息
     */
    public function update()
    {
        $user = D('Admin/User');
        $id = I('get.id',0, 'intval');
        if (is_numeric($id)) {
            $info = $user->findUserById($id);
            if ($user->isLogin()) {
                if (!isset($info)) {
                    $this->error('查无此人！');//防止用户在自行输入url时验证
                } else {
                    $this->assign('info', $info);
                    $this->display('User/updateInfo');
                }
            } else {
                $this->error('请登录...', __MODULE__.'/Login');
            }

        }
    }

    /**
     * 执行更新操作
     */
    public function updateInfo()
    {
        $user = D('Admin/User');
        $dataInfo = $user->updateUser();
        if (!is_array($dataInfo)) {
            $this->error($dataInfo);
        } else {
//            $where['user_id'] = $dataInfo['user_id'];
//            $res = $user->where($where)->save($dataInfo);
            $res = $user->updateU($dataInfo);
            if ($res === false) {
                $this->error('更新失败！');
            } else if ($res === 0) {
                $this->success('没有更新数据！', __MODULE__.'/User/Info/id/' . $dataInfo['user_id'], 3);
            } else {
                $this->success('更新成功！', __MODULE__.'/User/Info/id/' . $dataInfo['user_id'], 3);
            }
        }
    }

    /**
     * 用户修改密码
     */
    public function updatePassword()
    {
        $user = $_SESSION['user'];
        $realid = $user['user_id'];

        $this->assign('user_id', $realid);
        $this->display('User/password');
    }

    /**
     * 执行修改密码操作
     */
    public function password()
    {
        $user_id = I('post.user_id', 0, 'intval');
        $userInfo = $_SESSION['user'];
        $realid = $userInfo['user_id'];
        if ($user_id === 0 || $user_id != $realid) {
            $this->error('非法操作');
        }

        $user_oldpassword = I('post.user_oldpassword', '', 'htmlspecialchars');
        if ($user_oldpassword == '') {
            $this->error('原密码不能为空！');
        }
        $user_newpassword = I('post.user_newpassword', '', 'htmlspecialchars');
        if ($user_newpassword == '') {
            $this->error('新密码不能为空！');
        }
        $user_newpassword1 = I('post.user_newpassword1', '', 'htmlspecialchars');
        if ($user_newpassword1 == '') {
            $this->error('确认密码不能为空！');
        }
        $user = D('Admin/User');
        $password = $user->field('user_password')->where(['user_id' => $user_id])->find();
        $user_password = $password['user_password'];
        /*判断输入的原密码是否正确*/
        if (md5($user_oldpassword) != $user_password) {
            $this->error('原密码不正确！');
        } else if (strlen($user_newpassword) < 6 || strlen($user_newpassword) > 16) {
            $this->error('新密码必须是6-16位');
        } else if (strlen($user_newpassword1) < 6 || strlen($user_newpassword1) > 16) {
            $this->error('确认密码必须是6-16位');
        } else if ($user_newpassword != $user_newpassword1) {
            $this->error('新密码不一致！');
        } else if ($user_oldpassword == $user_newpassword) {
            $this->error('没做任何修改！');
        } else {
            $res = $user->updateUserById($user_newpassword, $realid);
            if ($res != 0) {
                $this->success('密码修改成功！', __MODULE__.'/User/Info/id/' . $realid, 3);
            } else {
                $this->error('没有修改！');
            }
        }
    }

    /**
     * 展示上传图片页面
     */
    public function uploadImg()
    {
        $this->display('User/updateImg');
    }

    public function updateImg()
    {
        $this->upload();
    }

    /**
     * 文件上传方法
     */
    public function upload()
    {
        $user = $_SESSION['user'];
        $user_id = $user['user_id'];
        $userConn = D('Admin/User');
        if (!$_FILES['user_image']['name']) {
            $this->error('请上传一张图片！');
        }
        $upload = new \Think\Upload(); //实例化上传类
        $upload->maxSize = 2048000;//设置附件上传大小,单位是b
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');//设置允许上传类型
        $upload->rootPath = './images/avatar/';//设置上传文件根目录
        $upload->autoSub = false;
        $upload->thumbRemoveOrigin = true;
        $upload->saveName = time().$user_id;
        //上传文件
        $info = $upload->uploadOne($_FILES['user_image']);
        if (!$info) {//上传错误提示错误信息
            $this->error($upload->getError());
        } else {

            $img = $userConn->field('user_image')->where(['user_id'=>$user_id])->find();
            $user_img = $img['user_image'];
            if($user_img!=null){
                $filePath =_SPORTIMGPATH_.'avatar/'.$img['topic_play_image'];
                unlink($filePath);
            }

            $imageName = $info['savename'];
            $data['user_image']=$imageName;
            $res = $userConn->field('user_image')->where(['user_id'=>$user_id])->save($data);
            if($res!=0){
                $this->success('上传头像成功！',__MODULE__.'/User/Info/id/'.$user_id,3);
            }else{
                $this->error('上传失败！');
            }
        }
    }

}