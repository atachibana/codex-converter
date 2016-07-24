<?php
/**
 * Triggerred interface from Codex to HelpHub
 *
 * Required parameters are
 *  request         ... Input data
 *  converter_type  ... Target Converter. Value is the same with Codex::TO_XXX.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */
require_once( 'class-codex.php' );

header( 'Content-type: text/plain; charset=UTF-8' );

if ( isset( $_POST['codex'] ) && isset( $_POST['converter_type'] ) ) {
	try {
        $codex_to = new Codex( $_POST['converter_type'] );
        $new_data = $codex_to->convert( $_POST['codex'] );
        echo $new_data;
	} catch ( Exception $e ) {
		$error_message = 'ERROR: ' . $e->getMessage() . ', TRACE: ' . $e->getTraceAsString();
		echo $error_message;
	}
}
else {
    echo 'ERROR: The parameter of "request" or "converter_type" is not found.';
}
?>
