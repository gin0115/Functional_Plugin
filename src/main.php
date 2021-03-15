<?php 

/**
 * Entry point for the application.
 * 
 * @author Glynn Quelch <glynn@pinkcrab.co.uk>
 */

 namespace Gin0115\Functional_Plugin;

function main(){

    // Register metaboxes.
    add_action('add_meta_boxes', __NAMESPACE__ . '\Admin\meta_box_view');

}