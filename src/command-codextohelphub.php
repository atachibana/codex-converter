<?php
/*****************************************************************************
 * command line interface of CodexToHelpHub
 *
 * by Akira Tachibana
 ****************************************************************************/
require_once( 'class-codextohelphub.php' );

$options = getopt("i:o:");
if ( 2 != count( $options) ) {
    print "Please specify required options '-i <input_file> -o <output_file>'.\n";
    exit;
}
$input_file = $options[ "i" ];
$output_file = $options[ "o" ];

$codex_to = new CodexToHelpHub();
ini_set( 'display_errors', 'On' );

$in = file( $options[ "i" ], FILE_IGNORE_NEW_LINES );
$out = $codex_to->migrate( $in );

$fp = fopen( $options[ "o" ], "a" );
fputs( $fp, implode( "\n", $out ) );
fclose( $fp );

?>
