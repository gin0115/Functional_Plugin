<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\HTML\Elements;

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
		return Str\tagWrap(
			'div ' . _render_attributes( $attributes ),
			'div'
		)( join(PHP_EOL, $contents) );
	};
}

/**
 * Renders <h2>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function h2( array $attributes = array() ): callable {
	return function( $contents ) use ( $attributes ): string {
		return Str\tagWrap(
			'h2 ' . _render_attributes( $attributes ),
			'h2'
		)( $contents );
	};
}

/**
 * Renders <p>
 * @param array<string, string> $attributes
 * @return callable(string): string
 */
function p( array $attributes = array() ): callable {
	return function( $contents ) use ( $attributes ): string {
		return Str\tagWrap(
			'p ' . _render_attributes( $attributes ),
			'p'
		)( $contents );
	};
}

/**
 * Renders out the string of attributes for an element.
 *
 * @param array<string, string> $attributes
 * @return string
 */
function _render_attributes( array $attributes ): string {
	return array_reduce(
		array_keys( $attributes ),
		function( $carry, $key ) use ( $attributes ) {
			$carry = $carry . \sprintf(
				'%s="%s" ',
				$key,
				$attributes[ $key ]
			);
			return $carry;
		},
		''
	);
}
