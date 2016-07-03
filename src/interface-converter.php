<?php
/**
 * inteface of each converter class.
 *
 * Each concrete converter should implement these functions. Each interface
 * matches to each line type.
 * For example, if the line stats with "#", SharpConverter is instanticated
 * and used to convert to "<li>" in case of HelpHub converter.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

/**
 * Base interface of typed interface.
 *
 * Defines common constants and methods. In concrete object, defines
 * common conversion rule can be shared among child classes.
 */
interface Converter {

	/**
	 * line type and class name indicator constants.
	 *
	 * @var string TYPE_XXX type indicator constants. It is class name.
	 */
	const TYPE_PLAIN     = 'PlainConverter';
    const TYPE_TITLE     = 'TitleConverter';        // == Title ==
    const TYPE_STAR      = 'StarConverter';         // *bullet list
    const TYPE_SHARP     = 'SharpConverter';        // #number list
    const TYPE_PRE       = 'PreConverter';          // <pre>...</pre>
    const TYPE_COLON     = 'ColonConverter';        // ;sub-title:description
    const TYPE_SEMICOLON = 'SemicolonConverter';    // ;sub-title:description
    const TYPE_SPACE     = 'SpaceConverter';        //   x = 10;
    const TYPE_BRACE     = 'BraceConverter';        // {{ or }}

	/**
	 * Returns its line type.
	 *
	 * @return string above TYPE_XXX constant.
	 */
	public function get_type();

	/**
	 * Indicates whether it should keep the same line type.
	 *
	 * In Codex article, 1st column character is used to indicate the line
	 * type. But in some tag, for example <pre> or {{, the same line type
	 * should be keeping until the end of tag, for example </pre> or }}.
	 * This method returns true when we shold keep the same type.
	 *
	 * @return boolean true when it should keep the same line type.
	 */
	public function keep_format();

	/**
	 * Converts input string and stores to Result object.
	 *
	 * This method does not return converted string, but stores them into
	 * Result object.
	 *
	 * @param string $line input data should be converted.
	 */
	public function convert( $line );
}

/**
 * Plain text converter interface.
 */
interface PlainConverter     extends Converter {}

/**
 * Title line converter interface.
 *
 * The line begins with "=".
 */
interface TitleConverter     extends Converter {}

/**
 * Star line converter interface.
 *
 * The line begins with "*" and means list.
 */
interface StarConverter      extends Converter {}

/**
 * Sharp line converter interface.
 *
 * The line begins with "#" and means numbered list
 */
interface SharpConverter     extends Converter {}

/**
 * Pre line converter interface.
 *
 * The line of <pre>.
 */
interface PreConverter       extends Converter {}

/**
 * Colon line converter interface.
 *
 * The line begins with ":" and means indented.
 */
interface ColonConverter     extends Converter {}

/**
 * Semi-Colon line converter interface.
 *
 * The line begins with ";" and means <strong> title.
 */
interface SemicolonConverter extends Converter {}

/**
 * Space line converter interface.
 *
 * The line begins with " " and means <pre>.
 */
interface SpaceConverter     extends Converter {}

/**
 * Brace line converter interface
 *
 * The line begins with "{{" and means Category or Language locator.
 */
interface BraceConverter     extends Converter {}

?>
