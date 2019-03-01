<?php
namespace app\index\controller;
use app\common\controller\Base;
use app\common\model\Article;
use app\common\model\ArtCate;
use app\common\model\Image;
use app\common\model\ChildCate;
use app\common\model\Comment;
use app\common\model\Fav;
use app\common\model\Like;
use app\common\model\Friend;
use app\common\model\User;
use think\facade\Request;
use think\facade\Session;
use think\Db;
use think\File;
class Demo10 extends Base
{
	//首页
	public function test1()
	{
		//全局查询条件
		$map=[];//将所有查询条件封装到数组中
		//条件1
		$map[]=['status','=',1];//不可省略等号
		//实现搜索
		$keywords=Request::param('keywords');
		if(!empty($keywords))
		{
			//条件2
			$map[]=['title','like','%'.$keywords.'%'];
		}
		//分类信息显示
		$cateId=Request::param('cate_id');
		
		if(isset($cateId))
		{
			//条件3
			$map[]=['cate_id','=',$cateId];

			$res=ArtCate::get($cateId);
			
			//列表信息分页显示
			$artList=Db::table('zh_article')
			->where($map)
			->where('cate_id',$cateId)
			->order('create_time','desc')
			->paginate(4);
			$this->view->assign('cateName',$res->name);
			$this->view->assign('title',$res->name);



		}
		else
		{
			$this->view->assign('cateName','全部话题');
			$artList=Db::table('zh_article')
			->where($map)
			->order('create_time','desc')
			->paginate(4);
		}
		$this->view->assign('empty','<h3>没有文章</h3>');
		$this->view->assign('artList',$artList);
		return $this->fetch();
	}

	//添加文章界面
	public function insert()
	{
		//1.登录才可以提问
		$this->unLogin();
		//2.设置页面标题
		$this->view->assign('title','我要上货');
		//3.获取栏目信息
		$cateList=ArtCate::all();
		Session::set('imgid',Session::get('user_name').time());
		if(count($cateList)>0)
		{
			//将查询到的栏目信息赋值给模板
			$this->assign('cateList',$cateList);
		}
		else
		{
			$this->error('请先添加栏目','demo10/test1');
		}
		//4.渲染发布界面
		return $this->view->fetch('insert');

	}
	//保存文章
	public function save()
	{
		//判断提交类型
		if(Request::isPost())
		{
			//获取用户提交的信息
			$data=Request::post();
			//halt($data);//显示数据
			
			$res=$this->validate($data,'app\common\validate\Article');
			if(true!==$res)
			{
				echo'<script>
				alert("'.$res.'");
				window.history.go(-1);  
				</script>';
			}
			else
			{
				//获取上传图片信息
				$file=Request::file('title_image');
				//halt($file);
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
					$data['imgid']=Session::get('imgid');
				}
				else
				{
					$this->error($file->getError());
				}
				//将数据写到数据表
				if(Article::create($data))
				{

					$this->success('文章发布成功','demo10/test1');


				}
				else
				{
					$this->error('文章发布失败');
				}

			}

		}
		else
		{
			$this->error('请求类型错误');
		}
	}

	//页面详情
	public function detail()
	{
		$data1=Request::param('id');
		$data2=Session::get('user_id');
		$data3=Article::where('id',$data1)->value('user_id');
		$data4=Article::where('id',$data1)->value('imgid');
		if(is_null($data2))
		{
			$this->error('请先登录','user/login');
		}
		$map[]=['article_id','=',$data1];
		$map[]=['user_id','=',$data2];
		$map2[]=['user_id','=',$data2];
		$map2[]=['friend_id','=',$data3];
		$myFav=Fav::where($map)->find();
		$myLike=Like::where($map)->find();
		$myFri=Friend::where($map2)->find();
		$artId=Request::param('id');
		$showimg=Db::table('zh_image')->where('id','=',$data4)->find();
		$art=Article::get(function($query) use ($artId)
		{
			$query->where('id','=',$artId)->setInc('pv');
		});

		if(!is_null($art))
		{
			$this->view->assign('art',$art);
			$this->view->assign('myFav',$myFav);
			$this->view->assign('myLike',$myLike);
			$this->view->assign('myFri',$myFri);
			$this->view->assign('showimg',$showimg);
			
		}
		//添加评论信息
		$this->view->assign('commentList',Comment::all(function($query) use ($artId)
			{
				$query->where('status',1)
				->where('article_id',$artId)
				->order('create_time','desc');
			}));
		$this->view->assign('title','详情');
		return $this->view->fetch('detail');
	}

	//收藏
	public function fav()
	{
		
		if(!Request::isAjax())
		{
			return['status'=>-1,'message'=>'请求类型错误']; 
		}
		//获取从前端传递过来的数据
		$data=Request::param();
		//halt($data);
		//判断用户是否登录
		if(empty($data['session_id']))
		{
			return['status'=>-2,'message'=>'请先登录'];
		}
		//查询条件
		$map[]=['user_id','=',$data['session_id']];
		$map[]=['article_id','=',$data['article_id']];
		$fav=Db::table('zh_user_fav')->where($map)->find();
		if(is_null($fav))
		{
			Db::table('zh_user_fav')->data([
				'user_id'=>$data['session_id'],
				'article_id'=>$data['article_id'],
				])->insert();
			return ['status'=>1,'message'=>'取消收藏'];
		}
		else
		{
			Db::table('zh_user_fav')->where($map)->delete();
			return ['status'=>0,'message'=>'收藏'];
		}
	}
	public function friend()
	{
		
		if(!Request::isAjax())
		{
			return['status'=>-1,'message'=>'请求类型错误']; 
		}
		//获取从前端传递过来的数据
		$data=Request::param();
		//halt($data);
		//判断用户是否登录
		if(empty($data['session_id']))
		{
			return['status'=>-2,'message'=>'请先登录'];
		}
		//查询条件
		$map[]=['user_id','=',$data['session_id']];
		$map[]=['friend_id','=',$data['friend_id']];
		$friend=Db::table('zh_friend')->where($map)->find();
		if(is_null($friend))
		{
			Db::table('zh_friend')->data([
				'user_id'=>$data['session_id'],
				'friend_id'=>$data['friend_id'],
				])->insert();
			return ['status'=>1,'message'=>'取消关注'];
		}
		else
		{
			Db::table('zh_friend')->where($map)->delete();
			return ['status'=>0,'message'=>'关注好友'];
		}
	}
	public function like()
	{

		if(!Request::isAjax())
		{
			return['status'=>-1,'message'=>'请求类型错误']; 
		}
		//获取从前端传递过来的数据
		$data=Request::param();
		//判断用户是否登录
		if(empty($data['session_id']))
		{
			return['status'=>-2,'message'=>'请先登录'];
		}
		//查询条件
		$map[]=['user_id','=',$data['session_id']];
		$map[]=['article_id','=',$data['article_id']];
		$like=Db::table('zh_user_like')->where($map)->find();
		if(is_null($like))
		{
			Db::table('zh_user_like')->data([
				'user_id'=>$data['session_id'],
				'article_id'=>$data['article_id'],
				])->insert();
			return ['status'=>1,'message'=>'取消点赞'];
		}
		else
		{
			Db::table('zh_user_like')->where($map)->delete();
			return ['status'=>0,'message'=>'点赞'];
		}
	}
	public function insertComment()
	{	
		if(Request::isAjax())
		{
			//获取评论
			$data1=Request::param();
			$data2=Session::get('user_id');
			$data1['user_id']=$data2;
			$data1['content']=nl2br($data1['content']);
			//halt($data);
			//将评论存到表
			if(strlen($data1['content'])==0)
			{
				//halt($data['content']);
				return ['status'=>0,'message'=>'发送内容为空'];	
			}
			else
			{
				//halt($data['content']);
				Comment::create($data1,true);
				return ['status'=>1,'message'=>'发送成功'];
			}
		}
		
	}
	public function webuploader()
	{
		return view();
		//return Session::get('art_id');
	}
	public function moreimage()
	{
		$file = Request::file('file');
	    $info=$file->move('up_new/');
	    $imgname=$info->getSaveName();
	    $i=Session::get('imgid');
	    if(Image::where('id',$i)->find()==null)
	    {
	    	Image::create(['id'=>$i]);
	    }
	    	$fir=Image::where('id',$i)->value('fir_image');
		    $sec=Image::where('id',$i)->value('sec_image');
		    $thi=Image::where('id',$i)->value('thi_image');
			if($fir==null)
			{
				Image::update(['fir_image'=>$imgname,'id'=>$i]);
					
			}
			elseif ($sec==null) {
				Image::update(['sec_image'=>$imgname,'id'=>$i]);
					
			}
			else
			{
				Image::update(['thi_image'=>$imgname,'id'=>$i]);
					
			}


	}
	public function testdb()
	{
		//return $i=Session::get('imgid');
		//$fir=Image::where('id',$i)->value('fir_image');

		//$imgId=Image::field('id')->select();
		//return Article::where('imgid','=',$imgId)->select();
		
	}
	public function addexp()
	{
		$data=Session::get('user_id');
		$map[]=['id','=',$data];
		//$exp=User::where($map)->find();
		$fir=User::where($map)->value('fir');
		$fir_lv=User::where($map)->value('lv');
		$et=strtotime(date('Y-m-d'));
		if($et-(User::where($map)->value('etime'))>=86400)
            {
                    User::where($map)->setInc('fir',10);
                    User::where($map)->update(['etime'=>$et]); 
                if(User::where($map)->value('fir')==User::where($map)->value('last'))
                {
                	User::where($map)->setInc('last',50);
                	User::where($map)->setInc('lv');
                    User::where($map)->update(['fir'=>0]);
                    return ['message'=>'恭喜升级','newfir'=>0,'newlv'=>User::where($map)->value('lv'),'newlast'=>User::where($map)->value('last')];
                }
                else
                {
                	return ['message'=>'签到成功','newfir'=>User::where($map)->value('fir'),'newlv'=>User::where($map)->value('lv'),'newlast'=>User::where($map)->value('last')];
                }
            }
            else
            {
            	return ['message'=>'您已签到'];
            }    
               

	}
	public function flist()
	{
		

		$friendId=Request::param('friend_id');
			if(isset($friendId))
		{
			//条件3
			$map2[]=['user_id','=',$friendId];

						
			//列表信息分页显示
			$artList=Db::table('zh_article')
			->where($map2)
			->order('create_time','desc')
			->paginate(4);
			$this->view->assign('cateName','好友文章');
			
		}
		else{
			$map[]=['user_id','=',Session::get('user_id')];	
		$res=Friend::field('friend_id')->where($map)->select();
		$map1=[];
		foreach($res as $k=>$val){ 
        if ( $val == "" ) {
            continue;
        }
    	$map1[]= $val['friend_id'];
    
		}
		$data=implode(',',$map1);
		$artList =Db::table('zh_article')
			->where('user_id','in',$data)
			->order('create_time','desc')
			->paginate(4); 
			
			$this->view->assign('cateName','好友文章');
			
		}
		$this->view->assign('artList',$artList);
		$this->view->assign('empty','<h3>没有文章</h3>');
			return $this->fetch();
	}
}
