<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Site as SiteModel;
use think\facade\Request;
use think\facade\Session;
class Site extends Base
{
	//站点管理首页
	public function index()
	{
		//获取站点信息
		$siteInfo=SiteModel::get(['status'=>1]);
		//模板赋值
		$this->view->assign('siteInfo',$siteInfo);
		//渲染模板
		return $this->view->fetch('index');
	}
	//保存站点修改信息
	public function save()
	{
		//获取数据
		$data=Request::param();
		//更新操作
		if(SiteModel::update($data))
		{
			$this->success('操作成功','index');
		}
		$this->error('操作失败','index');
	}
}