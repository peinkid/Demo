<?php
namespace app\index\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\common\model\User as UserModel;
use think\facade\Session;
class User extends Base
{
	//注册页面
	public function reg()
	{
		//检测是否开启注册
		$this->is_reg();
		$this->assign('title','用户注册');
		return $this->fetch();
	}

	//处理用户提交的注册信息
	public function insert()
	{
		if(Request::isAjax())
		{
			//验证数据
			$data=Request::post();//得到验证的数据
			$rule='app\common\validate\User';//自定义的验证规则
			//使用模型创建数据
			//获取用户通过表单提交过来的数据，排除确认密码
			//开始验证
			$res=$this->validate($data,$rule);
			if(true!==$res)//假定失败
			{
				return['status'=>-1,'message'=>$res];
			}
			else
			{
				if($user=UserModel::create($data))
			{

				//注册成功自动登录
				$res=UserModel::get($user->id);
				Session::set('user_id',$res->id);
				Session::set('user_name',$res->name);
				Session::set('is_admin',$res->is_admin);
				return ['status'=>1,'message'=>'注册成功'];
			}
			else
			{
				return ['status'=>0,'message'=>'注册失败'];
			}
			}

			//$data=Request::except('password_confirm','post');

			}
		else
		{
			$this->error("请求类型错误",'reg');
		}

	}
	//用户登录
	public function login()
	{
		$this->isLogin();
		return $this->view->fetch('login',['title'=>'用户登录']);
	}
	//登录验证与查询
	public function loginCheck()
	{
		if(Request::isAjax())
		{
			//验证数据
			$data=Request::post();//得到验证的数据
			$rule=[
			'email|邮箱'=>'require|email',
			'password|密码'=>'require|alphaNum',
			];
			//自定义的验证规则
			//使用模型创建数据
			//获取用户通过表单提交过来的数据，排除确认密码
			//开始验证
			$res=$this->validate($data,$rule);
			if(true!==$res)//假定失败
			{
				return['status'=>-1,'message'=>$res];
			}
			else
			{
				//执行查询
				$result=UserModel::get(function($query) use ($data)
				{
					$query->where('email',$data['email'])
					      ->where('password',$data['password']);
				});

				if(null == $result)
			{
				return ['status'=>0,'message'=>'登录失败,邮箱或密码不正确'];
				
			}
			else
			{
				//将用户数据写入session
				Session::set('user_id',$result->id);
				Session::set('user_name',$result->name);
				Session::set('is_admin', $result->is_admin);
				return ['status'=>1,'message'=>'登录成功'];
			}
			}

			//$data=Request::except('password_confirm','post');

			}
		else
		{
			$this->error("请求类型错误",'login');
		}
	}

	//退出登录
	public function logout()
	{
		//方法一
		/*Session::delete('user_id');
		Session::delete('user_name');
		$this->success('退出登录','demo10/test1');*/

		//方法二
		Session::clear();
		$this->success('退出登录','demo10/test1');
	}
	

}