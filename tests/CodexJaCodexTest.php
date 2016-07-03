<?php

mb_language("ja");
mb_internal_encoding('UTF-8');

use phpunit\framework\TestCase;

class CodexJaCodexTest extends TestCase {

    public function test01_Basic() {
        $codex_to = new Codex( Codex::TO_JACODEX );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = array( "==Description==" );
        $expected = array( "== 説明<span id=\"Description\"></span> ==" );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test02_Grammer() {
        $codex_to = new Codex( Codex::TO_JACODEX );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = file( "tests/ja1_test.txt", FILE_IGNORE_NEW_LINES );
        $expected = file ( "tests/ja1_test_expected.txt", FILE_IGNORE_NEW_LINES );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

}
