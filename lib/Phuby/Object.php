<?php
namespace Phuby;

class Object extends Module
{	
	public $class;
	public $reflection;
	public $instance;
	public $superclass;
	
	public function __construct($arguments = array())
	{
		$this->class = Module::derived(get_class($this));
		//$this->superclass = array_pop(class_parents($this->class));
		$this->reflection = new \ReflectionClass($this->class);
		$this->instance = $this->reflection->newInstance($arguments);
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
		return in_array($method, get_class_methods($this->class));
	}
	
	public function send($method, $arguments=null)
	{
		$arguments = func_get_args();
		return $this->send_array(array_shift($arguments),$arguments);
	}
	
	public function send_array($method,$args=array())
	{
		if(!$this->respond_to($method)) return null;
		return $this->reflection->getMethod($method)->invokeArgs($this->instance,$args);
	}
	
	public function super($arguments=null)
	{
		$arguments = func_get_args();
		$caller = array_pop(array_slice(debug_backtrace(),1,1));
		$origin = array_pop(array_slice(debug_backtrace(),3,1));
		if(empty($caller) || empty($origin)) return false;
		if(!isset($origin["object"])) $origin = array_pop(array_slice(debug_backtrace(),4,1));
		if(!isset($origin["object"])) return false;
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
		$class = $this->class;
		if(isset($class::$$prop)) $result = &$class::$$prop;
		return $result;
	}
	
	public function __call($method,$args)
	{
		return $this->send_array($method,$args);
	}
	
	public static function __callStatic($method,$args)
	{
		if(method_exists($this->class,$method))
			return $this->reflection->getMethod($method)->invokeArgs(null,$args);
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