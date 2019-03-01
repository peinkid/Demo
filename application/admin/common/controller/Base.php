<?php
//后台公共控制器
namespace app\admin\common\controller;
use think\Controller;
use think\facade\Session;
class Base extends Controller
{
	//初始化方法
	protected function initialize()
	{

	}
	//用户是否登录 调用位置：后台入口：admin/inde/index()
	protected function unLogin()
	{
		if(!Session::has('user_id'))
		{
			$this->error('您尚未登录','admin/user/login');
		}
	}

}