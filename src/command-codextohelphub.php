<?php
/**
 * command line interface converts Codex to Help Hub
 *
 * Codex article passed as input file is converted to HelpHub or WordPress
 * format and written out to the file.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

require_once( 'class-codex.php' );

$options = getopt( 'i:o:' );
if ( 2 != count( $options) ) {
    print 'Please specify required options \'-i <input_file> -o <output_file>\'.\n';
    exit;
}
$input_file = $options[ 'i' ];
$output_file = $options[ 'o' ];

$codex_to = new Codex( Codex::TO_HELPHUB );
ini_set( 'display_errors', 'On' );

$in = file( $options[ 'i' ], FILE_IGNORE_NEW_LINES );
$out = $codex_to->convert( $in );

$fp = fopen( $options[ 'o' ], 'w' );
fputs( $fp, implode( '\n', $out ) );
fclose( $fp );

?>
