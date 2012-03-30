<?php
if(!class_exists("SplClassLoader")) require "vendor/SplClassLoader.php";
$loader = new SplClassLoader('Phuby', __DIR__.'/lib');
$loader->register();

function a() { return new \Phuby\Arr(func_get_args()); }
//function proc($block) { return new \Phuby\Proc($block); }