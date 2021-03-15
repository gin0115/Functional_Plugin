<?php

/**
 * Handles the admin aspects of the quotes functionality.
 * 
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\HTML;

use function PinkCrab\FunctionConstructors\Arrays\{mapWithKey};

function select(string $name, array $options, array $attributes = []): callable {
    return function($current) use ($name){
        $current = is_array($current) ? $current : [$current];
        return sprintf('<select name="%s" id="%s %s">%s</select>',
            $name, $name, '111', selectOptions($option, $current));
    };
}

function selectOptions(array $options, array $current): array {
    return array_reduce(
        array_keys($options),
        function($carry, $optionValue) use ($current, $options): string{
            return sprintf
                ( '<option value="%s"%s>%s</option>'
                , $optionValue
                , in_array($options[$optionValue], $current) ? 'CHECKED' : ''
                , $options[$optionValue]
                );
        }, []
    );
}