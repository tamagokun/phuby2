<?php
namespace Phuby;

class Source
{
	protected $classes,$source,$name;
	
	public function __construct($classes)
	{
		$classes = (is_array($classes))? $classes : func_get_args();
		$this->classes = $classes;
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
			$ref = new ReflectionClass($class);
			$this->source .= $this->property_source($ref);
			$this->source .= $this->method_source($ref);
		}
	}
	
	public function name()
	{
		if(!$this->name)
			$this->name = "__Derived_".implode("_",$this->classes)."_".uniqid();
		return $this->name;
	}
	
	public function source()
	{
		return ($this->source)? $this->source : $this->generate();
	}
	
	protected function property_source(ReflectionClass $ref)
	{
		$result = array();
		foreach($ref->getDefaultProperties() as $prop=>$value)
		{
			$default = $ref->getProperty($prop);
			$output = Reflection::getModifierNames($default->getModifiers());
			$output[] = "$$prop";
			if($value) $output[] = "=$value";
			$output[] = ";";
			$result[] = implode(" ",$result);
		}
		return implode("\n",$result);
	}
	
	protected function method_source(ReflectionClass $ref)
	{
		$result = null;
		foreach($ref->getMethods() as $method)
		{
			$source = file($method->getFileName());
			$start = $method->getStartLine()-1;
			$length = $method->getEndLine() - $start;
			$result .= implode("", array_slice($source, $start, $length));
		}
		return $result;
	}
}