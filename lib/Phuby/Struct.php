<?php
namespace Phuby;

class Struct extends Object
{
	public $members = array();
	
	public function initialize($members)
	{
		$this->members = (is_array($members))? $members : func_get_args();
	}
	
	public function count()
	{
		return count($this->members);
	}
	
	public function instance($values)
	{
		$class = $this->class;
		$instance = $class::new_instance($this->members);
		$values = func_get_args();
		foreach($this->members as $key=>$member) $instance->member = $values[$key];
	}
	
	public function select($block)
	{
		
	}
	
	public function to_a()
	{
		
	}
	
	public function to_h()
	{
		
	}
	
	public function values_at($keys)
	{
		
	}
}
Struct::alias_method('length', 'count');
Struct::alias_method('size', 'count');
Struct::alias_method('values', 'to_a');