<?php

use phpunit\framework\TestCase;

class CodexConverterHelpHubTest extends TestCase {

    public function test01_Basic() {
        $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );
        $in = array( "==TITLE==" );
        $expected = array( "<h2>TITLE</h2>" );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test02_Grammer() {
        $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );
        $in = file( "tests/1_test.txt", FILE_IGNORE_NEW_LINES );
        $expected = file ( "tests/1_test_expected.txt", FILE_IGNORE_NEW_LINES );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test03_EndTagOrder() {
      $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );

      $in = array( "*Star", "#Sharp" );
      $expected = array( "<ul>", "<li>Star</li>", "</ul>", "<ol>", "<li>Sharp</li>", "</ol>" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test04_ContinuedBrace() {
      $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );

      $in = array( "{{before}}", "{{Languages|", "{{en:test}}", "}}", "{{after}}" );
      $expected = array( "{{before}}", "{{after}}" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test05_CodexArticle() {
      $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );

      $in = file( "tests/2_readmore.txt", FILE_IGNORE_NEW_LINES );
      $expected = file ( "tests/2_readmore_expected.txt", FILE_IGNORE_NEW_LINES );
      $out = $codex_to->convert( $in );
      // print_r( $out );
      $this->assertEquals( $expected, $out );
    }

    public function test06_Pre() {
      $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );

      $in = array( "<pre><!--more--></pre>" );
      // $expected = array( '[code language="php"]&lt;!--more--&gt;[/code]' );
      $expected = array( '[code language="php"]<!--more-->[/code]' );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );

      $in = array( "<pre>", "<!--more--></pre>" );
      // $expected = array( '[code language="php"]', "&lt;!--more--&gt;[/code]" );
      $expected = array( '[code language="php"]', "<!--more-->[/code]" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );

      $in = array( "<pre>", "<!--more-->", "</pre>" );
      // $expected = array( '[code language="php"]', "&lt;!--more--&gt;", "[/code]" );
      $expected = array( '[code language="php"]', "<!--more-->", "[/code]" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test07_SemiIncludingURL() {
        $codex_to = new CodexConverter( CodexConverter::TO_HELPHUB );

        $in = array( ";URL for [http://purl.org/rss/1.0/  RDF/RSS 1.0 feed] :<tt>&lt;?php bloginfo('rdf_url'); ?></tt>" );
        $expected = array( '<strong>URL for <a href="http://purl.org/rss/1.0/"> RDF/RSS 1.0 feed</a> </strong>
<p style="padding-left: 30px;"><code>&lt;?php bloginfo(\'rdf_url\'); ?></code></p>' );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

}
