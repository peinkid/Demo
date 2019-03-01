<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Cate as CateModel;
use think\facade\Request;
use think\facade\Session;
class Cate extends Base
{
	//分类管理的首页
	public function index()
	{
		//检查用户是否登录
		$this->unLogin();
		//登录后跳转分类管理界面
		return $this->redirect('cateList');
	}
	//分类列表
	public function cateList()
	{
		//检查用户是否登录
		$this->unLogin();
		//获取所有分类
		$cateList=CateModel::all();
		//设置模板变量
		$this->view->assign('title','分类管理');
		$this->view->assign('empty','<span style="color:red">没有分类</span>');
		$this->view->assign('cateList',$cateList);
		//渲染模板
		return $this->view->fetch('catelist');
	}
	//渲染分类编辑的界面
	public function cateEdit()
	{
		//获取分类id
		$cateId=Request::param('id');
		//根据主键查询要更新的分类信息
		$cateInfo=CateModel::where('id',$cateId)->find();
		//设置模板变量
		$this->view->assign('title','编辑分类');
		$this->view->assign('cateInfo',$cateInfo);
		//渲染模板
		return $this->view->fetch('cateedit');
	}
	//执行编辑操作
	public function doEdit()
	{
		//获取用户提交的信息
		$data=Request::param();
		//取出主键
		$id=$data['id'];
		
		//删除主键
		unset($data['id']);
		//执行编辑
		if(CateModel::where('id',$id)->data($data)->update())
		{
			return $this->success('编辑成功','cateList');
		}
		//编辑失败
		$this->error('没有编辑或编辑失败');
	}
	//执行删除操作
	public function doDelete()
	{
		//获取要删除的主键id
		$id=Request::param('id');
		//执行删除
		if(CateModel::where('id',$id)->delete())
		{
			return $this->success('删除成功','cateList');
		}
		//删除失败
		$this->error('删除失败');
	}
	//渲染添加界面
	public function cateAdd()
	{
		
		return $this->view->fetch('cateadd',['title'=>'添加分类']);
	}
	//添加分类
	public function doAdd()
	{
		//获取要添加的数据
		$data=Request::param();
		//执行添加并判断是否成功
		if(CateModel::create($data))
		{
			$this->success('添加成功','cateList');
		}
		$this->error('添加失败');
	}
} 