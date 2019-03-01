<?php
namespace app\validate;
use think\Validate;
//用户信息表的字段验证器
class User extends Validate
{
	//当前验证规则
	protected $rule=[
	'name|用户名'=>'require|length:5,20|chsAlphaNum',
	'email|邮箱'=>'require|email|unique:zh_user',
	'mobile|手机'=>'require|mobile|unqiue:zh_user',
	'password|密码'=>'require|length:6,10|alphaNum|confirm',
	];
}