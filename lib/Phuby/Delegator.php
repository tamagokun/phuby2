<?php
namespace Phuby;

class Delegator extends Object
{
	public static function delegate($delegated_methods)
	{
		/*$delegated_methods = func_get_args();
		$receiver = array_pop($delegated_methods);
		if( empty($delegated_methods) ) return false;
		
		$class = get_called_class();
		$methods = &$class::methods();
		foreach($delegated_methods as $delegated_method)
		{
			if(!isset($methods[$delegated_method])) $methods[$delegated_method] = array();
			array_unshift($methods[$delegated_method], array($receiver, $delegated_method));
		}*/
	}
}