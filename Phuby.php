<?php
require_once 'lib/ClassLoader.php';
$loader = new ClassLoader(array('lib'));
$loader->register();

function a() { return new \Phuby\Arr(func_get_args()); }
//function proc($block) { return new \Phuby\Proc($block); }