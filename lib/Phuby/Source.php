<?php
namespace Phuby;

class Source
{
	protected $classes,$source,$name,$existing;
	
	public function __construct($classes)
	{
		$classes = (is_array($classes))? $classes : func_get_args();
		$this->classes = $classes;
		$this->existing = array();
	}
	
	public function clean()
	{
		$this->existing = array();
		$this->source = null;
		$this->name = null;
	}
	
	public function compile()
	{
		$raw = "<?php class {$this->name()} { {$this->source()} } ?>";
		if(ini_get('allow_url_include') > 0)
			return require 'data:text/plain,base64,'.base64_encode($raw);
		$tmp=array_search('uri', @array_flip(stream_get_meta_data($GLOBALS[mt_rand()]=tmpfile())));
		file_put_contents($tmp,$raw);
		return require $tmp;
	}
	
	public function generate()
	{
		foreach($this->classes as $class)
		{
			$ref = new \ReflectionClass($class);
			$this->source .= $this->property_source($ref);
			$this->source .= $this->method_source($ref);
		}
		return $this->source;
	}
	
	public function name()
	{
		if(!$this->name)
			$this->name = "__Derived_".implode("_",str_replace("\\","_",$this->classes))."_".uniqid();
		return $this->name;
	}
	
	public function source()
	{
		return (!empty($this->source))? $this->source : $this->generate();
	}
	
	protected function property_source(\ReflectionClass $ref)
	{
		$result = array();
		foreach($ref->getDefaultProperties() as $prop=>$value)
		{
			$default = $ref->getProperty($prop);
			$output = \Reflection::getModifierNames($default->getModifiers());
			$output[] = "$$prop";
			if($value) $output[] = "=$value";
			$output[] = ";";
			$result[] = implode(" ",$result);
		}
		return implode("\n",$result);
	}
	
	protected function method_source(\ReflectionClass $ref)
	{
		$result = null;
		foreach($ref->getMethods() as $method)
		{
			$name = $this->method_name_filter($method);
			if(in_array($name,$this->existing)) continue;
			if(strpos($method->getDeclaringClass()->getName(),"Module") !== false) continue;
			if(strpos($method->getName(),"__") !== false) continue;
			$this->existing[] = $name;
			$source = file($method->getFileName());
			$start = $method->getStartLine()-1;
			$length = $method->getEndLine() - $start;
			$result .= str_replace($method->getName(),$name,implode("", array_slice($source, $start, $length)));
		}
		return $result;
	}
	
	protected function method_name_filter(\ReflectionMethod $method)
	{
		$name = $method->getName();
		$declared_clean = str_replace("\\","_",$method->getDeclaringClass()->getName());
		if(in_array($name,$this->existing)) $name = "__super_{$declared_clean}_{$name}";
		return $name;
	}
}