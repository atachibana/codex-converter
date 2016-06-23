<?php

mb_language("ja");
mb_internal_encoding('UTF-8');

use phpunit\framework\TestCase;

class CodexConverterJaCodexTest extends TestCase {

    public function test01_Basic() {
        $codex_to = new CodexConverter( CodexConverter::TO_JA_CODEX );
        $in = array( "==Description==" );
        $expected = array( "== 説明<span id=\"Description\"></span> ==" );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

}
