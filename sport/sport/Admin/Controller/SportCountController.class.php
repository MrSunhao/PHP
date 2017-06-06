<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16-12-18
 * Time: 下午1:33
 */

namespace Admin\Controller;


use Think\Controller;

class SportCountController extends Controller
{
    public function index()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);
        $uConn = D('User');
        $tConn = D('Topic');

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


        $this->assign('ldata', $ldata);
        $this->assign('sdata', $sdata);
        $this->assign('stype', json_encode($stype));
        $this->assign('sval', json_encode($sval));
        $this->display('Index/sportCount');
    }

    public function days_sport()
    {
        //得到每日运动统计
        $user = $this->is_Login();
        $role = $this->is_role($user);
        $uConn = D('User');
        $tConn = D('Topic');

        $rows = $uConn->count();
        $p = I('post.p', null);
        $date = I('post.date',null);
        if($date === '' || $date === null){
            $date = date('Y-m-d');
        }
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
        $day_res = $tConn->day_info($start, $nu,$date);
        $day_res['p'] = $p;
        $day_res['pages'] = $pages;
        echo json_encode($day_res);
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

    public function sportCnt()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);

        $p = 1; //当前页
        $row = 10; //展示条数
        $rows = 1; //总条数
        $pages = 1; //总页数
        $start = 0;//分页查询时开始的行数

        $p = I('post.p', 0, 'intval');
        $range = I('post.range', null);

        if ($range != 'week' && $range != 'month') {
            echo '参数错误！';
            exit();
        }
        $uConn = D('User');
        $tConn = D('Topic');
        $rows = $uConn->where(['user_status' => 1])->count();

        if ($rows % $row !== 0) {
            $pages = floor($rows / $row) + 1;
        } else {
            $pages = $rows / $row;
        }
        $start = ($p - 1) * $row;
        $data = $tConn->sportCount($range, $start, $row);
        $data['page'] = array(
            'p' => $p,
            'pages' => $pages
        );

        echo json_encode($data);
    }

    public function downLoad_days()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);
        $uConn = D('User');
        $tConn = D('Topic');
        $date = trim(I('post.date',null));
        if($date === '' || $date === null){
            $date = date('Y-m-d');
        }
        $rows = $uConn->where(['user_status' => 1])->count();
        $arr = $tConn->sportCount($date, 0, $rows);
        $this->export_xlsx($arr);
    }

    public function downLoad_week()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);
        $uConn = D('User');
        $tConn = D('Topic');
        $rows = $uConn->where(['user_status' => 1])->count();
        $arr = $tConn->sportCount('week', 0, $rows);
        $this->export_xlsx($arr);
    }
    public function downLoad_month()
    {
        $user = $this->is_Login();
        $role = $this->is_role($user);
        $uConn = D('User');
        $tConn = D('Topic');
        $rows = $uConn->where(['user_status' => 1])->count();
        $arr = $tConn->sportCount('month', 0, $rows);
        $this->export_xlsx($arr);
    }
    private function export_xlsx($arr)
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
            ->setCellValue('A1', '序号')
            ->setCellValue('B1', '学号')
            ->setCellValue('C1', '姓名')
            ->setCellValue('D1', '班级')
            ->setCellValue('E1', '运动时间(分钟)')
            ->setCellValue('F1', '运动距离(米)')
            ->setCellValue('G1', '总完成度');

        foreach ($arr as $key => $value) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+2),$key+1 )
                ->setCellValue('B'.($key+2),$value['user_number'] )
                ->setCellValue('C'.($key+2),$value['user_name'])
                ->setCellValue('D'.($key+2),$value['user_class'])
                ->setCellValue('E'.($key+2),$value['stime'])
                ->setCellValue('F'.($key+2),$value['sdata'])
                ->setCellValue('G'.($key+2),($value['rate']*100).'%');
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