<?php

declare(strict_types=1);

/**
 * Test for all the custom utility functions.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use PinkCrab\FunctionConstructors\{
    Comparisons as C,
    Numbers as Num,
    Arrays as Arr,
    Strings as Str,
    GeneralFunctions as F
};
use Gin0115\Functional_Plugin\Libs\Utils as U;


class Test_Ultils extends TestCase {

    /** @testdox Can map over an array using key, value and an additional array. From a programatic usecase. */
    public function test_arrayMapWith(): void {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $with = ['a', 'b', 'c'];
        $expected = ['key1abcvalue1', 'key2abcvalue2'];

        $abcified = U\arrayMapWith(
            fn($key, $value, $with): string =>  sprintf("%s%s%s", $key, join($with), $value), 
            $data, $with
        );

        $this->assertSame($expected[0], $abcified[0]);
        $this->assertSame($expected[1], $abcified[1]);
    }

    /** @testdox Can create a functioon to check if a value is a string and uppercase it or return false if not a string. */
    public function test_ifThen(): void
    {
        $ifStringUppercaseIt = U\ifThen('is_string', 'strtoupper', false);
        
        $this->assertEquals('HELLO', $ifStringUppercaseIt('heLLo'));
        $this->assertEquals(false, $ifStringUppercaseIt(1));
        $this->assertEquals(false, $ifStringUppercaseIt([1]));
    }

    /** @testdox Can either double a float or interger or return some emojis based on the value passed */
    public function test_ifElse():void {
        $ifNumberDoubleItOrArrayOfEmojis = U\ifElse(
            C\any('is_int', 'is_float'),
            fn($e) => $e * 2,
            F\always('😜😃😈')
        );
        
        $this->assertEquals(12, $ifNumberDoubleItOrArrayOfEmojis(6));
        $this->assertEquals(24.4, $ifNumberDoubleItOrArrayOfEmojis(12.2));
        $this->assertEquals('😜😃😈', $ifNumberDoubleItOrArrayOfEmojis(['array']));
    }

    /** @testdox Can use a partialially applied function for just returning the value it was passed. Allows for fallback methods.. */
    public function test_passThrough(): void
    {
        $passThrough = U\passThrough();

        $this->assertEquals('1', $passThrough('1'));
        $this->assertEquals(12.5, $passThrough(12.5));
        $this->assertEquals(04567, $passThrough(04567));
        $this->assertEquals([1,2,3,4,5], $passThrough([1,2,3,4,5]));
    }

    /** @testdox Can create a function for setting a key/propery in any array or object (public properties) */
    public function test_setPropertyWith(): void
    {
        $fooBarArray = U\setPropertyWith([],'foo')('bar');
        $this->assertArrayHasKey('foo', $fooBarArray);
        $this->assertContains('bar', $fooBarArray);

        $fooBarClass = U\setPropertyWith(new class(){public $foo;},'foo')('bar');
        $this->assertEquals('bar', $fooBarClass->foo);

        $fooBarAObjectArrAccess = U\setPropertyWith(
            new ArrayObject([], \ArrayObject::ARRAY_AS_PROPS) ,'foo'
        )('bar');
        $this->assertEquals('bar', $fooBarAObjectArrAccess->foo);
        
        $fooBarAObjectParamAccess = U\setPropertyWith(
            new ArrayObject([], \ArrayObject::STD_PROP_LIST) ,'foo'
        )('bar');
        $this->assertEquals('bar', $fooBarAObjectParamAccess['foo']);
    }

    /** @testdox Can clone an object and set a property. */
    public function test_cloneWith(): void
    {
        $baseClass = new stdClass;
        $clonedClass = U\cloneWith($baseClass,'foo')('bar');
        $this->assertNotSame(spl_object_id($baseClass), spl_object_id($clonedClass));
        $this->assertEquals('bar', $clonedClass->foo);
    }

    /** @testdox Can match a value based on multiple criteria and pass through a function.  */
    public function test_match(): void
    {
        $run = U\match(
            U\anyThen(
                'mb_strtoupper', 
                fn($e) => in_array($e, ['a','b','c']), 
                fn($e) => in_array($e, ['g','h','i'])
            ),
            U\allThen('mb_strtolower', 'is_string'),
            U\allThen(U\passThrough(), F\always(true))
        );
        $this->assertEquals('A', $run('a'));
        $this->assertEquals('H', $run('h'));
        $this->assertEquals('e', $run('e'));
        $this->assertEquals('C', $run('c'));
        $this->assertEquals('z', $run('Z'));
        $this->assertEquals(2, $run(2));
    }
}