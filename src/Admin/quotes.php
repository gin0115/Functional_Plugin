<?php

/**
 * Handles the admin aspects of the quotes functionality.
 * 
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

namespace Gin0115\Functional_Plugin\Admin;

use function PinkCrab\FunctionConstructors\GeneralFunctions\{compose, pipe, getProperty, always};
use function PinkCrab\FunctionConstructors\Arrays\{map, zip, mapKey};
use function PinkCrab\FunctionConstructors\Strings\{tagWrap};
use function Gin0115\Functional_Plugin\HTML\{select};

const QUOTE_META_KEYS = ['gin0115_fp_show_quote', 'gin0115_fp_postition'];

// update_p

function register_meta_box()
{
    add_meta_box('gin0115_fp_page_meta_box', 'Quotes', '\Gin0115\Functional_Plugin\Admin\meta_box_view', ['post']);
}

function updateProperty(string $property, callable $value): callable
{
    return function (array $array) use ($property, $value) {
        $array[$property] = $value($array);
        return $array;
    };
}

function dumper($e)
{
    dump($e);
    return $e;
}

function meta_box_view($post)
{
    print render_meta_box(
        array_map(function (string $key) {
            return get_post_meta(get_the_ID(), $key, true);
        }, QUOTE_META_KEYS)
    );
}

function render_meta_box(array $meta)
{
    dump($meta);
    return compose(
        zip(QUOTE_META_KEYS) // Create a typle with they key name
        ,
        map(mapKey(fn ($key) => $key === 1 ? 'meta_key' : 'meta_value')) // Map with named keys, over indexes
        ,
        map(updateProperty('htmlHeader', pipe(
            getProperty('meta_key'),
            tagWrap('h3 class="field-header"', 'h3')
        ))),
        map(updateProperty('htmlField', pipe(
            function ($field) {
                switch ($field['meta_key']) {
                    case 'gin0115_fp_show_quote':
                        return select($field['meta_key'], ['yes' => 'Yes', 'no' => 'No'], [$field['meta_value']]);

                    default:
                        return '';
                }
            }
        )($field))),
        __NAMESPACE__ . '\dumper'
    )($meta);
}
