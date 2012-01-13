<?php
require_once dirname(dirname(__FILE__)).'/lib/ClassLoader.php';
$loader = new ClassLoader(array(dirname(dirname(__FILE__)).'/lib'));
$loader->register();

require_once 'simpletest/autorun.php';
require_once dirname(__FILE__).'/simplespec.php';
?>