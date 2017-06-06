<?php
define('_SPORTIMGPATH_','/home/sun/www/sport/images/');
define('_FILESPATH_','/home/sun/www/sport/userfiles/');
return array(
	//'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '_PUBLIC_' => __ROOT__.'/Public',
        '_IMG_' => __ROOT__.'/images',
        '_FILES_'=>__ROOT__.'/userfiles'
    ),
    'rootPath'=>__ROOT__.'/images',
    'TMPL_L_DELIM'    =>    '<{', //模板里的左定界符
    'TMPL_R_DELIM'    =>    '}>', //模板里的右定界符
//    'SHOW_PAGE_TRACE'=>true,
    'DB_TYPE'=>'mysql',//数据库类型
    'DB_HOST'=>'localhost',//服务器地址
    'DB_NAME'=>'runner',//数据库名字
    'DB_USER'=>'root',//用户名
    'DB_PWD'=>'199648sth',//密码
    'DB_PORT'=>'3306',//端口
    'DB_PARAMS'=>array(),//数据库连接参数
    'DB_PREFIX'=>'',//数据库表前缀
    'DB_CHARSET'=>'utf8',//数据库字符集
    'DB_DEBUG'=>TRUE,//数据库调试模式 开启后可以记录SQL日志
);
