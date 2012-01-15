<?php
namespace Phuby;

class Object extends Module
{	
	public $class;
	public $instances;
	public $instance_variables;
	public $superclass;
	
	public function __construct($arguments = array())
	{
		$class = get_class($this);
		$this->class = $class;
		$this->instances = array();
		$this->instance_variables = $class::properties();
		$this->superclass = array_pop(class_parents($this->class));
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
	
	public function inject($object)
	{
		if($this->is_injected($object)) return true;
		$this->instances[get_class($object)] = $object;
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
		$methods = $class::methods();
		return in_array($method, get_class_methods($this->class)) || (in_array($method, array_keys($methods)) && !empty($methods[$method]));
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
		$methods = $class::methods();
		if(!isset($methods[$method]) || empty($methods[$method]))
			return call_user_func_array(array($this,$method), $args);
		
		$class = $methods[$method][0][0];
		return call_user_func_array(array($this->ensure_injected($class),$method),$args);
	}
	
	public function super($arguments=null)
	{
		$arguments = func_get_args();
		$caller = array_pop(array_slice(debug_backtrace(),1,1));
		$origin = array_pop(array_slice(debug_backtrace(),3,1));
		if(empty($caller) || empty($origin)) return false;
		
		$class = get_class($origin["object"]);
		$instance = $this->ensure_injected($class);
		if(!$instance) return false;
		
		$methods = &$class::methods();
		$aliases = $class::aliases();
		$method = $caller["function"];
		foreach(array_reverse($aliases) as $alias)
		{
			if($alias[1] == $method)
			{
				$method = $alias[0];
				break;
			}
		}
		if(isset($methods[$method]) && !empty($methods[$method]))
		{
			$callee = array_shift($methods[$method]);
			$result = $instance->send_array($method, $arguments);
			array_unshift($methods[$method], $callee);
		}else
		{
			$class = get_parent_class($instance);
			$result = call_user_func_array(array($class,$method),$arguments);
		}
		return $result;
	}
	
	public static function call($prop)
	{
		$result = null;
		foreach(self::$mixins as $name=>$class)
		{
			if(isset($name::$$prop))
			{
				$result = &$name::$$prop;
				break;
			}
		}
		return $result;
	}
	
	private function ensure_injected($class)
	{
		if(!isset($this->instances[$class]) || !$this->instances[$class])
			$this->instances[$class] = new $class();
		$this->instances[$class]->inject($this);
		return $this->instances[$class];
	}
	
	private function is_injected($object)
	{
		return array_key_exists(get_class($object),$this->instances);
	}
	
	public function __call($method,$args)
	{
		return $this->send_array($method,$args);
	}
	
	public static function __callStatic($method,$args)
	{
		$class = get_called_class();
		foreach($class::ancestors() as $class)
		{
			if(method_exists($class,$method)) return call_user_func_array(array($class,$method),$args);
		}
	}
	
	public function __get($property)
	{
		if(isset($this->$property)) return $this->instance_variables[$property];
		foreach($this->instances as $instance)
		{
			if(isset($instance->$property)) return $instance->$property;
		}
		return null;
	}
	
	public function __isset($property)
	{
		return isset($this->instance_variables[$property]);
	}
	
	public function __set($property,$value)
	{
		if(isset($this->$property))
			return $this->instance_variables[$property] = $value;
		foreach($this->instances as $instance)
		{
			if(isset($instance->$property)) $instance->$property = $value;
		}
	}
	
	public function __unset($property)
	{
		unset($this->instance_variables[$property]);
	}
}
Object::extend("Phuby\Delegator");
Object::alias_method("is_an","is_a");