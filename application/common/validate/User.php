<?php
//zh_user表的验证器
namespace app\common\validate;
use think\Validate;
class User extends Validate
{
	protected $rule=[
	'name|姓名'=>[
	'require',
	'length'=>'3,10',
	'chsAlphaNum',
	'unique'=>'zh_user'
	],

	'email|邮箱'=>[
	'require',
	'email',
	'unique'=>'zh_user',//该字段必须在zh_user中唯一
	],

	'mobile|手机'=>[
	'require',
	'number',
	'unique'=>'zh_user',
	'mobile',
	],

	'password|密码'=>[
	'require',
	'length'=>'6,15',
	'alphaNum',
	'confirm'//自动与password_confirm字段进行相等验证
	]
	];
}
