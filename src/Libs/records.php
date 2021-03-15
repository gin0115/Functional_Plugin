<?php

/**
 * Base model
 *
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Libs\Records;

use Gin0115\Functional_Plugin\Fixtures\Base_Model;

/**
 * Allows the mapping of an item to a new model
 * 
 * @param Base_Model $record The record to clone and set existing values with.
 * @param array<string, mixed> $values he array of values to set.
 * @return Base_Model Returns new updated, instance.
 */
function model_with( Base_Model $record, array $values ): Base_Model {
	$record = clone $record;
	foreach ( $values as $key => $value ) {
		if ( \property_exists( $record, $key ) ) {
			$record->{$key} = $value;
		}
	}
	return $record;
}
