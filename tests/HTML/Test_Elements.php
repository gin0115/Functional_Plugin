<?php

declare(strict_types=1);

/**
 * Tests for the Array functions class.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 */

use PHPUnit\Framework\TestCase;
use PinkCrab\FunctionConstructors\{
    Comparisons as Com,
    Numbers as Num,
    Arrays as Arr,
    Strings as Str,
    FunctionsLoader,
    Iterables as Itr,
    GeneralFunctions as Func
};
use Symfony\Component\DomCrawler\Crawler;
use Gin0115\Functional_Plugin\HTML\Elements as E;

/**
 * IterableFunction class.
 */
class Test_Elements extends TestCase {
    
    /** @testdox Can render a div with any defined attributes. */
    public function test_div_attributes(): void
    {
        $html = E\div(['id' => 'the_id', 'class' => 'class-1 class-2', 'foo' => 'bar'])(' ');
        $crawler = (new Crawler($html))->filter('#the_id')->first();
        $this->assertEquals('the_id', $crawler->attr('id'));
        $this->assertEquals('class-1 class-2', $crawler->attr('class'));
        $this->assertEquals('bar', $crawler->attr('foo'));
    }

    /** @testdox Can render a div with multiple contents. */
    public function test_div_contents(): void
    {
        $single = E\div(['id' => 'single'])('<p>Single</p>');
        $single_children = (new Crawler($single))
            ->filter('#single')
            ->each(fn (Crawler $node, $i)=> $node->text());
        
        $this->assertCount(1, $single_children);
        $this->assertContains('Single', $single_children);
        
        $triple = E\div(['id' => 'triple'])('<p>One</p>', '<p>Two</p>', '<p>Three</p>');
        $triple_children = (new Crawler($triple))
            ->filter('#triple')
            ->children()
            ->each(fn (Crawler $node, $i)=> $node->text());
        
        $this->assertCount(3, $triple_children);
        $this->assertContains('One', $triple_children);


    }
}