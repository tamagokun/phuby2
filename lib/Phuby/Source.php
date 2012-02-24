<?php
namespace Phuby;

class Source
{
	protected $classes,$source,$name,$existing,$use;
	
	public function __construct($classes)
	{
		$classes = (is_array($classes))? $classes : func_get_args();
		$this->classes = $classes;
		$this->existing = array("constants"=>array(),"methods"=>array(),"properties"=>array());
		$this->use = array();
	}
	
	public function clean()
	{
		$this->existing = array("constants"=>array(),"methods"=>array(),"properties"=>array());
		$this->source = null;
		$this->name = null;
		$this->use = array();
	}
	
	public function compile()
	{
		$raw = "<?php {$this->namespace_aliases()} class {$this->name()} { {$this->source()} } ?>";
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
			$this->source .= $this->constant_source($ref);
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
	
	public function namespace_aliases()
	{
		return implode("\n",array_unique($this->use));
	}
	
	public function source()
	{
		return (!empty($this->source))? $this->source : $this->generate();
	}
	
	protected function constant_source(\ReflectionClass $ref)
	{
		$result = array();
		foreach($ref->getConstants() as $constant=>$value)
		{
			if(in_array($constant,$this->existing["constants"])) continue;
			$this->existing["constants"][] = $constant;
			$result[] = "const $constant = {$this->string_prepare($value)};";
		}
		return implode("\n",$result);
	}
	
	protected function property_source(\ReflectionClass $ref)
	{
		$result = array();
		foreach($ref->getDefaultProperties() as $prop=>$value)
		{
			if(in_array($prop,$this->existing["properties"])) continue;
			$this->existing["properties"][] = $prop;
			$default = $ref->getProperty($prop);
			$output = \Reflection::getModifierNames($default->getModifiers());
			$output[] = "$$prop";
			if($value) $output[] = "={$this->string_prepare($value)}";
			$output[] = ";";
			$result[] = implode(" ",$result);
		}
		return implode("\n",$result);
	}
	
	protected function method_source(\ReflectionClass $ref)
	{
		$result = null;
		$source = null;
		$filename = null;
		foreach($ref->getMethods() as $method)
		{
			$name = $this->method_name_filter($method);
			if(in_array($name,$this->existing["methods"])) continue;
			if(strpos($method->getDeclaringClass()->getName(),"Module") !== false) continue;
			if(strpos($method->getName(),"__") !== false) continue;
			$this->existing["methods"][] = $name;
			if($filename !== $method->getFileName())
			{
				$filename = $method->getFileName();
				$source = file($filename);
				$this->parse_use_declarations(implode("",$source));
			}
			$start = $method->getStartLine()-1;
			$length = $method->getEndLine() - $start;
			$source[$start] = str_replace($method->getName(),$name,$source[$start]);
			$result .= implode("", array_slice($source, $start, $length));
		}
		return $result;
	}
	
	protected function method_name_filter(\ReflectionMethod $method)
	{
		$name = $method->getName();
		$declared_clean = str_replace("\\","_",$method->getDeclaringClass()->getName());
		if(in_array($name,$this->existing["methods"])) $name = "__super_{$declared_clean}_{$name}";
		return $name;
	}
	
	protected function parse_use_declarations($source)
	{
		$matches = array();
		preg_match_all('/^\s*(use .*;)$/',$source,$matches);
		foreach($matches as $match) $this->use[] = array_shift($match);
	}
	
	protected function string_prepare($value)
	{
		return (is_string($value))? "\"$value\"" : $value;
	}
}