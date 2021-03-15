<?php

/**
 * General Utility functions
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Libs\Utils;

/**
 * Array map, which gives access to array key and a selection of static
 * values.
 *
 * @param callable(string|int, mixed, array<string, mixed>): mixed $function
 * @param array<string|int, mixed> $data
 * @param array<string, mixed> $with
 * @return array<int, mixed>
 */
function array_map_with( callable $function, array $data, array $with = [] ): array {
    $return = array();
    foreach ( $data as $key => $value ) {
        $return[] = $function( $key, $value, $with );
    }
    return $return;
}

/**
 * Returns a callable for doing a dump() of the current 
 * value in chain.
 * 
 * @template A
 * @return callable(A): A Returns the same value passed
 */
function dumper(): callable {
	return function($e){
        dump( $e );
	    return $e;
    };
}