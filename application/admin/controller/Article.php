<?php
namespace app\admin\controller;
use app\admin\common\controller\Base;
use app\admin\common\model\Article as ArtModel;
use app\admin\common\model\Cate;
use think\facade\Request;
use think\facade\Session;
class Article extends Base
{
	//文章管理的首页
	public function index()
	{
		//检查用户是否登录
		$this->unLogin();
		//登录后跳转文章管理界面
		return $this->redirect('artList');
	}
	//文章列表
	public function artList()
	{
		//检查用户是否登录
		$this->unLogin();

		//获取当前用户id和级别
		$userId=Session::get('user_id');
		$isAdmin=Session::get('is_admin');
		//获取当前用户发布的文章
		$artList=ArtModel::where('user_id',$userId)->paginate(3);
		//管理员获取全部文章
		if($isAdmin==1)
		{
			$artList=ArtModel::paginate(3);
		}
		//设置模板变量
		$this->view->assign('title','文章管理');
		$this->view->assign('empty','<span style="color:red">没有文章</span>');
		$this->view->assign('artList',$artList);
		//渲染模板
		return $this->view->fetch('artlist');
	}
	//渲染文章编辑的界面
	public function artEdit()
	{
		//获取文章id
		$artId=Request::param('id');
		//根据主键查询要更新的文章信息
		$artInfo=ArtModel::where('id',$artId)->find();
		//检查栏目信息
		$cateNow=Cate::where('id',$artInfo->cate_id)->find();
		$cateList=Cate::all();
		//设置模板变量
		$this->view->assign('title','编辑文章');
		$this->view->assign('cateList',$cateList);
		$this->view->assign('artInfo',$artInfo);
		$this->view->assign('cateNow',$cateNow);
		//渲染模板
		return $this->view->fetch('artedit');
	}
	//执行编辑操作
	public function doEdit()
	{
		//获取用户提交的信息
		$data=Request::param();
		//获取上传的图片
		$file=Request::file('title_image');
				if($file==null)
				{
					echo'<script>
					alert("图片不能为空");
					window.history.go(-1);  
					</script>';
					return null;
				}
				//文件信息验证，成功后再上传到服务器指定目录,以public为起始目录
				$info=$file->validate([
					'size'=>1000000,
					'ext'=>'jpeg,jpg,png,gif',
					])->move('uploads/');
				if($info)
				{
					$data['title_image']=$info->getSaveName();
				}
				else
				{
					$this->error($file->getError());
				}
				//将数据写到数据表
				if(ArtModel::update($data))
				{
					$this->success('文章更新成功','artList');
				}
				else
				{
					$this->error('文章更新失败');
				}
	}
	//执行删除操作
	public function doDelete()
	{
		//获取要删除的主键id
		$artId=Request::param('id');
		//执行删除
		if(ArtModel::destroy($artId))
		{
			return $this->success('删除成功','artList');
		}
		//删除失败
		$this->error('删除失败');
	}

} 