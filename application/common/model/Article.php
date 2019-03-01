<?php
//zh_article表的用户模型
namespace app\common\model;
use think\Model;
class Article extends Model
{
	protected $pk='id';//默认主键
	protected $table='zh_article';//默认数据表
	protected $autoWriteTimestamp=true;//自动时间戳
	protected $createTime='create_time';
	protected $updateTime='update_time';
	protected $dateFormat='Y年m月d日';

	//开启自动设置
	protected $auto=[];//某个字段无论新增还是更新都要设置的字段
	//仅新增时有效
	protected $insert=['create_time','status'=>1,'is_top'=>0,'is_hop'=>0];
	//仅更新时有效
	protected $update=['update_time'];
	

}