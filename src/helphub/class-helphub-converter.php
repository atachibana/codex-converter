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

		$patterns[] = "/\[\[Function[ _]Reference\/(.*?)\|(.*?)\]\]/";
		$replaces[] = '<a href="https://developer.wordpress.org/reference/functions/$1">$2</a>';

		$patterns[] = "/\[\[Category\:(.*?)\]\]/";
		$replaces[] = 'Category:$1';

		$patterns[] = "/\[\[Image\:(.*?)\]\]/";
		$replaces[] = '<br /><strong>*** [TODO] Embed Image HERE !!! ***: $1 </strong><br />';

 		$patterns[] = "/\[http(.*?) (.*?)\]/";
		$replaces[] = '<a href="http$1">$2</a>';

		$patterns[] = "/\[http(.*?)\]/";
		$replaces[] = '<a href="http$1">http$1</a>';

		$new_line = preg_replace( $patterns, $replaces, $line );

		$pattern = "/\[\[#(.*?)\|(.*?)\]\]/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<a href=\"#" . $this->convert_text_to_id( $matches[1] ) . "\">" . $matches[2] . "</a>";
						},
						$new_line );

		$pattern = "/\[\[(.*?)\|(.*?)\]\]/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<a href=\"https://codex.wordpress.org/" . $this->convert_text_to_id( $matches[1] ) . "\">" . $matches[2] . "</a>";
						},
						$new_line );

		$pattern = "/\[\[(.*?)\]\]/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<a href=\"https://codex.wordpress.org/" . $this->convert_text_to_id( $matches[1] ) . "\">" . $matches[1] . "</a>";
						},
						$new_line );

		$patterns = "/<tt><a href(.*?)>(.*?)<\/a>[ ]*<\/tt>/";
		$new_line = preg_replace_callback( $patterns,
						function( $matches ) {
							return "<a href" . $matches[1] . "><code>" . preg_replace( "/</", "&lt;", $matches[2] ) . "</code></a>";
						},
						$new_line );

 		$pattern = array( "/<tt><nowiki>(.*?)<\/nowiki><\/tt>/", "/<tt>(.*?)<\/tt>/", "/<nowiki>(.*?)<\/nowiki>/" );
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<code>" . preg_replace( "/</", "&lt;", $matches[1] ) . "</code>" ;
						},
						$new_line );

		return $new_line;
    }

	/**
	 * Converts text to id
	 *
	 * Strips off HTML tag, Replace space by underscore, Replace special
	 * symbols by character references.
	 *
	 * @param string $line should be converted.
	 */
	public function convert_text_to_id( $line ) {
		$new_line = preg_replace( "/ /", "_", $line );
		$new_line = htmlspecialchars( strip_tags( $new_line ) );
		return $new_line;
	}
}

/**
 * Plain text converter class.
 */
class HelpHubPlainConverter extends HelpHubConverter implements PlainConverter {

	/**
	 * Determines whether this line can be ignored.
	 *
	 * Some lines are not required to convert to HelpHub.
	 *   __TOC__
	 *   __NOTOC__
	 *
	 * @param string $line should be converted.
	 * @return boolean true if it is not required output
	 */
	private function ignored_line( $line ) {
		$pattern = '/^(__TOC__|__NOTOC__).*/';
		return preg_match( $pattern, $line );
	}

	/**
	 * Converts Plain text.
	 *
	 * Encloses by <p> tag.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
		if ( $this->ignored_line( $line ) ) {
			return;
		}
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
		$new_line = $line;

		$pattern = "/^======[ ]*(.*?)[ ]*======/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<h6>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h6>";
						},
						$new_line );

		$pattern = "/^=====[ ]*(.*?)[ ]*=====/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<h5>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h5>";
						},
						$new_line );

		$pattern = "/^====[ ]*(.*?)[ ]*====/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<h4>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h4>";
						},
						$new_line );

		$pattern = "/^===[ ]*(.*?)[ ]*===/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<h3>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h3>";
						},
						$new_line );

		$pattern = "/^==[ ]*(.*?)[ ]*==/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							// return "<h2>$matches[1]<span id=\"" . preg_replace( "/ /", "_", $matches[1] ) . "\"></span></h2>";
							return "<h2>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h2>";
						},
						$new_line );

		$pattern = "/^=[ ]*(.*?)[ ]*=/";
		$new_line = preg_replace_callback( $pattern,
						function( $matches ) {
							return "<h1>$matches[1]<span id=\"" . $this->convert_text_to_id( $matches[1] ) . "\"></span></h1>";
						},
						$new_line );

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
 * Languages template conversion helper class
 *
 * Languages template is converted to short code "codex_languages".
 */
class LanguagesTemplate {
	private $result = "[codex_languages";

	/**
	 * Adds converted language locator.
	 *
	 * New locator has "_codex" prefix except "en". This is because in the
	 * future after I18N completed, we want to use simple locator such as "ja".
	 *
	 * Shortcode cannot handle hyphnen character well. So, in this pattern,
	 * pt-br, zh-ch, zh-tw are converted to ptbr, zhcn, zhtw.
	 *
	 * @param string $str language locator and page URL separated by "|"
	 *                    ex) {{ja|Version 4.6}}
	 */
	public function add( $str ) {
		$patterns[] = '/^\{\{[ ]*?en[ ]*?\|(.*?)\}\}.*/';
		$replaces[] = ' en="$1"';

		$patterns[] = '/^\{\{[ ]*?pt\-br[ ]*?\|(.*?)\}\}.*/';
		$replaces[] = ' ptbr_codex="$1"';
		$patterns[] = '/^\{\{[ ]*?zh\-cn[ ]*?\|(.*?)\}\}.*/';
		$replaces[] = ' zhcn_codex="$1"';
		$patterns[] = '/^\{\{[ ]*?zh\-tw[ ]*?\|(.*?)\}\}.*/';
		$replaces[] = ' zhtw_codex="$1"';

		$patterns[] = '/^\{\{(.*?)\|(.*?)\}\}.*/';
		$replaces[] = ' $1_codex="$2"';

		$new_str = preg_replace( $patterns, $replaces, $str );
		// Even if it does not match, it will be output. Otherwise, we will
		// lose the original information.
		$this->result .= $new_str;
	}

	/**
	 * Returns converted language locator
	 *
	 * @return $string converted result into shortcode
	 *                 ex) [codex_languages en="Version 4.6" ja="Version 4.6"]
	 */
	public function get_all() {
		return $this->result . "]";
	}
}

/**
 * Breace line converter class.
 */
class HelpHubBraceConverter extends HelpHubConverter implements BraceConverter {
	private $lang_obj = null;

	/**
	 * Converts brace line.
	 *
	 * Note: Language locator at the top of contents is converted to short code
	 *       [[codex_languages]]. Refer sample-functions.php for detail.
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
			$this->lang_obj = new LanguagesTemplate();
			return;
		}

		if ( preg_match( "/^\}\}/", $line ) ) {
			$result = $line;
			if ( $this->lang_obj ) {
				$result = $this->lang_obj->get_all();
				$this->lang_obj = null;
			}
			Result::get_result()->add( $result );
			return;
        }

		if ( $this->lang_obj ) {
			$this->lang_obj->add( $line );
		} else {
			Result::get_result()->add( $line );
		}
    }
}

?>
