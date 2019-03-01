<?php
namespace app\admin\controller;
//use think\facade\Config;
class Indexconfig
{
	public function index()
	{
		//dump(config::get());//获取全部配置文件
		//dump(config::get('app.'));//获取app配置文件
		//dump(config::pull('app'));//获取app配置文件
		//dump(config::get('app.app_name'));//获取二级配置项，前缀app.可省略
		//dump(config::get('default_lang'));
		//dump(config::has('default_lang'));//判断是否存在某个设置参数
		//config::set('app_debug',true);//修改配置
		//config::set('app_author','peinkid');//添加配置
		//---------以下为助手函数config简化：
		//dump(config());//助手函数config()不依赖Config类，注释line3
		//dump(config('default_lang'));//查询
		//dump(config('?default_lang'));//判断是否存在某个设置参数
		//dump(config('app_author','peinkid'));//修改添加配置
// 		config([
//     'app_debug'=>true,
//     'app_author'=>'peinkid'
// ],'app');//批量配置
	}
}