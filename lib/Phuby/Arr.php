<?php
namespace Phuby;

class Arr extends Enumerable
{
	public function assoc($object)
	{
		
	}
	
	public function chunk($size)
	{
		
	}
	
	public function concat($arrays)
	{
		
	}
	
	public function compact()
	{
		
	}
	
	public function fill()
	{
		
	}
	
	public function first()
	{
		
	}
	
	public function flatten()
	{
		
	}
	
	public function join($glue)
	{
		
	}
	
	public function last()
	{
		
	}
	
	public function offsetSet($offest,$value)
	{
		$this->super((empty($offset))? $this->count() : $offset,$value);
	}
	
	public function pack($format)
	{
		
	}
	
	public function pop()
	{
		
	}
	
	public function push($arguments)
	{
		
	}
	
	public function rand($quantity=1)
	{
		
	}
	
	public function rassoc($object)
	{
		
	}
	
	public function reverse()
	{
		
	}
	
	public function shift()
	{
		
	}
	
	public function shuffle()
	{
		
	}
	
	public function slice($offset,$length)
	{
		
	}
	
	public function splice($offset,$length=0,$replacement=array())
	{
		
	}
	
	public function to_h()
	{
		
	}
	
	public function transpose()
	{
		
	}
	
	public function unique()
	{
		
	}
	
	/*public function unshift($arguments)
	{
		
	}*/
	
	public function unshift($value)
	{
		return array_unshift($this->array, $value);
	}
}
Arr::alias_method('implode', 'join');
Arr::alias_method('in_groups_of', 'chunk');
Arr::alias_method('uniq', 'unique');