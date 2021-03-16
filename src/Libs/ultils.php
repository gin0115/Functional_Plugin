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
function arrayMapWith( callable $function, array $data, array $with = [] ): array {
    $return = array();
    foreach ( $data as $key => $value ) {
        $return[] = $function( $key, $value, $with );
    }
    return $return;
}

/** 
 * Allows for a partially applied if/else statement.
 * The passed data is run through the condition. 
 * If that returns true, the result of passing the data to expression is returned.
 * If that returns false, will return the $else data as is.
 * 
 * @param callable(mixed): bool $conditional
 * @param callabe(mixed): mixed $expression
 * @param mixed $else The fallback value.
 * @return callable(mixed): mixed
 */
function ifThen(callable $conditional, callable $expression, $else): callable
{
    return function($data) use ($conditional, $expression, $else){
        return $conditional($data) ?  $expression($data) : $else;
    };
}

/**
 * Allows for a partially applied if/else statement.
 * Passed data is run through the conditional and then based on its result
 * the retrun from either $true_expression or $false_expression.
 * 
 * @param callable(mixed):bool $conditional
 * @param callabe(mixed): mixed $true_expression
 * @param callabe(mixed): mixed $false_expression
 * @return callable(mixed): mixed
 */
function ifElse(callable $conditional, callable $true_expression, callable $false_expression): callable
{
    return function($data) use ($conditional, $true_expression, $false_expression){
        return $conditional($data) ?  $true_expression($data) : $false_expression($data);
    };
}

/** 
 * Returns a parial function, which just reutrns as its passed.
 * 
 * @Template A
 * @return callable(A): A
 */
function nothing(): callable
{
    return function($e){
        return $e;
    };
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