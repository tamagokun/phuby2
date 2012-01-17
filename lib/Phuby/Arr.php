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
		$result = new $this->class();
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
		return new $this->class(array_rand($this->array,$quantity));
	}
	
	public function rassoc($object)
	{
		foreach($this as $value) if((is_array($value) || $value instanceof Arr) && $value[1] == $object) return $value;
		return null;
	}
	
	public function reverse()
	{
		return new $this->class(array_reverse($this->array));
	}
	
	public function shift()
	{
		return array_shift($this->array);
	}
	
	public function shuffle()
	{
		$array = $this->array;
		shuffle($array);
		return new $this->class(array($array));
	}
	
	public function slice($offset,$length)
	{
		return new $this->class(array_slice($this->array,$offset,$length));
	}
	
	public function splice($offset,$length=0,$replacement=array())
	{
		array_splice($this->array,$offset,$length,$replacement);
		return $this;
	}
	
	public function to_h()
	{
		return $this->chunk(2)->inject(new Hash,function($v,$k,$o) { $o[$v[0]] = $v[1]; return $o; });
	}
	
	public function transpose()
	{
		$size = $this->count();
	}
	
	public function unique()
	{
		return new $this->class(array_unique($this->array));
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