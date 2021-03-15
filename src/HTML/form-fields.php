<?php

/**
 * Handles the admin aspects of the quotes functionality.
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\HTML\Form;

use function PinkCrab\FunctionConstructors\Arrays\{toString};
use function Gin0115\Functional_Plugin\Libs\Utils\{array_map_with};
use function Gin0115\Functional_Plugin\HTML\Elements\{_render_attributes};


/**
 * Renders a select
 *
 * @param string $name The Name and ID of the select
 * @param array<string, string> $options The options for the select [key => value]
 * @param array<string, string> $attributes The attributes the select.
 * @return callable(mixed): string
 */
function select( string $name, array $options, array $attributes = array() ): callable {
	return function( $current ) use ( $name, $options, $attributes ) {
		$current = is_array( $current ) ? $current : array( $current );
		return sprintf(
			'<select name="%s" id="%s" %s>%s</select>',
			$name,
			$name,
			_render_attributes($attributes),
			selectOptions( $options, $current )
		);
	};
}

/**
 * 	Generates the select options based on the passed options and current selected.
 * 
 * @param array<string, string> $options
 * @param array<string, string> $current
 * @return string
 */
function selectOptions( array $options, array $current): string {
	return toString(\PHP_EOL)
		(array_map_with(
			function($key, $value, $current){
				return sprintf(
					'<option value="%s"%s>%s</option>',
					$key,
					in_array( $key, $current ) ? 'CHECKED' : '',
					$value
				);
			}, $options, $current
		));
}

/**
 * Returns a callable for creating a simple input.
 *
 * @param string $type The input type (text, number, email etc)
 * @param string $name used for ID and Name
 * @param array<string, string> $attributes
 * @return callable(mixed): string
 */
function input( string $type, string $name, array $attributes = array() ): callable {
	return function( $current ) use ( $type, $name, $attributes ) {
		return \sprintf(
			'<input type="%s" id="%s" name="%s" %s value="%s">',
			$type,
			$name,
			$name,
			join( $attributes ),
			$current
		);
	};
}

/**	
 * Parses a label
 * 
 * @param string $name
 * @return callable(string): string
 */
function label(string $name): callable
{
	return function(string $label) use ($name){
		return \sprintf("<label for=\"%s\">%s</label>", $name, $label);
	};
}
