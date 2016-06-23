<?php
/*****************************************************************************
 * Web interface of CodexToHelpHub
 *
 * by Akira Tachibana
 ****************************************************************************/
require_once( 'class-codex-converter.php' );

header( "Content-type: text/plain; charset=UTF-8" );

if ( isset( $_POST['request'] ) ) {
    $data = $_POST['request'];
    $cr = array( "\r\n", "\r" );
    $data = str_replace( $cr, "\n", $data );
    $data_array = explode( "\n", $data );

	try {
        $codex_to = new CodexConverter('JaCodex');
        $new_data = $codex_to->convert( $data_array );
		echo implode( "\n", $new_data );
	} catch ( Exception $e ) {
		$error_message = "ERROR: " . $e->getMessage() . ", TRACE: " . $e->getTraceAsString();
		echo $error_message;
	}
}
else
{
    echo 'ERROR: The parameter of "request" is not found.';
}
?>
