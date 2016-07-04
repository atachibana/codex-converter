<?php
/**
 * Japanese Codex Converter classes.
 *
 * Defines rule of Codex to Japanese Codex conversion.
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
abstract class JaCodexConverter implements Converter {

	private $type;

	/**
	 * constructor.
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
	 * Indicates the same line type should be kept.
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
	 * In general, just change some words and store Result object.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
		$new_line = $this->word_convert( $line );
		Result::get_result()->add( $new_line );
	}

	/**
	 * Converts Codex or Media Wiki word format.
	 *
	 * @param string $line should be converted.
	 * @return string converted text.
	 */
	protected function word_convert( $line ) {
		$patterns[] = '/\[\[Function[ _]Reference\//';
		$replaces[] = '[[関数リファレンス/';

		$patterns[] = '/\[\[Category\:(.*?)\]\]/';
		$replaces[] = 'Category:$1';

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

/**
 * Plain text converter class.
 */
class JaCodexPlainConverter extends JaCodexConverter implements PlainConverter {

	/**
	 * Converts Plain text.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
		parent::convert( $line );
    }
}

/**
 * Title line converter class.
 */
class JaCodexTitleConverter extends JaCodexConverter implements TitleConverter {

	/**
	 * Converts Title line.
	 *
	 * Original English title must be remain because it is used as link target.
	 * It is set as <span id= )
	 *
	 * First it trys major title conversion. If failed, then general
	 * conversion with span id.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
		$patterns[] = '/==[ ]*Description[ ]*==/';
		$replaces[] = '== 説明<span id="Description"></span> ==';

		$patterns[] = '/==[ ]*Usage[ ]*==/';
		$replaces[] = '== 使い方<!--Usage--> ==';

		$patterns[] = '/==[ ]*Return Values[ ]*==/';
		$replaces[] = '== 戻り値<!--Return Values--> ==';

		$patterns[] = '/==[ ]*Examples[ ]*==/';
		$replaces[] = '== 用例<!--Examples--> ==';

		$patterns[] = '/==[ ]*Notes[ ]*==/';
		$replaces[] = '== 注<!--Notes--> ==';

		$patterns[] = '/==[ ]*Change Log[ ]*==/';
		$replaces[] = '== 変更履歴<!--Change Log--> ==';

		$patterns[] = '/==[ ]*Source File[ ]*==/';
		$replaces[] = '== ソースファイル<!--Source File--> ==';

		$patterns[] = '/==[ ]*Related[ ]*==/';
		$replaces[] = '== 関連<!--Related--> ==';

		$patterns[] = '/==[ ]*Parameters[ ]*==/';
		$replaces[] = '== パラメータ<!--Parameters--> ==';

		// If we could match in above rules, then return.
		$new_line = preg_replace( $patterns, $replaces, $line, -1, $count );
		if ( 0 < $count ) {
			Result::get_result()->add( $new_line );
			return;
		}

		// If not, then copy title to <span id=
        $patterns = array( '/^(=+)[ ]*(.*?)[ ]*(=+)/');
		$replaces = array( '$1' );
        $title_tag = preg_replace( $patterns, $replaces, $line );
		$replaces = array( '$2' );
        $title = preg_replace( $patterns, $replaces, $line );
		$tagid = preg_replace( '/ /', '_', $title );
		$new_line = "$title_tag $title <span id=\"$tagid\"></span> $title_tag";

        Result::get_result()->add( $new_line );
    }
}

/**
 * Star line converter class.
 */
class JaCodexStarConverter extends JaCodexConverter implements StarConverter {

    /**
	 * Converts Star line.
	 *
	 * @param string $line should be converted.
	 */
    public function convert( $line ) {
        parent::convert( $line );
    }
}

/**
 * Sharp line converter class.
 */
class JaCodexSharpConverter extends JaCodexConverter implements SharpConverter {

    /**
	 * Converts Sharp line.
	 *
	 * @param string $line should be converted.
	 */
    public function convert( $line ) {
        parent::convert( $line );
    }
}

/**
 * Colon line converter class.
 */
class JaCodexColonConverter extends JaCodexConverter implements ColonConverter {

    /**
	 * Converts Colon line.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        parent::convert( $line );
    }
}

/**
 * Semicolon line converter class.
 */
class JaCodexSemicolonConverter extends JaCodexConverter implements SemicolonConverter {

    /**
	 * Converts Semicolon line.
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        parent::convert( $line );
    }
}

/**
 * Spase line converter class.
 */
class JaCodexSpaceConverter extends JaCodexConverter implements SpaceConverter {

    /**
	 * Converts space line.
	 *
	 * Line starts with space character is converted to <pre> tag
	 *
	 * @param string $line should be converted.
	 */
	public function convert( $line ) {
        $patterns = array( '/^[ ]+(.+)$/' );
        $replaces = array( '<pre>$1</pre>' );
        $new_line = preg_replace( $patterns, $replaces, $line );
        Result::get_result()->add( $new_line );
    }
}

/**
 * Pre line converter class.
 */
class JaCodexPreConverter extends JaCodexConverter implements PreConverter {
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
	 * @param string $line should be converted.
     */
    public function convert( $line ) {
		if ( preg_match( '/<\/pre>/', $line ) ) {
			$this->in_pre_tab = false;
		}
        Result::get_result()->add( $line );
    }
}

/**
 * Breace line converter class.
 */
class JaCodexBraceConverter extends JaCodexConverter implements BraceConverter {
	private $in_lang_locator = false;

    /**
	 * Converts brace line.
	 *
	 * Note: Language locator at the top of contents is changed the style
	 *       and moved to bottom.
	 *
	 * @param string $line should be converted.
	 */
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
				// language selector is moved to the bottom of the contents.
				Result::get_result()->add_bottom( $new_line );
	        } else {
                // Usual {{xxx}}
	            $new_line = $line;
	            Result::get_result()->add( $new_line );
	        }
        }
    }
}

?>
