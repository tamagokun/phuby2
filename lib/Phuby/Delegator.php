<?php
namespace Phuby;

class Delegator extends Object
{
	public static function delegate($delegated_methods)
	{
		$delegated_methods = func_get_args();
		$receiver = array_pop($delegated_methods);
		if( empty($delegated_methods) ) return false;
		
		$class = get_called_class();
		$mixins = &$class::methods();
		foreach($delegated_methods as $delegated_method)
			$class::alias_method($delegated_method,"__super_{$receiver}_{$delegated_method}");
	}
}