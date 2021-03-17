<?php

/**
 * General Utility functions
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Libs\Utils;

use ArrayAccess;
use InvalidArgumentException;
use PinkCrab\FunctionConstructors\Comparisons as C;
use PinkCrab\FunctionConstructors\GeneralFunctions as F;

/**
 * Array map, which gives access to array key and a selection of static
 * values.
 *
 * @param callable(string|int, mixed, array<string,mixed>):mixed $function
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
function passThrough(): callable
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
 * @param bool $use_dump Will use dump() over var_dump() if true (default) and is included.
 * @return callable(A): A Returns the same value passed
 */
function dumper(bool $useDump = true): callable {
	return function($e) use ($useDump){
        if(\function_exists('dump') && $useDump === true){
            dump( $e );
        } else {
            var_dump( $e );
        }        
	    return $e;
    };
}

/** 
 * Returns a callable for the setting of an array/objects property/key
 * 
 * @template A
 * @param array<string, mixed>|object|ArrayObject|ArrayAccess|A $record
 * @param string $key
 * @return callable(mixed):array<string, mixed>|object|ArrayObject|ArrayAccess|A
 */
function setPropertyWith($record, string $key): callable
{
    return function($value) use ($record, $key){
        return F\setProperty($record)($key, $value);
    };
}

/**
 * Clones a record with a setter returned for a defined propery
 *
 * @param array<string, mixed>|object $record
 * @param string $property
 * @return callable(mixed):array<string, mixed>|object
 */
function cloneWith($record, string $property): callable
{
    if(!is_array($record) && !is_object($record)){
        throw new InvalidArgumentException("Only arrays and objects can be cloned from.");
    }

    //If an object, clone.
    if(is_object($record)){
        $record = clone $record;
    }
    return function($value) use ($record, $property){
        if(is_array($record) || is_subclass_of($record, ArrayAccess::class) ){
            $record[$property] = $value;
        }elseif(\property_exists($record, $property)){
            $record->{$property} = $value;
        }
        return $record;

    };
}

function cloneWithMany($record): callable
{
    return function( array $properties) use ($record) {
        foreach ($properties as $property => $value) {
            $record = cloneWith($record, $property)($value);
        }
        return $record;
    };
}

/**
 * Matches a value against any number of expressions
 * and passes the value through a function on success.
 *
 * @template T
 * @param callable(T):bool ...$matchExpressions List of match expressions.
 * @return callable(T):mixed|null Returns callable to pass value from all expressions.
 */
function match(callable ...$matchExpressions): callable
{
    return function($value) use ($matchExpressions) {
        if(count($matchExpressions) === 0){
            return null;
        }
        $current = array_shift($matchExpressions);
        $result = $current($value);
        return ! is_null($result)
            ? $result : match(...$matchExpressions)($value);
    };
}

function anyThen(callable $expression, callable ...$conditionals): callable
{
    return function($value) use ($expression, $conditionals){
        return ifElse
            ( C\any(...$conditionals)
            , $expression
            , F\always(null)
            )($value);
    };
}

function allThen(callable $expression, callable ...$conditionals): callable
{
    return function($value) use ($expression, $conditionals){
        return ifElse
            ( C\all(...$conditionals)
            , $expression
            , F\always(null)
            )($value);
    };
}
