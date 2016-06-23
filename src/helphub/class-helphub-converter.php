<?php

// require_once( 'interface-converter.php' );
// require_once( 'class-result.php' );
// require_once( 'class-adapter.php' );

class HelpHubConverterMgr {

	// static $filter;

	public function __construct( $filter_type ) {
		// $this->filter = new $filter_type();
        // Adapter::initialize( $filter_type );
	}

	public function get_converter( $type ) {
		// error_log( __METHOD__ . " type=" . $type );
		$classname = $type;
		return new $classname( $type );
	}
}

abstract class Converter {

    const TYPE_BASE      = 'Converter';
    const TYPE_PLAIN     = 'PlainConverter';
	const TYPE_TITLE     = 'TitleConverter';        // == Title ==
	const TYPE_STAR      = 'StarConverter';         // *bullet list
	const TYPE_SHARP     = 'SharpConverter';        // #number list
	const TYPE_PRE       = 'PreConverter';          // <pre>...</pre>
	const TYPE_COLON     = 'ColonConverter';        // ;sub-title:description
	const TYPE_SEMICOLON = 'SemicolonConverter';    // ;sub-title:description
	const TYPE_SPACE     = 'SpaceConverter';        //   x = 10;
	const TYPE_BRACE     = 'BraceConverter';        // {{ or }}

	private $type;

	public function __construct( $type ) {
		// error_log( "DEBUG: " . __METHOD__ . " type=" . $type );
		$this->type = $type;
	}

	public function __destruct() {
	}

	public function get_type() {
		// error_log( "DEBUG: " . __METHOD__ . " type=" . $this->type );
		return $this->type;
	}

	public function keep_format() {
		return false;
	}

	public function convert( $line ) {}
	// public function close() {}

/*
	FilterMgr::initialize( $type );
	FilterMgr::filter( $type, $line );
	FilterMgr::get_oject( $type )->filter( $type, $line );
*/

	protected function word_convert( $line ) {
		$patterns = array( "/\\\'\\\'\\\'(.*?)\\\'\\\'\\\'/",
                           "/\\\'\\\'(.*?)\\\'\\\'/",
                           "/\\\'(.*?)\\\'/",
                           '/\\\"(.*?)\\\"/',
						   "/\'\'\'(.*?)\'\'\'/",
                           "/\'\'(.*?)\'\'/",
						   "/\'(.*?)\'/",
						   '/\"(.*?)\"/',
						   "/<tt>(.*?)<\/tt>/",
                           "/\[\[Function[ _]Reference\/(.*?)\|(.*?)\]\]/",
						   "/\[\[Category\:(.*?)\]\]/",
						   "/\[\[Image\:(.*?)\]\]/",
                           "/\[\[(((?!\]\]).)*?)\|(.*?)\]\]/",
                           "/\[\[(.*?)\]\]/",
                           "/\[http(.*?) (.*?)\]/",
                           "/\[http(.*?)\]/" );
        $replaces = array( '<strong>$1</strong>',
                           '<em>$1</em>',
                           '\'$1\'',
                           '"$1"',
						   '<strong>$1</strong>',
						   '<em>$1</em>',
						   '\'$1\'',
						   '"$1"',
						   '<code>$1</code>',
                           '<a href="https://developer.wordpress.org/reference/functions/$1">$2</a>',
						   'Category:$1',
						   '<br /><strong>*** [TODO] Embed Image HERE !!! ***: $1 </strong><br />',
						   '<a href="https://codex.wordpress.org/$1">$3</a>',
						   '<a href="https://codex.wordpress.org/$1">$1</a>',
                           '<a href="http$1">$2</a>',
                           '<a href="http$1">http$1</a>');
        $new_line = preg_replace( $patterns, $replaces, $line );
        return $new_line;
    }
}

class PlainConverter extends Converter {
	public function convert( $line ) {
        $new_line = $this->word_convert( $line );
        // $new_line = Adapter::convert( $this->get_type(), $line );
        Result::get_object()->add( "<p>" . $new_line . "</p>" );
    }
}

class TitleConverter extends Converter {
	public function convert( $line ) {

        $patterns = array( '/^======[ ]*(.*?)[ ]*======/',
                           '/^=====[ ]*(.*?)[ ]*=====/',
                           '/^====[ ]*(.*?)[ ]*====/',
                           '/^===[ ]*(.*?)[ ]*===/',
                           '/^==[ ]*(.*?)[ ]*==/',
                           '/^=[ ]*(.*?)[ ]*=/' );
        $replaces = array( '<h6>$1</h6>',
                           '<h5>$1</h5>',
                           '<h4>$1</h4>',
                           '<h3>$1</h3>',
                           '<h2>$1</h2>',
                           '<h1>$1</h1>' );
        $new_line = preg_replace( $patterns, $replaces, $line );

		// $new_line = Adapter::convert( $this->get_type(), $line );
        Result::get_object()->add( $new_line );
    }
}

class StarConverter extends Converter {
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_object()->add( '<ul>' );
    }

	public function __destruct() {
		// error_log( __METHOD__ . " DEBUG 01");
		Result::get_object()->add( '</ul>' );
	}

	/*
    public function close() {
        Result::get_object()->add( '</ul>' );
    }
	*/

    public function convert( $line ) {
        $patterns = array( '/^\*[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( '<li>' . $new_line . '</li>' );
    }
}

class SharpConverter extends Converter {
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_object()->add( '<ol>' );
    }

	public function __destruct() {
		// error_log( __METHOD__ . " DEBUG 01");
		Result::get_object()->add( '</ol>' );
	}

	/*
    public function close() {
        Result::get_object()->add( '</ol>' );
    }
	*/

    public function convert( $line ) {
        $patterns = array( '/^#[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( '<li>' . $new_line . '</li>' );
    }
}

class ColonConverter extends Converter {
	public function convert( $line ) {
        $patterns = array( '/^:(.*?)$/');
        $replaces = array( '<p style="padding-left: 30px;">$1</p>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( $new_line );
    }
}

class SemicolonConverter extends Converter {
	public function convert( $line ) {
		// $patterns = array( '/^;(.*?):(.*)$/',
		// NG --- $patterns = array( '/^;(((?!:\/\/).)*?):(.*)$/',
		$patterns = array( '/^;(.*?):((?!\/\/).)(.*)$/',
                           '/^;[ ]*(.*)$/');
		// $replaces = array( '1=$1,2=$2,3=$3,4=$4,5=$5',
		// $replaces = array( '<strong>$1</strong>' . PHP_EOL . '<p style="padding-left: 30px;">$2</p>',
		$replaces = array( '<strong>$1</strong>' . PHP_EOL . '<p style="padding-left: 30px;">$2$3</p>',
                           '<strong>$1</strong>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( $new_line );
    }
}

class SpaceConverter extends Converter {
	public function convert( $line ) {
        $patterns = array( '/^[ ]+(.+)$/' );
        $replaces = array( '[code language="php"]$1[/code]' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        Result::get_object()->add( $new_line );
    }
}

class PreConverter extends Converter {
	private $in_pre_tab = true;

    public function keep_format() {
        return $this->in_pre_tab;
    }

    /**
     * <pre> line migrator
     *
     * if blocks are
     * 1) <pre>text</pre>
     * 2) <pre>text
     * 3) text</pre>
     * 4) text (exists in between <pre> and </pre>)
     * Notice about $in_pre_tab is set to false when </pre> included line.
     *
     * @param string $line wiki format text
     */
    public function convert( $line ) {
		if ( preg_match( '/^<pre[ ]?.*?>(.*?)<\/pre>/', $line ) ) {
            $code = preg_replace( '/^<pre[ ]?.*?>(.*?)<\/pre>/', '$1', $line);
			// $new_line = '[code language="php"]' . htmlspecialchars( $code ) . '[/code]';
			$new_line = '[code language="php"]' . $code . '[/code]';
            $this->in_pre_tab = false;
		} elseif ( preg_match( '/^<pre[ ]?.*?>(.*)/', $line ) ) {
			$code = preg_replace( '/^<pre[ ]?.*?>(.*)/', '$1', $line);
			// $new_line = '[code language="php"]' . htmlspecialchars( $code );
			$new_line = '[code language="php"]' . $code;
	/*
        if ( preg_match( '/^<pre>(.*?)<\/pre>/', $line ) ) {
            $code = preg_replace( '/^<pre>(.*?)<\/pre>/', '$1', $line);
            $new_line = "<pre>" . htmlspecialchars( $code ) . "</pre>";
            $this->in_pre_tab = false;
		} elseif ( preg_match( '/^<pre (.*?)>(.*?)<\/pre>/', $line ) ) {
			$pre_tag = preg_replace( '/^<pre (.*?)>(.*?)<\/pre>/', '$1', $line);
			$code = preg_replace( '/^<pre (.*?)>(.*?)<\/pre>/', '$2', $line);
            $new_line = "<pre" . $pre_tag . ">" . htmlspecialchars( $code ) . "</pre>";
            $this->in_pre_tab = false;
		} elseif ( preg_match( '/^<pre>(.*)/', $line ) ) {
            $code = preg_replace( '/^<pre>(.*)/', '$1', $line);
            $new_line = "<pre>" . htmlspecialchars( $code );
		} elseif ( preg_match( '/^<pre (.*?)>(.*)/', $line ) ) {
			$pre_tag = preg_replace( '/^<pre (.*?)>(.*)/', '$1', $line);
			$code = preg_replace( '/^<pre (.*?)>(.*)/', '$2', $line);
            $new_line = "<pre" . $pre_tag . ">" . htmlspecialchars( $code );
	*/
        } elseif ( preg_match( '/(.*?)<\/pre>/', $line ) ) {
            $code = preg_replace( '/(.*?)<\/pre>/', '$1', $line);
			// $new_line = htmlspecialchars( $code ) . "</pre>";
			// $new_line = htmlspecialchars( $code ) . "[/code]";
			$new_line = $code . "[/code]";
            $this->in_pre_tab = false;
        } else {
			// $new_line = htmlspecialchars( $line );
			$new_line = $line;
        }
        Result::get_object()->add( $new_line );
    }
}

class BraceConverter extends Converter {
	private $in_lang_locator = false;

    public function convert( $line ) {
        if ( preg_match( "/^\{\{Languages\|/", $line ) ) {
            $this->in_lang_locator = true;
        }

        if ( $this->in_lang_locator ) {
            // NOP - Don't need output of $line
        } else {
            $new_line = $line;
            Result::get_object()->add( $new_line );
        }

        if ( preg_match( "/^\}\}/", $line ) ) {
            $this->in_lang_locator = false;
        }
    }
}

?>
