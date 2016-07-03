<?php
/**
 * Sample parts of functions.php for page template page-codextohelphub.php
 *
 * Apppend this snippet into you functions.php when you use the page template
 * page-codextohelphub.php. It loads required JavaScript and stylesheet.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */
function codex_converter_scripts() {
    if ( is_page_template( 'page-codextohelphub.php' ) ) {
        $dir = get_stylesheet_directory_uri();
        wp_enqueue_script( 'codex-converter-script', $dir.'/codex-converter.js', array('jquery'));
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'https://code.jquery.com/jquery-2.2.4.min.js' );
        wp_enqueue_style( 'codex-converter-css', $dir.'/codex-converter.css' );
    }
    if ( is_page_template( 'page-codextranslatoraid.php' ) ) {
        $dir = get_stylesheet_directory_uri();
        wp_enqueue_script( 'codex-converter-script', $dir.'/codex-translator-aid.js', array('jquery'));
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'https://code.jquery.com/jquery-2.2.4.min.js' );
        wp_enqueue_style( 'codex-converter-css', $dir.'/codex-converter.css' );
    }
}
add_action( 'wp_enqueue_scripts', 'codex_converter_scripts', 50 );
