<?php
//基础控制器，必须继承think\Controller.php
namespace app\common\controller;
use think\Controller;
use think\facade\Session;
use app\common\model\ArtCate;
use app\admin\common\model\Site;
use think\facade\Request;
use app\common\model\Article;
use app\common\model\User;
use app\common\model\Friend;
use app\common\model\ChildCate;
class Base extends Controller
{
	protected function initialize()
	{
		//初始化方法
		//创建常量、公共方法
		//在所有的方法之前被调用
		$this->showNav();//显示分类导航
		$this->showChildNav();//显示子分类
		$this->is_open();//检测网站是否关闭
		$this->getPv();//获取右侧热门排行
		$this->exp();
		$this->getF();
	}

	//防止重复登录----全局
	protected function isLogin()
	{
		if(Session::has('user_id'))
		{
			$this->error('您已登录','demo10/test1');
		}
	}
	//检测是否未登陆
	protected function unLogin()
	{
		if(!Session::has('user_id'))
		{
			$this->error('您尚未登录','user/login');
		}
	}

	//显示导航
	protected function showNav()
	{
		//查询栏目表，获取所有分类信息
		$cateList=ArtCate::all(function($query)
			{
				$query->where('status',1)->order('sort','asc');
			});
		//将栏目信息赋值给模板 nav.html
		$this->view->assign('cateList',$cateList);

	}
	//显示子导航
	protected function showChildNav()
	{
		//查询栏目表，获取所有分类信息
		$childList=ChildCate::all(function($query)
			{
				$query->where('status',1)->order('sort','asc');
			});
		//将栏目信息赋值给模板 nav.html
		$this->view->assign('childList',$childList);

	}

	//检测站点是否关闭
	public function is_open()
	{
		//获取站点状态
		$isOpen=Site::where('status',1)->value('is_open');
		//若关闭，只允许关闭前台
		if($isOpen==0 && Request::module()=='index')
		{
			//关掉网站
			$info = <<<'INFO'
            <body style="background-color:#333">
            <h1 style="color:#eee;text-align:center;margin:200px">站点维护中...</h1>
            </body>
INFO;
            exit($info);
		}
	}

	//检测是否允许用户注册
	public function is_reg()
	{
		//获取注册状态
		$isReg=Site::where('status',1)->value('is_reg');
		//若关闭，直接回到首页
		if($isReg==0)
		{
			$this->error('注册关闭','demo10/test1');
		}

	}

	//根据阅读量pv获取内容
	public function getPv()
	{
		$pvList=Article::where('status',1)->order('pv','desc')->limit(9)->select();
		$this->view->assign('pvList',$pvList);
	}
	public function exp()
	{
		//查询条件
		if($data=Session::get('user_id'))
		{
		$map[]=['id','=',$data];
		$exp=User::where($map)->find();
		$this->view->assign('exp',$exp);
		}
		// Db::table('zh_user_fav')->data([
		// 		'user_id'=>$data['session_id'],
		// 		'article_id'=>$data['article_id'],
		// 		])->insert();
		
	}
	public function getF()
	{
		if($data=Session::get('user_id'))
		{
			$map[]=['user_id','=',$data];
			$flist=Friend::where($map)->select();
			$this->view->assign('flist',$flist);	
		}
		
	}

}