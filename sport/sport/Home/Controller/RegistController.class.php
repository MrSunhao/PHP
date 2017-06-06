<?php
/**
 * 执行注册的相关操作
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/28
 * Time: 20:14
 */

namespace Home\Controller;


use Think\Controller;

class RegistController extends Controller
{
    public function index()
    {
        $this->display('User/regist');//展示注册页面
    }

    public function register()
    {
        $user = D('Admin/User');
        $result = $user->registUser();
        if ($result === null) {
            $login = $user->checkLoginError();
            if ($login === null) {
                $this->success('注册成功，正在登录...', __ROOT__ . '/index.php', 0);
            } else {
                $this->error($login);
            }
        } else {
            $this->error($result);
        }

    }
}