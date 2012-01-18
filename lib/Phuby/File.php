<?php
namespace Phuby;

class File extends Object
{
	public static function absolute_path($file,$dir=null)
	{
	
	}

	public static function atime($file)
	{
	
	}
	
	public static function basename($file)
	{
		
	}
	
	public static function chmod($mode,$file)
	{
		$args = func_get_args();
		$mode = array_shift($args);
	}
	
	public static function chown($owner,$group,$file)
	{
		
	}
	
	public static function ctime($file)
	{
		
	}
	
	public static function delete($file)
	{
		
	}
	
	public static function directory($file)
	{
		
	}
	
	public static function dirname($file)
	{
		
	}
	
	public static function exist($file)
	{
		
	}
	
	public static function extname($file)
	{
		
	}
	
	public static function join($path)
	{
		$paths = func_get_args();
	}
	
	public static function rename($old,$new)
	{
		
	}
	
	public static function size($file)
	{
		
	}
	
	public static function split($file)
	{
		
	}
	
	public static function umask($int)
	{
		
	}
}
File::alias_method("unlink","delete");
File::alias_method("exists","exist");
File::alias_method("expand_path","absolute_path");