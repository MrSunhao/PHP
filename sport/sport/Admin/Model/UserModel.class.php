<?php
/**
 * Created by PhpStorm.
 * User: Sun
 * Date: 2016/11/29
 * Time: 8:53
 */

namespace Admin\Model;


use Think\Model;

class UserModel extends Model
{
    /**
     * 检查用户是否登录
     */
    public function isLogin()
    {
        if (isset($_SESSION['user'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查登录错误
     * @param user_name
     * @param user_password
     */
    public function checkLoginError()
    {
        $user_number = I('post.user_number', '', 'htmlspecialchars');
        $user_password = I('post.user_password', '');
        //检查用户名或密码是否为空
        if ($user_number === '') {
            return '用户名为空';
        }
        if ($user_password === '') {
            return '密码为空';
        }
        //检查账号是否存在
        $where['user_number'] = $user_number;
        $where['user_password'] = md5($user_password);
        $result = $this->where($where['user_name'])->find();
        if (empty($result)) {
            return '账号不存在！';
        }

        //检查密码是否正确
        $result = $this->where($where)->find();
        if (empty($result)) {
            return '用户名或密码错误';
        }
        //检查账号是否被冻结
        if ($result['user_status'] == 0) {
            return '账号被冻结';
        }
        $this->userLoginInfo($result);
        return null;

    }

    /**
     * 记录登录信息
     * @param $result
     */
    public function userLoginInfo($result)
    {
        //记录登录信息
        $where['user_id'] = $result['user_id'];
        $this->where($where)->save(
            ['user_lastLoginTime' => date('Y-m-d H:i:s'),
                'user_lastLoginIp' => get_client_ip()
            ]);
        /*记录用户信息*/
        $user = [
            'user_id' => $result['user_id'],
            'user_number' => $result['user_number'],
            'user_name' => $result['user_name'],
            'user_class' => $result['user_class'],
            'user_role' => $result['user_role']
        ];
        session('user', $user);
    }

    /**
     * 用户注册
     * @return null|string
     */
    public function registUser()
    {
        $user_number = trim(I('post.user_number', '', 'htmlspecialchars'));
        $length = strlen($user_number);
        $where['user_number'] = $user_number;
        $number = $this->where($where)->find();
        if ($user_number === '') {
            return '账号为空！';
        }
        if(!preg_match("/^\\d*$/i",$user_number)){
            return '学号只能是6-11位数字的组合哦！';
        }
        if($length<6 || $length>11){
            return '学号只能是6-11位的数字组合哦！';
        }
        if ($number) {
            return '账号已经存在！';
        }

        $user_name = I('post.user_name', '', 'htmlspecialchars');
        if ($user_name === '') {
            return '用户名为空！';
        }

        $user_class = I('post.user_class', '', 'htmlspecialchars');
        if ($user_class === '') {
            return '班级为空！';
        }

        $user_gender = I('post.user_gender', '', 'htmlspecialchars');
        if ($user_gender === '') {
            return '性别为空！';
        }
        $user_grade = I('post.user_grade', '', 'htmlspecialchars');
        if ($user_grade === '') {
            return '年级为空！';
        }

        $user_password = I('post.user_password', '');
        if ($user_password === '') {
            return '密码为空！';
        }
        if (strlen($user_password) < 6 || strlen($user_password) > 16) {
            return '密码必须是6-16位！';
        }
        $user_password1 = I('post.user_password1', '');
        if ($user_password1 === '') {
            return '确认密码为空！';
        }
        if (strlen($user_password1) < 6 || strlen($user_password1) > 16) {
            return '密码必须是6-16位！';
        }
        if ($user_password !== $user_password1) {
            return '密码不一致！';
        }

        $userData = array(
            'user_number' => $user_number,
            'user_name' => $user_name,
            'user_class' => $user_class,
            'user_gender' => $user_gender,
            'user_grade' => $user_grade,
            'user_password' => $user_password
        );
        $this->createUser($userData);
        return null;
    }

    /**
     * 更新用户
     * @param $id
     */
    public function updateUser()
    {
        $user_id = I('post.user_id', 0, 'htmlspecialchars');
        if ($user_id === 0) {
            return '不合法操作！';
        }
        if (($this->where(['user_id' => $user_id])->find()) == null) {
            return '查无记录！';
        }
        $user_number = I('post.user_number', '', 'htmlspecialchars');
        if ($user_number === '') {
            return '学号为空！';
        }
        $user_name = I('post.user_name', '', 'strip_tags');
        if ($user_name === '') {
            return '用户名为空！';
        }
        $user_class = I('post.user_class', '', 'strip_tags');
        if ($user_class === '') {
            return '班级为空！';
        }
        $user_gender = I('post.user_gender', '', 'htmlspecialchars');
        if ($user_gender === '') {
            return '性别为空！';
        }
        $user_grade = I('post.user_grade', '', 'htmlspecialchars');
        if ($user_grade === '') {
            return '班级为空';
        }

        /*以下数据如果为空的话，要将格式转换为null类型 防止数据插入时出错*/
        $user_birthday = I('post.user_birthday', '', 'htmlspecialchars');
        if ($user_birthday === '') {
            $user_birthday = null;
        }
        if ($user_birthday !== null) {
            $patten = "/^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/i";
            if (!preg_match($patten, $user_birthday)) {
                return '生日格式不正确！';
            }
        }
        $user_hobby = I('post.user_hobby', '', 'strip_tags');
        if ($user_hobby === '') {
            $user_hobby = null;
        }
        $user_descroption = I('post.user_descroption', '', 'strip_tags');
        if ($user_descroption === '') {
            $user_descroption = null;
        }
        $user_qq = I('post.user_qq', '', 'htmlspecialchars');
        if ($user_qq !== '') {
            if (!preg_match('/^[1-9]\d{4,10}$/i', $user_qq)) {
                return 'QQ格式非法！';

            }
        } else {
            $user_qq = null;
        }
        $user_email = I('post.user_email', '', 'htmlspecialchars');
        if ($user_email !== '') {
            if (!preg_match('/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/i', $user_email)) {
                return 'Email格式非法！';
            }
        } else {
            $user_email = null;
        }
        $updateInfo = array(
            'user_id' => $user_id,
            'user_number' => $user_number,
            'user_name' => $user_name,
            'user_class' => $user_class,
            'user_gender' => $user_gender,
            'user_grade' => $user_grade,
            'user_birthday' => $user_birthday,
            'user_hobby' => $user_hobby,
            'user_descroption' => $user_descroption,
            'user_qq' => $user_qq,
            'user_email' => $user_email
        );
        return $updateInfo;
    }

    /**
     * 修改用户信息
     * @param $data
     * @return bool
     */
    public function updateU($data)
    {
        $where['user_id'] = $data['user_id'];
        return $this->where($where)->save($data);
    }

    /**
     * 创建用户
     * @param $userdata
     * @author sun
     */
    public function createUser($userData)
    {
        $userData['user_password'] = md5($userData['user_password']);
        return $this->field('user_number,user_name,user_class,user_gender,user_grade,user_password')->data($userData)->filter('strip_tags')->add();
    }

    /**
     * 批量管理用户（冻结或者解冻用户）只是改变用户的状态，并不是真正的删除
     * @param $ids
     * @param $status
     */
    public function manageUserStatus($ids, $status)
    {
        if (!is_array($ids)) {//如果传入的是单值，将其转换为数组
            $ids = array($ids);
        }
        $where['user_id'] = array('in', $ids);
        $data['user_status'] = $status;
        return $this->where($where)->save($data);
    }

    /**
     * 删除用户数据（真正的删除）
     * 满足条件为用户状态为冻结用户并且要知道被删除用户的id
     * @param $id
     * @return mixed
     */
    public function deleteUserData($ids)
    {
        if (empty($ids) === false) {
            for ($i = 0; $i < $ids . sizeof(); $i++) {
                $where['user_id'] = $ids[$i];
                $where['user_status'] = 0;
                return $this->where($where)->delete();
            }
        }
    }


    /**
     * 修改用户密码
     * @param $newPassword
     * @param $id
     */
    public function updateUserById($newPassword, $ids)
    {
        if (!is_array($ids)) {//如果传入的是单值，将其转换为数组
            $ids = array($ids);
        }

        $where['user_id'] = array('in', $ids);
        $data['user_password'] = md5($newPassword);
        return $this->where($where)->save($data);
    }

    /**
     * 查看单个用户信息,得到的信息不包括密码,身份,状态
     * @param $id
     * @return 用户信息
     */
    public function findUserById($id)
    {
        return $this->field('user_password,user_status,user_lastLoginIp,user_lastLoginTime', true)->where(['user_id' => $id])->find();
    }

    /**
     * 查询所有用户信息,所得的信息不包括密码
     */
    public function findAllUser()
    {
        return $this->field('user_password,user_role,user_status', true)->where(['user_status'=>1])->order('user_grade,user_class')->select();
    }

    /**
     * 根据学号或姓名查用户信息
     * @param $number
     * @param $name
     */
    public function getInfoByNuOrNa($number, $name)
    {
        if ($number !== '' && $name === '') {
            $where = array(
                'user_number' => $number,
                'user_role' => 2
            );
        } else if ($number === '' && $name !== '') {
            $where = array(
                'user_name' => $name,
                'user_role' => 2
            );
        } else {
            $where = array(
                'user_number' => $number,
                'user_name' => $name,
                'user_role' => 2
            );
        }
        return $this->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->where($where)->select();
    }


    /**
     * 根据年级查询用户信息（包括 user_id,user_name,user_class,user_image）
     * @param $grade
     * @return mixed
     */
    public function selectUserInfoByGrade($grade)
    {
        if ($grade === 'All') {
            return $this->field('user_id,user_name,user_class,user_image')->select();
        } else {
            return $this->field('user_id,user_name,user_class,user_image')->where(['user_grade' => $grade])->select();
        }
    }

    /**
     * 返回普通用户列表
     * @param int $page
     * @param int $rows
     * @return mixed
     */
    public function pageUserInfo($page = 1, $r, $rows = 10, $number, $name, $role)
    {
        $map = null;
        $ro = $r;
        $map['user_status'] = 1;
        if ($number === '') {
            $number = null;
        }
        if ($name === '') {
            $name = null;
        }
        if ($role === 0) {
            if ($r === -1) {
                $ro = 0;
                if ($number === null && $name === null) {
                    $map['user_role'] = array('neq', $ro);
                } else if ($number !== null && $name === null) {
                    $map['user_role'] = array('neq', $ro);
                    $map['user_number'] = array('eq', $number);
                } else if ($number === null && $name !== null) {
                    $map['user_role'] = array('neq', $ro);
                    $map['user_name'] = array('eq', $name);
                } else {
                    $map['user_role'] = array('neq', $ro);
                    $map['user_number'] = array('eq', $number);
                    $map['user_name'] = array('eq', $name);
                }
                return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc,user_role')->where($map)->select();
            } else {
                $ro = $r;
                if ($number === null && $name === null) {
                    $map['user_role'] = array('eq', $ro);
                } else if ($number !== null && $name === null) {
                    $map['user_role'] = array('eq', $ro);
                    $map['user_number'] = array('eq', $number);
                } else if ($number === null && $name !== null) {
                    $map['user_role'] = array('eq', $ro);
                    $map['user_name'] = array('eq', $name);
                } else {
                    $map['user_role'] = array('eq', $ro);
                    $map['user_number'] = array('eq', $number);
                    $map['user_name'] = array('eq', $name);
                }
                return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc,user_role')->where($map)->select();

            }
        } else {
            $ro = 2;
            if ($number === null && $name === null) {
                $map['user_role'] = array('eq', $ro);
            } else if ($number !== null && $name === null) {
                $map['user_role'] = array('eq', $ro);
                $map['user_number'] = array('eq', $number);
            } else if ($number === null && $name !== null) {
                $map['user_role'] = array('eq', $ro);
                $map['user_name'] = array('eq', $name);
            } else {
                $map['user_role'] = array('eq', $ro);
                $map['user_number'] = array('eq', $number);
                $map['user_name'] = array('eq', $name);
            }
            return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc,user_role')->where($map)->select();
        }
    }

    /**
     * 查询被冻结的用户
     * @param int $page
     * @param int $rows
     * @return mixed
     */
    public function pageFrozenUserInfo($page = 1, $rows = 10)
    {
        $where = array(
            'user_status' => 0
        );
        return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc')->where($where)->select();
    }

    /**
     * 查询被冻结的管理员
     * @param int $page
     * @param int $rows
     * @return mixed
     */
    public function pageFrozenMUserInfo($page = 1, $rows = 10)
    {
        $where = array(
            'user_role' => 1,
            'user_status' => 0
        );
        return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc')->where($where)->select();
    }

    /**
     * 返回管理员列表
     * @param int $page
     * @param int $rows
     * @return mixed
     */
    public function managerInfo($page = 1, $rows = 10)
    {
        $where = array(
            'user_role' => 1,
            'user_status' => 1
        );
        return $this->page($page, $rows)->field('user_id,user_number,user_name,user_gender,user_grade,user_class,user_qq,user_role,user_lastLoginIp,user_lastLoginTime')->order('user_grade asc')->where($where)->select();
    }

    public function gradeCount()
    {
        $sql = "SELECT COUNT(*) value,user_grade name FROM user GROUP BY user_grade";
        return $this->query($sql);
    }
}