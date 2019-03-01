<?php
namespace app\common\controller;

//要被代理的类

class Test1
{
	public function hello($name)
	{
		return 'Hello '.$name;
	}
}