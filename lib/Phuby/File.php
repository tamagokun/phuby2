<?php
namespace Phuby;

class File extends Object
{
	public static function absolute_path($file,$dir=null)
	{
		if(!is_null($dir))
		{
			$current = getcwd();
			chdir($dir);
		}
		$path = realpath($file);
		if(!is_null($dir)) chdir($current);
		return $path;
	}

	public static function atime($file)
	{
		return new DateTime(fileatime($file));
	}
	
	public static function basename($file,$suffix="")
	{
		return str_replace($suff,"",basename($file));
	}
	
	public static function chmod($mode,$file)
	{
		$result = 0;
		$files = func_get_args();
		$mode = array_shift($files);
		foreach($files as $file) if(chmod($file,$mode)) $result++;
		return $result;
	}
	
	public static function chown($owner,$group,$file)
	{
		$result = 0;
		$files = func_get_args();
		$owner = array_shift($files);
		$group = array_shift($files);
		foreach($files as $file) if(chown($file,$owner) && chgrp($file,$group)) $result++;
		return $result;
	}
	
	public static function ctime($file)
	{
		return new DateTime(filectime($file));
	}
	
	public static function delete($file)
	{
		$result = 0;
		$files = func_get_args();
		foreach($files as $file) if(unlink($file)) $result++;
		return $result;
	}
	
	public static function directory($file)
	{
		return is_dir($file);
	}
	
	public static function dirname($file)
	{
		return dirname($file);
	}
	
	public static function exist($file)
	{
		return file_exists($file);
	}
	
	public static function extname($file)
	{
		$info = pathinfo($file);
		return (isset($info["extension"]))? $info["extension"] : "";
	}
	
	public static function join($path)
	{
		return implode(DIRECTORY_SEPARATOR,func_get_args());
	}
	
	public static function rename($old,$new)
	{
		return rename($old,$new);
	}
	
	public static function size($file)
	{
		return filesize($file);
	}
	
	public static function split($file)
	{
		return array(File::dirname($file),File::basename($file));
	}
	
	public static function umask($int)
	{
		return umask($int);
	}
}
File::alias_method("unlink","delete");
File::alias_method("exists","exist");
File::alias_method("expand_path","absolute_path");