<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        //跳转到主页

        //得到年级人数比和运动人数统计
        $uConn = D('Admin/User');
        $tConn = D('Admin/Topic');
        $nConn = D('Admin/Notice');

        //得到年级人数集合
        $res = $uConn->gradeCount();
        $val = null;
        foreach ($res as $key => $value) {
            $val[$key] = $value['name'];
        }
        $ldata = json_encode($val);
        $sdata = json_encode($res);

        //得到运动类型人数集合
        $sres = $tConn->sport_distribution();

        $stype = null;
        $sval = null;
        foreach ($sres as $key => $value) {
            $stype[$key] = $value['stype'];
            $sval[$key] = $value['sval'];
        }

        //得到当天的运动公告
        $todayNotice = $nConn->getNoticeInfo();
        $todayNotice['notice_run_data'] = round($todayNotice['notice_run_data'] / 1000, 2);
        $todayNotice['notice_play_time'] = round($todayNotice['notice_play_time'] / 60, 2);

        $this->assign('ldata', $ldata);
        $this->assign('sdata', $sdata);
        $this->assign('stype', json_encode($stype));
        $this->assign('sval', json_encode($sval));
        $this->assign('tSport', $todayNotice);

        $session = $_SESSION['user'];
        if ($session) {
            $userId = $session['user_id'];
            $userName = $session['user_name'];

            $this->assign('userId', $userId);
            $this->assign('userName', $userName);
            $this->display('index');
        } else {
            $this->display('index');
        }
    }

    public function days_sport()
    {
        $uConn = D('Admin/User');
        $tConn = D('Admin/Topic');

        $rows = $uConn->count();
        $p = I('post.p', null);
        $nu = 8;

        $pages = 1;
        if ($rows % $nu !== 0) {
            $pages = floor($rows / $nu) + 1;
        } else {
            $pages = $rows / $nu;
        }
        if ($p < 1) {
            $p = 1;
        }
        if ($p > $pages) {
            $p = $pages;
        }
        $start = ($p - 1) * $nu;
        $date = date('Y-m-d');
        $day_res = $tConn->day_info($start,$nu,$date);
        $day_res['p'] = $p;
        $day_res['pages'] = $pages;
        echo json_encode($day_res);
    }

    public function week_sport()
    {
        $tConn = D('Admin/Topic');
        $uConn = D('Admin/User');
        $number = I('post.number', null);
        $user = $uConn->where(['user_number' => $number])->find();
        if ($user === null) {
            echo json_encode(null);
        } else {
            $res = $tConn->weekSport($number);
            echo json_encode($res);
        }
    }

    /**
     * 动态页面展示
     */
    public function dynamic()
    {
        $this->display('Index/dynamic');
    }

    /**
     * 处理客户端的异步请求
     */
    public function dynamicPage()
    {
        $page = 1;
        $number = 5;
        $totalPage = 1;
        $Tic = D('Admin/Topic');
        $rows = $Tic->count(); //获取总条数
        if ($rows % $number === 0) {
            $totalPage = $rows / $number;
        } else {
            $totalPage = floor($rows / $number) + 1;
        }
        $page = intval($_GET['page']);  //获取请求的页数
        if ($page <= 0) {
            $page = 1;
        }
        if ($page > $totalPage) {
            $page = $totalPage;
        }
        $list = $Tic->getSportList($page, $number);
        $list[] = array(
            'toltalpage' => $totalPage,
        );
        if ($list) {
            echo json_encode($list);  //转换为json数据输出
        }
    }

    /**
     * 个人页面展示
     */
    public function record()
    {
        $user = $_SESSION['user'];
        if (isset($user)) {
            $this->assign('uid',$user['user_id']);
            $this->assign('uname',$user['user_name']);
            $this->display('Index/record');
        } else {
            $this->error('请登录...', __MODULE__ . '/Login', 1);
        }

    }

    /**
     * 查看他人的记录展示
     */
    public function lookRecord()
    {
        $uid = I('Get.uid',-1,'intval');
        if($uid === -1){
            $this->error('参数错误！');
        }
        $uConn = D('Admin/User');
        $user = $uConn->findUserById($uid);

        $this->assign('uid',$uid);
        $this->assign('uname',$user['user_name']);
        $this->display('Index/record');
    }

    /**
     * 处理个人记录页面异步请求
     */
    public function recordPage()
    {
        $page = 1;
        $number = 5;
        $totalPage = 1;
        //$user_id = $_SESSION['user']['user_id'];
        $user_id = intval($_GET['uid']);
        $Tic = D('Admin/Topic');
        $rows = $Tic->where(['topic_user_id' => $user_id])->count(); //获取总条数
        if ($rows % $number === 0) {
            $totalPage = $rows / $number;
        } else {
            $totalPage = floor($rows / $number) + 1;
        }
        $page = intval($_GET['page']);  //获取请求的页数
        if ($page <= 0) {
            $page = 1;
        }
        if ($page > $totalPage) {
            $page = $totalPage;
        }
        $list = $Tic->getSportOwnList($page, $number,$user_id);
        $list[] = array(
            'toltalpage' => $totalPage,
        );
        if ($list) {
            echo json_encode($list);  //转换为json数据输出
        }
    }

    /**
     * 删除个人动态
     */
    public function del()
    {
        $topic_id = I('get.tid', 0, 'intval');
        if ($topic_id === 0) {
            $this->error('非法操作');
        } else {
            $Tic = D('Admin/Topic');
            $uId = $Tic->field('topic_user_id')->where(['topic_id' => $topic_id])->find();

            if ((integer)$uId['topic_user_id'] !== (integer)$_SESSION['user']['user_id']) {
                $this->error('非法操作！');
            } else {
                $res = $Tic->delOwn($topic_id);
                if ($res === false) {
                    $this->error('删除失败！');
                } else if ($res === 0) {
                    $this->error('没有删除数据！');
                } else {
                    $this->success('删除成功', __MODULE__ . '/Index/record', 3);
                }
            }
        }
    }

    /**
     * 跳转发表动态页面 用户必须登录
     */
    public function toPosted()
    {
        $userId = $_SESSION['user']['user_id'];
        if ($userId != null) {
            $this->display('Index/posted');
        } else {
            $this->error('请登录...', __MODULE__ . '/Login', 3);
        }
    }

    /**
     * 接受主题表单
     * 接受的表单数据   sport_type hour(小时) minute（分钟） sport_data（运动距离）content（动态内容）sport_image(运动截图)
     * 要插入数据的字段 topic_user_id（发动态人id） topic_play_type(运动类型) topic_play_data（运动数据） topic_content（动态内容） topic_play_image（运动图片） topic_posted_time（发布时间）
     */
    public function addTopic()
    {

        $data = null;
        $topic_user_id = 0;
        $topic_play_type = null;
        $topic_play_data = 0;
        $topic_content = null;
        $topic_play_image = null;
        $topic_posted_time = null;

        $user = $_SESSION['user'];
        $topic_user_id = $user['user_id']; //通过SESSION获取用户id

        $sport_type = I('post.sport_type', '', 'intval');
        if ($sport_type === '') {
            return '运动类型为空！';
        }
        if ($sport_type === 1) {
            $sport_type = '跑步';
        }
        if ($sport_type === 2) {
            $sport_type = '篮球';
        }
        if ($sport_type === 3) {
            $sport_type = '足球';
        }
        if ($sport_type === 4) {
            $sport_type = '羽毛球';
        }
        if ($sport_type === 5) {
            $sport_type = '乒乓球';
        }
        $topic_play_type = $sport_type;

        $hour = I('post.hour', null, 'htmlspecialchars');
        $minute = I('post.minute', null, 'htmlspecialchars');
        if ($hour != null && $minute != null) {
            $hour = (integer)$hour;
            $minute = (integer)$minute;
            $topic_play_data = $hour * 60 + $minute;
            if ($topic_play_data >= 540) {
                $this->error('运动不能超过9小时哦！！！');
            }
        }
        $sport_data = I('post.sport_data', -1, 'intval');
        if ($sport_data !== -1) {
            if ($sport_data < 0) {
                $this->error('数据不合法！！！请输入0-40的数字');
            }
            if ($sport_data > 40) {
                $this->error('不能超过40千米哦！！！');
            }
            $topic_play_data = $sport_data * 1000;
        }

        $content = I('post.content', '', 'htmlspecialchars');
        if ($content === '') {
            $topic_content = null;
        } else {
            $topic_content = $content;
        }

        /*图片上传设置*/
        $upload = new \Think\Upload(); //实例化上传类
        $upload->maxSize = 2048000;//设置附件上传大小,单位是b 限制2MB
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');//设置允许上传类型
        $upload->rootPath = './images/sportImg/';//设置上传文件根目录
        $upload->autoSub = false;
        $upload->thumbRemoveOrigin = true;
        $upload->saveName = array('uniqid', '');
        //上传文件
        $info = $upload->uploadOne($_FILES['sport_image']);
        if (!$info) {//上传错误提示错误信息
            return $upload->getError();
        } else {
            $topic_play_image = $info['savename'];
        }

        $topic_posted_time = date('Y-m-d H:i:s');

        //查询当天目标
        $nConn = D('Admin/Notice');
        $goal = $nConn->getGoals();
        $rate = 0; //运动比
        if ($topic_play_type === '跑步') {
            $rate = round($topic_play_data / $goal['notice_run_data'], 3);
        } else {
            $rate = round($topic_play_data / $goal['notice_play_time'], 3);
        }

//组织数据
        $data = array(
            'topic_user_id' => $topic_user_id,
            'topic_play_type' => $topic_play_type,
            'topic_play_data' => $topic_play_data,
            'topic_content' => $topic_content,
            'topic_play_image' => $topic_play_image,
            'topic_posted_time' => $topic_posted_time,
            'topic_rate' => $rate
        );
        return $data;
    }

    /**
     * 发表动态处理（添加到动态数据表）
     */
    public function posted()
    {
        $user = D('Admin/Topic');
        $userId = $_SESSION['user']['user_id'];
        if ($userId === null) {
            $this->error('请登录...', __MODULE__ . '/Login', 3);
        } else {
            $Info = $this->addTopic();
            if (is_array($Info)) {
                $res = $user->data($Info)->add();
                if ($res !== false) {
                    $this->success('发表成功！', __MODULE__ . '/Index/dynamic', 3);
                } else {
                    $this->error('发表失败！');
                }
            } else {
                $this->error($Info);
            }
        }
    }

    /**
     * 运动榜
     */
    public function rankList()
    {
        $this->display('Index/ranklist');

    }

    public function rankListPage()
    {
        $p = 1; //当前页
        $row = 10; //展示条数
        $pages = 1; //总页数
        $p = I('post.p', 1, 'intval');
        $sport_type = I('post.type');
        $user_grade = I('post.grade');
        $data = I('post.data');

        $tConn = D('Admin/Topic');
        $uConn = D('Admin/User');
        $nConn = D('Admin/Notice');

        $tInfo = $tConn->getSportInfoByType($sport_type, $p, $row);
        $tlength = sizeof($tConn->getTotalpages($sport_type));
        if ($tlength % $row !== 0) {
            $pages = floor($tlength / $row) + 1;
        } else {
            $pages = $tlength / $row;
        }

        if ($p < 1) {
            $p = 1;
        }

        if ($p > $pages) {
            $p = $pages;
        }

        $uInfo = $uConn->selectUserInfoByGrade($user_grade);
        $res = null;
        foreach ($tInfo as $key => $value) {
            $rvalue = null;
            foreach ($uInfo as $ukey => $uvalue) {
                if ($value['topic_user_id'] === $uvalue['user_id']) {
                    $rvalue['topic_id'] = $value['topic_id'];
                    $rvalue['topic_user_id'] = $value['topic_user_id'];
                    $rvalue['topic_play_type'] = $value['topic_play_type'];
                    $rvalue['topic_play_data'] = $value['topic_play_data'];
                    $rvalue['user_name'] = $uvalue['user_name'];
                    $rvalue['user_class'] = $uvalue['user_class'];
                    $rvalue['user_image'] = $uvalue['user_image'];
                }
            }
            if (empty($rvalue)) {
                continue;
            } else {
                $goal = $nConn->getGoalByNewDate($sport_type);
                $rvalue['goal'] = $goal;//得到目标
                $res[$key] = $rvalue;
            }
        }
        if (empty($res)) {
            $res = null;
            echo json_encode($res);
        } else {
            $res['page'] = array(
                'p' => $p,
                'pages' => $pages
            );
            echo json_encode($res);
        }
    }

    public function getNotice()
    {
        $p = 1; //当前页
        $row = 4; //展示条数
        $pages = 1; //总页数
        $p = I('post.p', 1, 'intval');

        $nConn = D('Admin/Notice');
        $len = $nConn->count(); //获得公告总的记录数
        if ($len % $row !== 0) {
            $pages = floor($len / $row) + 1;
        } else {
            $pages = $len / $row;
        }

        if ($p < 1) {
            $p = 1;
        }

        if ($p > $pages) {
            $p = $pages;
        }
        $data = $nConn->getNotice($p, $row);
        $data['page'] = array(
            'p' => $p,
            'pages' => $pages
        );
        echo json_encode($data);
    }
}