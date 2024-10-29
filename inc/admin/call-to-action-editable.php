<?php

if ( ! defined( 'ABSPATH' ) ) {
    die ('Unauthorized access');
}

/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 */

add_action('admin_init', function () {
    // Skip block registration if Gutenberg is not enabled/merged.
    if (!function_exists('register_block_type')) {
        return;
    }
    $dir = dirname(__FILE__);

    $index_js = 'call-to-action-editable/index.js';
    
    wp_register_script('call-to-action-editable-block-editor', plugins_url($index_js, __FILE__), array('wp-blocks','wp-i18n','wp-element','wp-components'),filemtime("$dir/$index_js")
    );

    $editor_css = 'call-to-action-editable/editor.css';
    wp_register_style('call-to-action-editable-block-editor-style', plugins_url($editor_css, __FILE__), array(),filemtime("$dir/$editor_css")
    );

    $style_css = 'call-to-action-editable/style.css';
    
    wp_register_style('call-to-action-editable-block', plugins_url($style_css, __FILE__), array(), filemtime("$dir/$style_css"));

    register_block_type('advance-search/call-to-action-editable', array(
        'editor_script' => 'call-to-action-editable-block-editor',
        'editor_style' => 'call-to-action-editable-block-editor-style',
        'style' => 'call-to-action-editable-block',
    ));
});
