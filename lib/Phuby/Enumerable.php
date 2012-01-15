<?php
namespace Phuby;

class Enumerator extends Object implements \Iterator, \ArrayAccess, \Countable
{
	public $array;
	public $default;
	public $valid = false;
	
	public function initialize($array=array(),$default=null)
	{
		$this->array = $array;
		$this->default = $default;
	}
	
	public function count()
	{
		return count($this->array);
	}
	
	public function current() {
  	return current($this->array);
	}
	
  public function getIterator() {
		return $this;
  }
  
  public function key() {
  	return key($this->array);
  }
  
  public function offsetExists($offset) {
  	return isset($this->array[$offset]);
  }
  
  public function offsetGet($offset, $default = null) {
  	if (is_null($default)) $default = $this->default;
    return ($this->offsetExists($offset)) ? $this->array[$offset] : $default;
  }
  
 	public function offsetSet($offset, $value) {
  	$this->array[$offset] = $value;
  }
  
  public function offsetUnset($offset) {
  	unset($this->array[$offset]);
  }
  
  public function next() {
		$this->valid = (next($this->array) !== false);
	}
	
  public function rewind() {
  	$this->valid = (reset($this->array) !== false);
	}
	
	public function valid() {
		return $this->valid;
 	}
}

class Enumerable extends Enumerator
{
	public function all($block)
	{
		foreach($this as $key=>$value) if(!$block(&$value,&$key)) return false;
		return true;
	}
	
	public function any($block)
	{
		foreach($this as $key=>$value) if($block(&$value,&$key)) return true;
		return false;
	}
	
	public function clear()
	{
		$this->array = array();
		return $this;
	}
	
	public function collect($block)
	{
		$result = new Arr;
		foreach($this as $key=>$value) $result[] = $block(&$value,&$key);
		return $result;
	}
	
	public function detect($block)
	{
		foreach($this as $key=>$value) if($block(&$value,&$key)) return $value;
		return null;
	}
	
	public function filter($block)
	{
		
	}
	
	public function has_key($key)
	{
		return $this->keys()->has_value($key);
	}
	
	public function has_value($value)
	{
		return in_array($value,$this->array);
	}
	
	public function index($object)
	{
		foreach($this as $key=>$value) if($value == $object) return $key;
		return null;
	}
	
	public function inject($object, $block)
	{
		foreach($this as $key=>$value) $object = $block(&$value,&$key,&$object);
		return $object;
	}
	
	public function keys()
	{
		return array_keys($this->array);
	}
	
	public function none($block)
	{
		foreach($this as $key=>$value) if($block(&$value,&$key)) return false;
		return true;
	}
	
	public function partition($block)
	{
		
	}
	
	public function reject($block)
	{
		
	}
	
	public function rindex($object)
	{
		
	}
	
	public function replace($array)
	{
		
	}
	
	public function select($block)
	{
		
	}
	
	public function shift()
	{
		
	}
	
	public function sort($sort_flags=null)
	{
		
	}
	
	public function sort_by($block, $sort_flags=null)
	{
		
	}
	
	public function to_native_a()
	{
		
	}
	
	public function values()
	{
		
	}
	
	public function values_at($keys)
	{
		
	}
}

Enumerable::alias_method("at","offsetGet");
Enumerable::alias_method("fetch","offsetGet");
Enumerable::alias_method("length","count");
Enumerable::alias_method("map","collect");
Enumerable::alias_method("size","count");
Enumerable::alias_method("store","offsetSet");