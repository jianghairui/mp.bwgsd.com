<?php
/**
 * Created by PhpStorm.
 * User: Jiang
 * Date: 2019/12/6
 * Time: 16:38
 */
// [ 应用入口文件 ]

namespace think;

define('APP_PATH',__DIR__);
define('ROOT_PATH',dirname(__DIR__));
// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';
// 支持事先使用静态方法设置Request对象和Config对象
// 执行应用并响应
Container::get('app')->bind('admin')->run()->send();

require ROOT_PATH . '/vendor/autoload.php';