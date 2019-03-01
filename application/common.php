<?php
use think\Db;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件



//根据用户主键id查询用户名称
if(!function_exists('getUserName'))
{
	function getUserName($id)
	{
		return Db::table('zh_user')
		->where('id',$id)
		->value('name');
	}
}
if(!function_exists('getUserLv'))
{
	function getUserLv($id)
	{
		return Db::table('zh_user')
		->where('id',$id)
		->value('lv');
	}
}

if(!function_exists('getUserNum'))
{
	function getUserNum($id)
	{
		return Db::table('zh_article')
		->where('user_id',$id)
		->count();
	}
}

//过滤文章内容
if(!function_exists('getArtContent'))
{
	function getArtContent($content)
	{
		return mb_substr(strip_tags(str_replace('&nbsp;','',$content)),0,15).'...';
	}
}

//获取栏目名称
if(!function_exists('getCateName'))
{
	function getCateName($cateId)
	{
		return Db::table('zh_article_category')
		->where('id',$cateId)
		->value('name');
	}
}
