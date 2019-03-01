<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\User as UserModel;
use think\facade\Request;
use think\facade\Session;
class User extends Base
{
	//渲染登录界面
	public function login()
	{
		$this->view->assign('title','管理员登录');
	    return $this->view->fetch('login');
	}
	//验证后台登录
	public function checkLogin()
	{
		
		$data=Request::param();
		//查询条件
		$map[]=['email','=',$data['email']];
		$map[]=['password','=',$data['password']];
		$result=UserModel::where($map)->find();
		if($result)
		{
			Session::set('user_id',$result['id']);
			Session::set('user_name',$result['name']);
			Session::set('is_admin',$result['is_admin']);
			$this->success('登录成功','admin/user/userList');
		}
		$this->error('登录失败');
	}
	//退出登录
	public function logout()
	{
		//清除session
		Session::clear();
		//退出登录并跳转到登录界面
		$this->success('退出成功','admin/user/login');
	}
	//用户列表
	public function userList()
	{
		//获取当前用户id和级别is_admin
		$data['user_id']=Session::get('user_id');
		$data['is_admin']=Session::get('is_admin');
		//获取当前用户信息
		$userList=UserModel::where('id',$data['user_id'])->select();
		//如果是超级管理员，可获取全部信息
		if($data['is_admin']==1)
		{
			$userList=UserModel::select();
		}
		//模板赋值
		$this->view->assign('title','用户管理');
		$this->view->assign('empty','<span style="color:red">没有数据</span>');
		$this->view->assign('userList',$userList);
		//渲染模板
		return $this->view->fetch('userList');
	}
	//渲染编辑界面
	public function userEdit()
	{
		//获取要更新的用户主键
		$userId=Request::param('id');
		//根据主键进行查询
		$userInfo=UserModel::where('id',$userId)->find();
		//设置编辑界面的模板变量
		$this->view->assign('title','编辑用户');
		$this->view->assign('userInfo',$userInfo);
		//渲染模板
		return $this->view->fetch('userEdit');
	}
	//执行编辑保存
	public function doEdit()
	{
		//获取用户提交的信息
		$data=Request::param();
		//取出主键
		$id=$data['id'];
		//将用户密码加密再保存回去
		$data['password']=$data['password'];
		//删除主键
		unset($data['id']);
		//执行编辑
		if(UserModel::where('id',$id)->data($data)->update())
		{
			return $this->success('编辑成功','userList');
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
		if(UserModel::where('id',$id)->delete())
		{
			return $this->success('删除成功','userList');
		}
		//删除失败
		$this->error('删除失败');
	}
}