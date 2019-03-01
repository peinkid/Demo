<?php
namespace app\common\validate;
use think\Validate;
//文档表的字段验证器
class Article extends Validate
{
	//当前验证规则
	protected $rule=[
	'title|标题'=>'require|length:2,20|chsAlphaNum',
	'title_image|标题图片'=>'image',
	'content|文章内容'=>'require',
	'user_id|作者'=>'require',
	'cate_id|栏目名称'=>'require',
	];
}