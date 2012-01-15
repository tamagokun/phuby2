<?php
require 'spec_helper.php';

use \Phuby\Arr;

class Describe_Enumerable_Array extends SimpleSpec {
    
    function should_instantiate_a_array_object() {
        $e = new Arr(array('ing', 'cool', 'wow'));
        expect( $e )->should_be_a('Phuby\Arr');
        expect( $e )->should_be_a('Phuby\Enumerable');
        expect( $e )->should_be_a('Phuby\Object');
        
        expect($e->array)->should_be(array('ing', 'cool', 'wow'));
    }
    
    function should_be_iteratable_with_foreach() {
        $e = \Phuby\a('ing', 'cool', 'wow');
        $results = array();
        foreach ($e as $k => $v) {
            $results[] = $v;
        }
        expect($results)->should_be(array('ing', 'cool', 'wow'));
    }
    
    function should_provide_functional_iterators() {
        $e = \Phuby\a('ing', 'cool', 'wow');
        
        expect($e->any(function($v) { return $v == "ing"; }))->should_be(true);
        expect($e->any(function($v) { return $v == "invalid"; }))->should_be(false);
        
        expect($e->map(function($v) { return strlen($v); })->array)->should_be(array(3,4,3));
        
        $rs = $e->inject('', function($v,$k,$o) { return $o .= $v;});
        expect($rs)->should_be('ingcoolwow');

    }
    
    function should_provide_enumeration_methods() {
        //FIXME: uses asort which maintain index association, make sense here?
        # Enumerable#sort 
        expect(array_values(\Phuby\a(3,2,1)->sort()->array))->should_be(array(1,2,3));
        
        $e = \Phuby\a(1, 2, 3, \Phuby\a(4, 5, 6, \Phuby\a(7, 8, 9)));
        
        expect($e->flatten()->array)->should_be(array(1,2,3,4,5,6,7,8,9));
    }
}

