<?php
namespace Phuby;

class Object extends Module
{	
	public $class;
	public $derived;
	public $reflection;
	public $instance;
	public $superclass;
	
	public function __construct($arguments = array())
	{
		$this->class = get_class($this);
		$this->derived = \Phuby\Module::derived($this->class);
		//$this->superclass = array_pop(class_parents($this->class));
		if(!empty($this->derived))
		{
			$this->reflection = new \ReflectionClass($this->derived);
			$this->instance = $this->reflection->newInstance();
			if($this->class != $this->derived)
			{
				$this->instance->class = get_class($this);
				$this->instance->reflection = $this->reflection;
				$this->instance->instance = $this->instance;
			}
		}
		if($this->respond_to("initialize"))
			$this->send_array("initialize",func_get_args());
	}
	
	public function __destruct()
	{
		if($this->respond_to("finalize")) $this->send_array("finalize");
	}
	
	public function dup()
	{
		return clone $this;
	}
	
	public function inspect()
	{
		ob_start();
		print_r($this);
		return ob_get_clean();
	}
	
	public function is_a($class)
	{
		return $this instanceof $class;
	}
	
	public function method_missing($method,$arguments=array())
	{
		return $this->send_array($method,$arguments);
	}
	
	public function respond_to($method)
	{
		$class = $this->class;
		if(method_exists($this,$method)) return true;
		if(array_key_exists($method, $class::aliases())) return true;
		return $this->reflection && in_array($method,array_map(function($m) { return $m->getName(); },$this->reflection->getMethods()));
	}
	
	public function send($method, $arguments=null)
	{
		$arguments = func_get_args();
		return $this->send_array(array_shift($arguments),$arguments);
	}
	
	public function send_array($method,$args=array())
	{
		if(!$this->respond_to($method)) return null;
		$class = $this->class;
		$aliases = $class::aliases();
		if(array_key_exists($method, $aliases)) $method = $aliases[$method];
		if(method_exists($this,$method)) return call_user_func_array(array($this,$method),$args);
		if(in_array($method,array_map(function($m) { return $m->getName(); },$this->reflection->getMethods())))
			return $this->reflection->getMethod($method)->invokeArgs($this->instance,$args);
		return null;
	}
	
	public function super($arguments=null)
	{
		$arguments = func_get_args();
		$caller = array_pop(array_slice(debug_backtrace(),1,1));
		$method = $caller['function'];
		$class = $this->class;
		$mixins = $class::mixins();
		foreach(array_reverse($mixins['ancestors']) as $ancestor)
		{
			$class_clean = str_replace("\\","_",$ancestor);
			if($this->respond_to("__super_{$class_clean}_{$method}"))
				return $this->send_array("__super_{$class_clean}_{$method}",$arguments);
		}
		return null;
	}
	
	public static function call($prop)
	{
		$result = null;
		$class = get_called_class();
		$mixin = $class::mixins();
		$derived = $mixin['derived'];
		if($derived && isset($derived::$$prop))
			$result =  &$derived::$$prop;
		return $result;
	}
	
	public function __call($method,$args)
	{
		return $this->send_array($method,$args);
	}
	
	public static function __callStatic($method,$args)
	{
		$class = get_called_class();
		$mixin = $class::mixins();
		if($mixin['derived'] && method_exists($mixin['derived'],$method))
			return call_user_func_array(array($mixin['derived'],$method),$args);
		return null;
	}
	
	public function __get($property)
	{
		if(isset($this->$property)) return $this->instance->$property;
		return null;
	}
	
	public function __isset($property)
	{
		return isset($this->instance->$property);
	}
	
	public function __set($property,$value)
	{
		if(isset($this->$property))
			return $this->instance->$property = $value;
		return null;
	}
	
	public function __unset($property)
	{
		unset($this->instance->$property);
	}
}
Object::extend("Phuby\Delegator");
Object::alias_method("is_an","is_a");