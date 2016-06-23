<?php

// require_once( 'interface-converter.php' );
// require_once( 'class-result.php' );
// require_once( 'class-adapter.php' );

class JaCodexConverterMgr {

	// static $filter;

	public function __construct( $filter_type ) {
		// $this->filter = new $filter_type();
        // Adapter::initialize( $filter_type );
	}

	public function get_converter( $type ) {
		// error_log( __METHOD__ . " type=" . $type );
		$classname = 'JaCodex' . $type;
		return new $classname( $type );
	}
}

abstract class JaCodexConverter {

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
		$patterns = array( "/\[\[Function[ _]Reference\//",
						   "/\[\[Category\:(.*?)\]\]/");
        $replaces = array( '[[関数リファレンス/',
						   'Category:$1');
	    $patterns[] = '/^(.*)is located in (.*)./';
		$replaces[] = '${1} は ${2} で定義されています。';

		$patterns[] = '/%%%(.*?)%%%/';
		$replaces[] = '<pre>${1}</pre>';

		$patterns[] = '/\* Since \[\[(.*?)\]\]/';
		$replaces[] = '* [[${1}]] 以降';

        $new_line = preg_replace( $patterns, $replaces, $line );
        return $new_line;
    }
}

class JaCodexPlainConverter extends JaCodexConverter {
	public function convert( $line ) {
        $new_line = $this->word_convert( $line );
        // $new_line = Adapter::convert( $this->get_type(), $line );
		// Result::get_object()->add( "<p>" . $new_line . "</p>" );
		Result::get_object()->add( $new_line );
    }
}

class JaCodexTitleConverter extends JaCodexConverter {
	public function convert( $line ) {
		/*
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
        */
		$patterns = array();
		$replaces = array();

		$patterns[0] = '/==[ ]*Description[ ]*==/';
		$replaces[0] = '== 説明<span id="Description"></span> ==';

		$patterns[1] = '/==[ ]*Usage[ ]*==/';
		$replaces[1] = '== 使い方<!--Usage--> ==';

		$patterns[2] = '/==[ ]*Return Values[ ]*==/';
		$replaces[2] = '== 戻り値<!--Return Values--> ==';

		$patterns[3] = '/==[ ]*Examples[ ]*==/';
		$replaces[3] = '== 用例<!--Examples--> ==';

		$patterns[4] = '/==[ ]*Notes[ ]*==/';
		$replaces[4] = '== 注<!--Notes--> ==';

		$patterns[5] = '/==[ ]*Change Log[ ]*==/';
		$replaces[5] = '== 変更履歴<!--Change Log--> ==';

		$patterns[6] = '/==[ ]*Source File[ ]*==/';
		$replaces[6] = '== ソースファイル<!--Source File--> ==';

		$patterns[7] = '/==[ ]*Related[ ]*==/';
		$replaces[7] = '== 関連<!--Related--> ==';

		$patterns[8] = '/==[ ]*Parameters[ ]*==/';
		$replaces[8] = '== パラメータ<!--Parameters--> ==';

/*
		$patterns[9] = '/(.*)is located in (.*)\./';
		$replaces[9] = '${1} は ${2} で定義されています。';

		$patterns[10] = '/%%%(.*)%%%/';
		$replaces[10] = '<pre>${1}</pre>';

		$patterns[11] = '/\* Since \[\[(.*)\]\]/';
		$replaces[11] = '* [[${1}]] 以降';
*/
		$new_line = preg_replace( $patterns, $replaces, $line, -1, $count );
		if ( 0 < $count ) {
			Result::get_object()->add( $new_line );
			return;
		}

        $patterns = array( '/^(=+)[ ]*(.*?)[ ]*(=+)/');
		// $replaces = array( '$1$2$3<span id="$2"></span>' );
        // $new_line = preg_replace( $patterns, $replaces, $line );

		$replaces = array( '$1' );
        $title_tag = preg_replace( $patterns, $replaces, $line );
		$replaces = array( '$2' );
        $title = preg_replace( $patterns, $replaces, $line );
		$tagid = preg_replace( '/ /', '_', $title );
		$new_line = "$title_tag $title <span id=\"$tagid\"></span> $title_tag";

		// $new_line = Adapter::convert( $this->get_type(), $line );
        Result::get_object()->add( $new_line );
    }
}

class JaCodexStarConverter extends JaCodexConverter {
/*
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_object()->add( '<ul>' );
    }

	public function __destruct() {
		// error_log( __METHOD__ . " DEBUG 01");
		Result::get_object()->add( '</ul>' );
	}
*/
	/*
    public function close() {
        Result::get_object()->add( '</ul>' );
    }
	*/

    public function convert( $line ) {
		$new_line = $this->word_convert( $line );
		Result::get_object()->add( $new_line );
/*
        $patterns = array( '/^\*[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( '<li>' . $new_line . '</li>' );
*/
    }
}

class JaCodexSharpConverter extends JaCodexConverter {
/*
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_object()->add( '<ol>' );
    }

	public function __destruct() {
		// error_log( __METHOD__ . " DEBUG 01");
		Result::get_object()->add( '</ol>' );
	}
*/
	/*
    public function close() {
        Result::get_object()->add( '</ol>' );
    }
	*/

    public function convert( $line ) {
		$new_line = $this->word_convert( $line );
		Result::get_object()->add( $new_line );
/*
        $patterns = array( '/^#[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( '<li>' . $new_line . '</li>' );
*/
    }
}

class JaCodexColonConverter extends JaCodexConverter {
	public function convert( $line ) {
		$new_line = $this->word_convert( $line );
		Result::get_object()->add( $new_line );

/*
        $patterns = array( '/^:(.*?)$/');
        $replaces = array( '<p style="padding-left: 30px;">$1</p>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
		// $new_line = Adapter::convert( $this->get_type(), $new_line );
        Result::get_object()->add( $new_line );
*/
    }
}

class JaCodexSemicolonConverter extends JaCodexConverter {
	public function convert( $line ) {
		$new_line = $this->word_convert( $line );
		Result::get_object()->add( $new_line );

/*

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
*/

    }
}

class JaCodexSpaceConverter extends JaCodexConverter {
	public function convert( $line ) {
        $patterns = array( '/^[ ]+(.+)$/' );
        $replaces = array( '<pre>$1</pre>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        Result::get_object()->add( $new_line );
    }
}

class JaCodexPreConverter extends JaCodexConverter {
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
			// $new_line = '[code language="php"]' . $code . '[/code]';
			$new_line = $line;
            $this->in_pre_tab = false;
		} elseif ( preg_match( '/^<pre[ ]?.*?>(.*)/', $line ) ) {
/*
			$code = preg_replace( '/^<pre[ ]?.*?>(.*)/', '$1', $line);
			// $new_line = '[code language="php"]' . htmlspecialchars( $code );
			$new_line = '[code language="php"]' . $code;
*/
			$new_line = $line;
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
/*
            $code = preg_replace( '/(.*?)<\/pre>/', '$1', $line);
			// $new_line = htmlspecialchars( $code ) . "</pre>";
			// $new_line = htmlspecialchars( $code ) . "[/code]";
			$new_line = $code . "[/code]";
*/
			$new_line = $line;
            $this->in_pre_tab = false;
        } else {
			// $new_line = htmlspecialchars( $line );
			$new_line = $line;
        }
        Result::get_object()->add( $new_line );
    }
}

class JaCodexBraceConverter extends JaCodexConverter {
	private $in_lang_locator = false;

    public function convert( $line ) {
        if ( preg_match( "/^\{\{Languages\|/", $line ) ) {
            $this->in_lang_locator = true;
        } elseif ( preg_match( "/^\}\}/", $line ) ) {
			$this->in_lang_locator = false;
		} else {
	        if ( $this->in_lang_locator ) {
	            // {{en|Administration Screens}} -> [[en:Administration Screens]]
				$patterns = array( '/^\{\{(.*?)\|(.*?)\}\}/' );
		        $replaces = array( '[[$1:$2]]' );
		        $new_line = preg_replace( $patterns, $replaces, $line );
				// Result::get_object()->add( $new_line );
				Result::get_object()->add_bottom( $new_line );
	        } else {
	            $new_line = $line;
	            Result::get_object()->add( $new_line );
	        }
        }
    }
}

?>
