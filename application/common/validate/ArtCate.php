<?php
namespace app\common\validate;
use think\Validate;
//栏目表的字段验证器
class ArtCate extends Validate
{
	//当前验证规则
	protected $rule=[
	'name|栏目名称'=>'require|length:3,20|chsAlphaNum',
	];
}