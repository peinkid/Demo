<?php
//zh_article_category表的用户模型
namespace app\common\model;
use think\Model;
class Friend extends Model
{
	protected $pk='id';//默认主键
	protected $table='zh_friend';//默认数据表
}