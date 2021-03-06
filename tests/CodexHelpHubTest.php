<?php

use PHPUnit\Framework\TestCase;

class CodexHelpHubTest extends TestCase {

    public function test01_Basic() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = array( "==TITLE==" );
        $expected = array( "<h2>TITLE<span id=\"TITLE\"></span></h2>" );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test02_Grammer() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = file( "tests/1_test.txt", FILE_IGNORE_NEW_LINES );
        $expected = file ( "tests/1_test_expected.txt", FILE_IGNORE_NEW_LINES );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test03_EndTagOrder() {
      $codex_to = new Codex( Codex::TO_HELPHUB );
      // In Logger class, display_errors is turned off.
      // To show error, turn on here.
      ini_set( 'display_errors', 'On' );
      $in = array( "*Star", "#Sharp" );
      $expected = array( "<ul>", "<li>Star</li>", "</ul>", "<ol>", "<li>Sharp</li>", "</ol>" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test04_ContinuedBrace() {
      $codex_to = new Codex( Codex::TO_HELPHUB );
      // In Logger class, display_errors is turned off.
      // To show error, turn on here.
      ini_set( 'display_errors', 'On' );
      $in = array( "{{before}}", "{{Languages|", "{{en|test}}", "{{ja|TEST}}", "}}", "{{after}}" );
      $expected = array( "{{before}}", '[codex_languages en="test" ja_codex="TEST"]', "{{after}}" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );

	  // short code cannot handle hyphne character well. We have to absorve it
	  $in = array( "{{Languages|", "{{pt-br|TEST}}", "{{zh-cn|TEST}}", "{{zh-tw|TEST}}", "}}");
      $expected = array( '[codex_languages ptbr_codex="TEST" zhcn_codex="TEST" zhtw_codex="TEST"]');
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test05_CodexArticle() {
      $codex_to = new Codex( Codex::TO_HELPHUB );
      // In Logger class, display_errors is turned off.
      // To show error, turn on here.
      ini_set( 'display_errors', 'On' );
      $in = file( "tests/2_readmore.txt", FILE_IGNORE_NEW_LINES );
      $expected = file ( "tests/2_readmore_expected.txt", FILE_IGNORE_NEW_LINES );
      $out = $codex_to->convert( $in );
      // print_r( $out );
      $this->assertEquals( $expected, $out );
    }

    public function test06_Pre() {
      $codex_to = new Codex( Codex::TO_HELPHUB );
      // In Logger class, display_errors is turned off.
      // To show error, turn on here.
      ini_set( 'display_errors', 'On' );
      $in = array( "<pre><!--more--></pre>" );
      $expected = array( '[code language="php"]<!--more-->[/code]' );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );

      $in = array( "<pre>", "<!--more--></pre>" );
      $expected = array( '[code language="php"]', "<!--more-->[/code]" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );

      $in = array( "<pre>", "<!--more-->", "</pre>" );
      $expected = array( '[code language="php"]', "<!--more-->", "[/code]" );
      $out = $codex_to->convert( $in );
      $this->assertEquals( $expected, $out );
    }

    public function test07_SemiIncludingURL() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = array( ";URL for [http://purl.org/rss/1.0/  RDF/RSS 1.0 feed] :<tt><?php bloginfo('rdf_url'); ?></tt>" );
        $expected = array( '<strong>URL for <a href="http://purl.org/rss/1.0/"> RDF/RSS 1.0 feed</a> </strong>
<p style="padding-left: 30px;"><code>&lt;?php bloginfo(\'rdf_url\'); ?></code></p>' );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test08_NoWiki() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = array( 'converted to <nowiki><br /></nowiki>' );
        $expected = array( '<p>converted to <code>&lt;br /></code></p>' );
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test09_StringTypeInputTest() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = "==String Test==";
        $expected = "<h2>String Test<span id=\"String_Test\"></span></h2>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test10_SpaceOnly() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = "  ";
        $expected = "<p>  </p>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

    public function test11_TTandA() {
        $codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );
        $in = "<tt>[[Template Tags/the_content|the_content()]]</tt>";
        $expected = "<p><a href=\"https://codex.wordpress.org/Template_Tags/the_content\"><code>the_content()</code></a></p>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }

	public function test12_section_id() {
		$codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );

        $in = "[[#External References|External Reference]]";
        $expected = "<p><a href=\"#External_References\">External Reference</a></p>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );

		$in = "[[Administration Screens#Appearance|Appearance]]";
        $expected = "<p><a href=\"https://codex.wordpress.org/Administration_Screens#Appearance\">Appearance</a></p>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );

		$in = "====== External References ======";
        $expected = "<h6>External References<span id=\"External_References\"></span></h6>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );

		$in = "==Modify <tt>the_excerpt()</tt>==";
        $expected = "<h2>Modify <tt>the_excerpt()</tt><span id=\"Modify_the_excerpt()\"></span></h2>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );

		$in = "== Customizing the \"more&hellip;\" text ==";
        $expected = "<h2>Customizing the \"more&hellip;\" text<span id=\"Customizing_the_&quot;more&amp;hellip;&quot;_text\"></span></h2>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );

		$in = '====When to set $more====';
        $expected = '<h4>When to set $more<span id="When_to_set_$more"></span></h4>';
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
	}
/*
    public function test13_blockquote_issue2() {
		$codex_to = new Codex( Codex::TO_HELPHUB );
        // In Logger class, display_errors is turned off.
        // To show error, turn on here.
        ini_set( 'display_errors', 'On' );

        $in = "<blockquote><i>test</i></blockquote>";
        $expected = "<blockquote><i>test</i></blockquote>";
        $out = $codex_to->convert( $in );
        $this->assertEquals( $expected, $out );
    }
*/	
}
