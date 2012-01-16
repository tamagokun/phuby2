<?php
namespace Phuby;

class Arr extends Enumerable
{
	public function assoc($object)
	{
		foreach($this as $value) if((is_array($value) || $value instanceof Arr) && $value[0] == $object) return $value;
		return null;
	}
	
	public function chunk($size)
	{
		
	}
	
	public function concat($arrays)
	{
		$arrays = func_get_args();
		foreach($arrays as $array)
		{
			if($array instanceof Enumerable) $array = $array->array;
			foreach($array as $value) $this[] = $value;
		}
		return $this;
	}
	
	public function compact()
	{
		return $this->reject(function($v) { return $v == null; });
	}
	
	public function fill($start,$length,$value)
	{
		$this->array = array_fill($start,$length,$value);
		return $this;
	}
	
	public function first()
	{
		return $this[0];
	}
	
	public function flatten()
	{
		$class = $this->class;
		$result = $class::new_instance();
		foreach($this as $value)
		{
			if(is_array($value)) $value = new Arr($value);
			if($value instanceof Arr)
				foreach($value->flatten() as $flattened) $result[] = $flattened;
			else
				$result[] = $value;
		}
		return $result;
	}
	
	public function join($glue)
	{
		return join($glue, $this->array);
	}
	
	public function last()
	{
		return $this[$this->count() - 1];
	}
	
	public function offsetSet($offest,$value)
	{
		$this->super((empty($offset))? $this->count() : $offset,$value);
	}
	
	public function pack($format)
	{
		$arguments = array_merge(array($format),$this->array);
		return call_user_func_array("\pack",$arguments);
	}
	
	public function pop()
	{
		return array_pop($this->array);
	}
	
	public function push($arguments)
	{
		$arguments = func_get_args();
		foreach($arguments as $argument) $this[] = $argument;
		return $this;
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