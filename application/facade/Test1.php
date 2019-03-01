<?php
namespace app\facade;

//代理 app\common\controller\Test1 的代理类

class Test1 extends \think\Facade
{
	protected static function getFacadeClass()
	{
		/*
         若使用动态绑定，app\facade\Test1 下的getFacadeClass方法为空：
         protected static function getFacadeClass()
         {}
        */
		//return 'app\common\controller\Test1';//非动态绑定情况下使用该行
	}
}