<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Libs\HTML\Elements;

use PinkCrab\FunctionConstructors\{
	Strings as Str
};

/**
 * Renders a div with defined attributes and contents.
 *
 * @param array<string, string> $attributes
 * @return callable(...mixed): string
 */
function div( array $attributes = [] ): callable {
	return function( ...$contents ) use ( $attributes ): string {
		return _element('div', $attributes, $contents);
	};
}

/**
 * Renders <h2>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function h2( array $attributes = array() ): callable {
	return function( ...$contents ) use ( $attributes ): string {
		return _element('h2', $attributes, $contents);
	};
}

/**
 * Renders <p>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function p( array $attributes = array() ): callable {
	return function( ...$contents ) use ( $attributes ): string {
		return _element('p', $attributes, $contents);
	};
}

/**
 * Renders <span>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function span( array $attributes = array() ): callable {
	return function( ...$contents ) use ( $attributes ): string {
		return _element('span', $attributes, $contents);
	};
}

/**
 * Renders <img>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function img( array $attributes = array() ): callable {
	return function( ...$contents ) use ( $attributes ): string {
		return _element('img', $attributes, [], false);
	};
}

/**	
 * Renders an HTML element, either with or without closing tags.
 * 
 * @param string $type The HTML Element to create.
 * @param array<string, string|null> $attributes The attributes for the element.
 * @param array<string> $contents The contents of the element. Not used for single tags
 * @param bool $has_closing_tag Denotes if its single tag, or has a closing tag.
 * @return string The compiled html.
 */
function _element(string $type, array $attributes, array $contents = [], bool $has_closing_tag = true): string
{
	return $has_closing_tag 
		? Str\tagWrap("{$type} " . _render_attributes( $attributes ),"{$type}")( join(PHP_EOL , $contents) )
		: \sprintf("<%s %s>", $type, _render_attributes( $attributes ));
}

/**
 * Renders out the string of attributes for an element.
 *
 * @param array<string, string|null> $attributes
 * @return string
 */
function _render_attributes( array $attributes ): string {
	return array_reduce(
		array_keys( $attributes ),
		function( $carry, $key ) use ( $attributes ) {
			// Selects the template, based on the attribute value.
			// If empty string or null, will not include the assigment.
			$template = ! is_null($attributes[ $key ]) && strlen($attributes[ $key ]) >= 1
				? ' %s="%s"' : ' %s';

			$carry = $carry . \sprintf($template, $key, $attributes[ $key ]);
			return $carry;
		},
		''
	);
}
