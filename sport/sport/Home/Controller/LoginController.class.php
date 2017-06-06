<?php
/**
 * 执行登录的相关操作
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/28
 * Time: 19:33
 */

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller
{
    public function index()
    {
        //展示登录页面
        $this->display('User/login');
    }

    public function login()
    {
        $user = D('Admin/User'); //跨模块实例化对象
        if($user->isLogin()){    //判断是否登录
            $this->redirect(__MODULE__.'/Index/index','',2,'您已经登录...');
        }else {
            $result = $user->checkLoginError();
            if ($result === null) {
                $role = $_SESSION['user']['user_role'];
                $role = (Integer)$role;
                if(($role === 1)||($role === 0)){
                    $this->success('登录成功！',__ROOT__.'/index.php/Admin',0);
                }else{
                    $this->success('登录成功！',__ROOT__.'/index.php',0);
                }
            } else {
                $this->error($result);
            }
        }
    }
}