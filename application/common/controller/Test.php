<?php
namespace app\common\controller;
class Test
{
	private $name;
	public function __construct($name='pein')
	{
		$this->name=$name;
	}
	public function setName($name)
	{
		$this->name=$name;
	}
	public function getName()
	{
		return '方法是:'.__METHOD__.' 属性是:'.$this->name;
	}
}