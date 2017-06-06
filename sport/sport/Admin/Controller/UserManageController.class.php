<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-17
 * Time: 下午3:40
 */

namespace Admin\Controller;


use Think\Controller;

class UserManageController extends Controller
{
    //对普通用户的管理展示
    public function index()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $uConn = D('User');
        $page = I('get.p', 1, 'intval'); //当前页
        $r = I('get.r', 2, 'intval'); //当前查询的角色

        $number = trim(I('get.number', null));
        $name = trim(I('get.name', null));
        $user_role = I('get.role', false, 'intval');

        $snumber = $number;
        $sname = $name;
        $srole = $user_role;

        $rows = 10;
        $totalPages = 1; //总页数

        if ($role === 0) {
            if ($user_role !== false) {
                $r = $user_role;
                if ($user_role === -1) {
                    $map['user_role'] = array('neq', 0);
                    $map['user_status'] = array('eq', 1);
                    $totalRows = $uConn->where($map)->count(); //得到总的记录数
                } else {
                    $map['user_role'] = array('eq', $user_role);
                    $map['user_status'] = array('eq', 1);
                    $totalRows = $uConn->where($map)->count(); //得到总的记录数
                }
            } else {
                $srole = $r;
                if ($r === -1) {
                    $map['user_role'] = array('neq', 0);
                    $map['user_status'] = array('eq', 1);
                    $totalRows = $uConn->where($map)->count(); //得到总的记录数
                } else {
                    $map['user_role'] = array('eq', $r);
                    $map['user_status'] = array('eq', 1);
                    $totalRows = $uConn->where($map)->count(); //得到总的记录数
                }
            }
        } else {
            $map['user_role'] = array('eq', 2);
            $totalRows = $uConn->where($map)->count(); //得到总的记录数
        }

        if ($totalRows % $rows !== 0) {
            $totalPages = floor($totalRows / $rows) + 1;
        } else {
            $totalPages = $totalRows / $rows;
        }

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $res = $uConn->pageUserInfo($page, $r, $rows, $number, $name, $role);

        $p = $page;
        $lo = $p;
        if ($lo > 1) {
            $lo = ($lo - 1) * $rows + 1;
        }

        $url = 'number=' . $number . '&name=' . $name;

        $this->assign('p', $p); //当前页
        $this->assign('r', $r);
        $this->assign('lo', $lo);//循环变量
        $this->assign('snumber', $snumber); //搜索条件 学号
        $this->assign('sname', $sname);//搜索条件 姓名
        $this->assign('url', $url);
        if ($role === 0) {
            $this->assign('srole', $srole);//搜索条件 用户类型
        }
        $this->assign('pages', $totalPages); //总页数
        $this->assign('userList', $res);//用户信息列表
        $this->assign('role', $role); //登陆用户角色
        $this->display('Index/manageUser');
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

    public function manageFrozenU()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $uConn = D('User');
        $page = I('get.p', 1, 'intval');
        $rows = 10;
        $totalRows = $uConn->where(['user_status' => 0])->count();
        $totalRows = (Integer)$totalRows;
        $totalPages = 1;
        if ($totalRows % $rows !== 0) {
            $totalPages = floor($totalRows / $rows) + 1;
        } else {
            $totalPages = $totalRows / $rows;
        }

        if ($page < 1) {
            $page = 1;
        }
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $res = $uConn->pageFrozenUserInfo($page, $rows);

        $p = $page;
        $lo = $p;
        if ($lo > 1) {
            $lo = ($lo - 1) * $rows + 1;
        }
        $this->assign('p', $p); //当前页
        $this->assign('lo', $lo);
        $this->assign('pages', $totalPages); //总页数
        $this->assign('userList', $res);
        $this->display('Index/frozenUser');
    }

    public function delU()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $uid = I('post.uid', 1, 'intval');
        $status = 0; //冻结参数
        $uConn = D('User');
        $res = $uConn->manageUserStatus($uid, $status);
        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
            echo '没有删除任何项！';
        } else {
            echo '删除成功！';
        }
    }

    public function delUs()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $json = $_POST['ids'];
        $uids = json_decode($json, true);
        unset($uids['length']);
        $status = 0; //冻结参数
        $uConn = D('User');

        $res = $uConn->manageUserStatus($uids, $status);
        if ($res === false) {
            echo '删除出错！';
        } else if ($res === 0) {
            echo '没有删除任何项！';
        } else {
            echo '删除成功！';
        }
    }

    public function saveU()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $uid = I('post.uid', 1, 'intval');
        $status = 1; //冻结参数

        $uConn = D('User');
        $res = $uConn->manageUserStatus($uid, $status);
        if ($res === false) {
            echo '解冻出错！';
        } else if ($res === 0) {
            echo '没有解冻任何项！';
        } else {
            echo '解冻成功！';
        }
    }

    public function saveUs()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $ids = I('post.', 1, 'intval');

        $json = $_POST['json'];
        $ids = json_decode($json, true);
        unset($ids['length']);
        $status = 1; //冻结参数
        $uConn = D('User');
        $res = $uConn->manageUserStatus($ids, $status);
        if ($res === false) {
            echo '解冻出错！';
        } else if ($res === 0) {
            echo '没有解冻任何项！';
        } else {
            echo '解冻成功！';
        }
    }

    public function resetPwd()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $Jids = $_POST['ids'];
        $JnewPwd = $_POST['newPwd'];
        $ids = json_decode($Jids, true);
        unset($ids['length']);
        $newPwd = json_decode($JnewPwd, true);
        unset($newPwd['length']);

        if ($newPwd === '') {
            echo '重置密码为空！';
        }

//        dump($newPwd);
//        dump($ids);
//        exit();
        $uConn = D('User');
        $res = $uConn->updateUserById($newPwd, $ids);
        if ($res === false) {
            echo '重置出错！';
        } else if ($res === 0) {
            echo '没有重置任何项！';
        } else {
            echo '重置成功！';
        }
    }

    public function upU()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $id = I('get.uid', 0, 'intval');
        $uConn = D('User');
        $uInfo = $uConn->findUserById($id);
        $this->assign('role', $role);
        $this->assign('uInfo', $uInfo);
        $this->display('Index/upU');
    }

    public function doupU()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $user_id = I('post.id', 0, 'intval');
        $user_number = trim(I('post.number', '', 'htmlspecialchars'));
        $user_name = trim(I('post.name', '', 'htmlspecialchars'));
        $user_gender = trim(I('post.gender', -1, 'intval'));
        $user_grade = trim(I('post.grade', '', 'htmlspecialchars'));
        $user_class = trim(I('post.user_class', '', 'htmlspecialchars'));
        $user_qq = trim(I('post.qq', '', 'htmlspecialchars'));
        $user_role = I('post.role', -1, 'intval');

        if ($user_id === 0) {
            echo '操作错误！';
        }
        if ($user_number === '') {
            echo '学号为空！';
        }
        if ($user_name === '') {
            echo '姓名为空！';
        }
        if ($user_gender === -1) {
            echo '性别为空';
        }
        if ($user_role === -1) {
            echo '操作错误！';
        }
        if ($user_role === '') {
            $user_role = 2;
        }
        if ($user_grade === '') {
            echo '年级为空！';
        }
        if ($user_class === '') {
            echo '班级为空！';
        }
        if ($user_qq === '') {
            $user_qq = null;
        }

        $data = array(
            'user_id' => $user_id,
            'user_number' => $user_number,
            'user_name' => $user_name,
            'user_gender' => $user_gender,
            'user_grade' => $user_grade,
            'user_class' => $user_class,
            'user_qq' => $user_qq,
            'user_role' => $user_role
        );

        $uConn = D('User');
        $res = $uConn->updateU($data);
        if ($res === false) {
            echo '更新失败！';
        } else if ($res === 0) {
            echo '没有更新数据！';
        } else {
            echo '更新成功！';
        }
    }

    //展示上传用户页面
    public function showUpLoad()
    {
        $user = $_SESSION['user'];
        $user_id = $user['user_id'];
        $this->display('Index/upLoadUser');
    }

    //处理上传表单
    public function uploadUser()
    {
        $user = $_SESSION['user'];
        $user_id = $user['user_id'];

        if (empty($_FILES['file_stu']['name'])) {
            $this->error('请上传一个Excel文件!');
        }
        $upload = new \Think\Upload(); //实例化上传类
        $upload->maxSize = 2048000;//设置附件上传大小,单位是b
        $upload->exts = array('xls', 'xlsx');//设置允许上传类型
        $upload->rootPath = './userfiles/';//设置上传文件根目录
        $upload->savePath = '';
        $upload->autoSub = false;
        $upload->thumbRemoveOrigin = true;
        $upload->saveName = date('Y-m-d H:i:s');

        //上传文件
        $info = $upload->uploadOne($_FILES['file_stu']);
        if (!$info) {//上传错误提示错误信息
            $this->error($upload->getError());
        } else {

            $fileName = _FILESPATH_ . $info['savename'];
            $data = $this->import_excel($fileName);

            $val = null;
            $udata = null;
            foreach ($data as $key => $value) {
                if ($key >= 2) {
                    if (!is_numeric($value['A'])) {
                        $this->error('学号必须是0-9数字组合！');
                    }
                    $gender = null;
                    if($value['C'] === '男'){
                        $gender = 0;
                    }
                    else if($value['C'] === '女'){
                        $gender = 1;
                    }else{
                        $this->error('性别错误:'.$value['user_gender']);
                    }
                    $val['user_number'] = (string)$value['A'];
                    $val['user_name'] = $value['B'];
                    $val['user_gender'] = $gender;
                    $val['user_grade'] = $value['D'];
                    $val['user_class'] = $value['E'];
                    $val['user_password'] = (string)$value['A'];
                }
                $udata[$key] = $val;
            }

            //将表中数据插入数据库
            $uConn = D('User');
            $res = 0;
            foreach ($udata as $key => $value) {
                if ($key >= 2) {
                    $where['user_number'] = $udata[$key]['user_number'];
                    $number = $uConn->where($where)->find();
                    if ($number) {
                        $this->error($number['user_number'] . '账号已存在');
                    }
                    $res = $uConn->createUser($udata[$key]);
                }
            }
            if ($res !== false) {
                $this->success('导入用户成功！', __MODULE__ . '/UserManage/index', 1);
            } else {
                $this->error('导入失败！');
            }

        }
    }

    /**
     * 导入excel文件
     * @param  string $file excel文件路径
     * @return array        excel文件内容数组
     */
    private function import_excel($file)
    {
        // 判断文件是什么格式
        $type = pathinfo($file);


        $type = strtolower($type["extension"]);
        ini_set('max_execution_time', '0');
        Vendor('PHPExcel.PHPExcel');

        // 判断使用哪种格式
        if ($type === 'xls') {
            $objReader = new \PHPExcel_Reader_Excel5();
        } else {
            $objReader = new \PHPExcel_Reader_Excel2007();
        }

        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        // 取得总行数
        $highestRow = $sheet->getHighestRow();
        // 取得总列数
        $highestColumn = $sheet->getHighestColumn();

        //读取excel文件
        $data = array();
        $data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
        return $data;
    }

    //导出用户文件
    public function downLoadU()
    {
        $user = $_SESSION['user'];
        $user_id = $user['user_id'];
        $this->export_xlsx();
        dump('ok');
        $this->index();
    }

    private function export_xlsx()
    {
        /**
         * PHPExcel
         *
         * Copyright (c) 2006 - 2015 PHPExcel
         *
         * This library is free software; you can redistribute it and/or
         * modify it under the terms of the GNU Lesser General Public
         * License as published by the Free Software Foundation; either
         * version 2.1 of the License, or (at your option) any later version.
         *
         * This library is distributed in the hope that it will be useful,
         * but WITHOUT ANY WARRANTY; without even the implied warranty of
         * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
         * Lesser General Public License for more details.
         *
         * You should have received a copy of the GNU Lesser General Public
         * License along with this library; if not, write to the Free Software
         * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
         *
         * @category   PHPExcel
         * @package    PHPExcel
         * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
         * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
         * @version    ##VERSION##, ##DATE##
         */

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');

        /** Include PHPExcel */
        Vendor('PHPExcel.PHPExcel');


// Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

// Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");


// Add some data
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '学号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '性别')
            ->setCellValue('D1', '年级')
            ->setCellValue('E1', '班级')
            ->setCellValue('F1', 'QQ');

        $uConn = D('User');
        $Info = $uConn->findAllUser();
        foreach ($Info as $key => $value) {
            $gender = null;
            if($value['user_gender'] == 0){
                $gender = '男';
            }
            if($value['user_gender'] == 1){
                $gender = '女';
            }
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+2),$value['user_number'] )
                ->setCellValue('B'.($key+2),$value['user_name'])
                ->setCellValue('C'.($key+2),$gender)
                ->setCellValue('D'.($key+2),$value['user_grade'])
                ->setCellValue('E'.($key+2),$value['user_class'])
                ->setCellValue('F'.($key+2),$value['user_qq']);
        }

// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.time().'.xlsx');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        dump('ok');
        exit;

    }

}