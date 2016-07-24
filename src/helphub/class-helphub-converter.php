<?php
/**
 * HelpHub Converter classes.
 *
 * Defines rule of Codex to HelpHub conversion.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

/**
 * Base class of Converters.
 *
 * Defines common constants and methods. In concrete object, defines
 * common conversion rule can be shared among child classes.
 */
abstract class HelpHubConverter implements Converter {

	private $type = "";

	/**
	 * Keeps sub converter for nested elements such as sub list.
	 */
	protected $sub_converter = null;

	/**
	 * Initializes $type by input line type.
	 *
	 * @param string $type line type. values are Converter::TYPE_XXX.
	 */
	public function __construct( $type ) {
		$this->type = $type;
	}

	/**
	 * Returns line type.
	 *
	 * @return string line type. values are Converter::TYPE_XXX.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Indicates the same Converter should be re-used.
	 *
	 * For example, in the <pre> tag, the 1st column "*" does not mean the
	 * <li>. In <pre> tag, this method should return true until </pre>.
	 *
	 * @return boolean true when it should continue the same line type.
	 */
	public function keep_format() {
		return false;
	}

	/**
	 * Converts Codex line.
	 *
	 * It does not return the converted text, just stores into Result object.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {}

	/**
	 * Converts Codex or Media Wiki word format.
	 *
	 * @param string $line should be converted.
	 * @return string converted text.
	 */
	protected function word_convert( $line ) {
		$patterns[] = "/\\\'\\\'\\\'(.*?)\\\'\\\'\\\'/";
		$replaces[] = '<strong>$1</strong>';

		$patterns[] = "/\'\'\'(.*?)\'\'\'/";
		$replaces[] = '<strong>$1</strong>';

		$patterns[] = "/\\\'\\\'(.*?)\\\'\\\'/";
		$replaces[] = '<em>$1</em>';

		$patterns[] = "/\'\'(.*?)\'\'/";
		$replaces[] = '<em>$1</em>';

		$patterns[] = "/\\\'(.*?)\\\'/";
		$replaces[] = "'$1'";

		$patterns[] = "/\'(.*?)\'/";
		$replaces[] = "'$1'";

		$patterns[] = '/\\\"(.*?)\\\"/';
		$replaces[] = '"$1"';

		$patterns[] = '/\"(.*?)\"/';
		$replaces[] = '"$1"';

		$patterns[] = '/<tt>(.*?)<\/tt>/';
		$replaces[] = '<code>$1</code>';

		$patterns[] = "/\[\[Function[ _]Reference\/(.*?)\|(.*?)\]\]/";
		$replaces[] = '<a href="https://developer.wordpress.org/reference/functions/$1">$2</a>';

		$patterns[] = "/\[\[Category\:(.*?)\]\]/";
		$replaces[] = 'Category:$1';

		$patterns[] = "/\[\[Image\:(.*?)\]\]/";
		$replaces[] = '<br /><strong>*** [TODO] Embed Image HERE !!! ***: $1 </strong><br />';

		$patterns[] = "/\[\[(((?!\]\]).)*?)\|(.*?)\]\]/";
		$replaces[] = '<a href="https://codex.wordpress.org/$1">$3</a>';

		$patterns[] = "/\[\[(.*?)\]\]/";
		$replaces[] = '<a href="https://codex.wordpress.org/$1">$1</a>';

		$patterns[] = "/\[http(.*?) (.*?)\]/";
		$replaces[] = '<a href="http$1">$2</a>';

		$patterns[] = "/\[http(.*?)\]/";
		$replaces[] = '<a href="http$1">http$1</a>';

        $new_line = preg_replace( $patterns, $replaces, $line, -1, $count );
		if ( 0 < $count ) {
			return $new_line;
		}

		// <nowiki><br /></nowiki> -> <pre>&lt;br /&gt;</pre>
		$patterns = array( '/<nowiki>/', '/<\/nowiki>/' );
		$replaces = array( 'atachibana-begin', 'atachibana-end' );
		$temp_line = preg_replace( $patterns, $replaces, $line, -1, $count );
		if ( 0 < $count ) {
			$temp_line = htmlspecialchars( $temp_line );
			$patterns = array( '/atachibana-begin/', '/atachibana-end/' );
			$replaces = array( '<code>', '</code>' );
			$new_line = preg_replace( $patterns, $replaces, $temp_line );
			return $new_line;
		}
		return $line;
    }
}

/**
 * Plain text converter class.
 */
class HelpHubPlainConverter extends HelpHubConverter implements PlainConverter {

	/**
	 * Converts Plain text.
	 *
	 * Encloses by <p> tag.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        $new_line = $this->word_convert( $line );
        Result::get_result()->add( '<p>' . $new_line . '</p>' );
    }
}

/**
 * Title line converter class.
 */
class HelpHubTitleConverter extends HelpHubConverter implements TitleConverter {

	/**
	 * Converts Title line.
	 *
	 * Encloses by <h1> - <h6> tag.
	 *
	 * @param string $line should be converted.
	 */
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
        Result::get_result()->add( $new_line );
    }
}

/**
 * Star line converter class.
 */
class HelpHubStarConverter extends HelpHubConverter implements StarConverter {

	/**
	 * Outputs <ul> tag from constructor.
	 *
	 * @param string $type line type. It must be Converter::TYPE_STAR.
	 */
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_result()->add( '<ul>' );
    }

	/**
	 * Outputs </ul> tag from destructor.
	 */
	public function __destruct() {
		if ( $this->sub_converter ) {
			unset( $this->sub_converter );
		}
		Result::get_result()->add( '</ul>' );
	}

	/**
	 * Converts star line.
	 *
	 * Encloses by <li> tag.
	 *
	 * @param string $line should be converted.
	 */
    public function convert( $line ) {
        $patterns = array( '/^\*[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );

		// TODO: Refactoring with class-codex.php unification
		$sub_type = Util::get_type( $new_line );

		if ( $this->sub_converter ) {
			// previous Converter says it should be kept or the previous
			// line type and current line type is the same
			if ( $sub_type == $this->sub_converter->get_type() ) {
					 // NOP. same Converter object is re-used.
			} else {
				// explicitly calls destructor. Otherwise, the order of end
				// tag and start tag must be wrong.
				unset( $this->sub_converter );
				if ( ( $sub_type == Converter::TYPE_STAR ) || ( $sub_type == Converter::TYPE_STAR ) ) {
					$this->sub_converter = Util::get_converter( Codex::TO_HELPHUB, $sub_type );
				} else {
					$this->sub_converter = null;
				}
			}
		} else {
			if ( ( $sub_type == Converter::TYPE_STAR ) || ( $sub_type == Converter::TYPE_STAR ) ) {
				$this->sub_converter = Util::get_converter( Codex::TO_HELPHUB, $sub_type );
			}
		}

		if ( $this->sub_converter ) {
			$this->sub_converter->convert( $new_line );
		} else {
			$new_line = $this->word_convert( $new_line );
	        Result::get_result()->add( '<li>' . $new_line . '</li>' );
		}
	}
}

/**
 * Sharp line converter class.
 */
class HelpHubSharpConverter extends HelpHubConverter implements SharpConverter {

	/**
	 * Outputs <ol> tag from constructor.
	 *
	 * @param string $type line type. It must be Converter::TYPE_SHARP.
	 */
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_result()->add( '<ol>' );
    }

	/**
	 * Outputs </ol> tag from destructor.
	 */
	public function __destruct() {
		Result::get_result()->add( '</ol>' );
	}

	/**
	 * Converts star line.
	 *
	 * Encloses by <li> tag.
	 *
	 * @param string $line should be converted.
	 */
    public function convert( $line ) {
        $patterns = array( '/^#[ ]*(.*?)/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
        Result::get_result()->add( '<li>' . $new_line . '</li>' );
    }
}

/**
 * Colon line converter class.
 */
class HelpHubColonConverter extends HelpHubConverter implements ColonConverter {

	/**
	 * Converts colon line.
	 *
	 * Indent the line.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        $patterns = array( '/^:(.*?)$/');
        $replaces = array( '<p style="padding-left: 30px;">$1</p>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
        Result::get_result()->add( $new_line );
    }
}

/**
 * Semicolon line converter class.
 */
class HelpHubSemicolonConverter extends HelpHubConverter implements SemicolonConverter {

	/**
	 * Converts semicolon line.
	 *
	 * ;title: content ... <strong>title</strong> LF indented content
	 * ;title          ... <strong>title</strong>
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
		$patterns = array( '/^;(.*?):((?!\/\/).)(.*)$/',
                           '/^;[ ]*(.*)$/');
		$replaces = array( '<strong>$1</strong>' . PHP_EOL . '<p style="padding-left: 30px;">$2$3</p>',
                           '<strong>$1</strong>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        $new_line = $this->word_convert( $new_line );
        Result::get_result()->add( $new_line );
    }
}

/**
 * Space line converter class.
 */
class HelpHubSpaceConverter extends HelpHubConverter implements SpaceConverter {

	/**
	 * Outputs [code language="php"] tag from constructor.
	 *
	 * @param string $type line type. It must be Converter::TYPE_SHARP.
	 */
	public function __construct( $type ) {
		parent::__construct( $type );
        Result::get_result()->add( '[code language="php"]' );
    }

	/**
	 * Outputs [/code] tag from destructor.
	 */
	public function __destruct() {
		Result::get_result()->add( '[/code]' );
	}

	/**
	 * Converts space line.
	 *
	 * Note: It always converts to [code language=php]. Migrator must adjust it.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        $patterns = array( '/^[ ]+(.+)$/' );
        $replaces = array( '$1' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        Result::get_result()->add( $new_line );
    }
}

/**
 * Pre line converter class.
 */
class HelpHubPreConverter extends HelpHubConverter implements PreConverter {
	private $in_pre_tab = true;

	/**
	 * Indicates we should keep to use PreConverter for <pre> block.
	 *
	 * @return boolean true if we are in the <pre> blocks.
	 */
    public function keep_format() {
        return $this->in_pre_tab;
    }

    /**
     * Converts <pre> line.
     *
     * Four if & elses blocks mean
     * 1) <pre>text</pre>
     * 2) <pre>text
     * 3) text</pre>
     * 4) text (exists in between <pre> and </pre>)
     * Notice about $in_pre_tab is set to false when </pre> included line.
     *
	 * @param string $line should be converted.
     */
    public function convert( $line ) {
		if ( preg_match( '/^<pre[ ]?.*?>(.*?)<\/pre>/', $line ) ) {
            $code = preg_replace( '/^<pre[ ]?.*?>(.*?)<\/pre>/', '$1', $line);
			$new_line = '[code language="php"]' . $code . '[/code]';
            $this->in_pre_tab = false;
		} elseif ( preg_match( '/^<pre[ ]?.*?>(.*)/', $line ) ) {
			$code = preg_replace( '/^<pre[ ]?.*?>(.*)/', '$1', $line);
			$new_line = '[code language="php"]' . $code;
        } elseif ( preg_match( '/(.*?)<\/pre>/', $line ) ) {
            $code = preg_replace( '/(.*?)<\/pre>/', '$1', $line);
			$new_line = $code . "[/code]";
            $this->in_pre_tab = false;
        } else {
			$new_line = $line;
        }
        Result::get_result()->add( $new_line );
    }
}

/**
 * Breace line converter class.
 */
class HelpHubBraceConverter extends HelpHubConverter implements BraceConverter {
	private $in_lang_locator = false;

	/**
	 * Converts brace line.
	 *
	 * Note: Language locator at the top of contents is not required in
	 *       HelpHub. During $in_lang_locator is true, lines are ignored.
	 *
	 * @param string $line should be converted.
	 */
    public function convert( $line ) {
		if ( preg_match( "/^\{\{Versions\}\}[ ]*$/", $line ) ) {
			$new_line = '<p>See also: other <a href="https://codex.wordpress.org/WordPress_Versions">WordPress Versions</a></p>';
            Result::get_result()->add( $new_line );
			return;
        }

        if ( preg_match( "/^\{\{Languages\|/", $line ) ) {
            $this->in_lang_locator = true;
        }

        if ( $this->in_lang_locator ) {
			// Language locator is not required for HelpHub.
			// $line is not output even if {{Languages|
        } else {
            $new_line = $line;
            Result::get_result()->add( $new_line );
        }

        if ( preg_match( "/^\}\}/", $line ) ) {
            $this->in_lang_locator = false;
        }
    }
}

?>
