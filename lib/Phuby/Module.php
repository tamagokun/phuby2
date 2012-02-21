<?php
namespace Phuby;

abstract class Module
{
	public static $mixins = array();
	
	public static function alias($new,$old)
	{
		$class = get_called_class();
		$mixins = &$class::mixins();
		$mixins['aliases'][$new] = $old;
		//array($class,$old);
	}
	
	public static function alias_method($new,$old)
	{
		$class = get_called_class();
		$class::alias($new,$old);
	}
	
	public static function alias_method_chain($method,$with)
	{
		$class = get_called_class();
		$class::alias_method("{$method}_without_{$with}",$method);
		$class::alias_method($method, "{$method}_with_{$with}");
	}
	
	public static function aliases()
	{
		$class = get_called_class();
		$mixins = $class::mixins();
		return $mixins["aliases"];
	}
	
	public static function extend($modules)
	{
		$modules = (is_array($modules))? $modules : func_get_args();
		$class = get_called_class();
		$mixins = &$class::mixins();
		foreach($modules as $module)
		{
			if(!in_array($module, $mixins['ancestors']))
			{
				array_unshift($mixins['ancestors'],$module);
			}
		}
		$class::update_derived_modules();
	}
	
	public static function derived($class)
	{
		return (isset(Module::$mixins[$class]))? Module::$mixins[$class]['derived'] : null;
	}
	
	public static function &mixins()
	{
		$class = get_called_class();
		if(isset(Module::$mixins[$class])) return Module::$mixins[$class];
		$mixin = array('ancestors'=>array(),'aliases'=>array(),'derived'=>'');
		Module::$mixins[$class] = $mixin;
		return Module::$mixins[$class];
	}
	
	public static function new_instance($arguments = null)
	{
		$class = get_called_class();
		return $class::new_instance_array(func_get_args());
	}
	
	public static function new_instance_array($arguments = array())
	{
		$reflection = new \ReflectionClass(get_called_class());
		return $reflection->newInstanceArgs($arguments);
	}
	
	public static function update_derived_modules()
	{
		foreach(Module::$mixins as $class=>&$mixin)
		{
			$classes = $mixin['ancestors'];
			$classes[] = $class;
			$source = new Source($classes);
			$source->compile();
			$mixin['derived'] = $source->name();
		}
	}
}