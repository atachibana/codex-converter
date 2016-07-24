<?php
/**
 * Utility class of codex-converter
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

class Util {

/**
 * Investigates line type from first column or pattern
 *
 * @param string $line wiki format text
 * @return string migrator class name. Converter::TYPE_XXX
 */
public static function get_type( $line ) {
    $patterns[] = '/^=.*/';
    $replaces[] = Converter::TYPE_TITLE;

    $patterns[] = '/^\*.*/';
    $replaces[] = Converter::TYPE_STAR;

    $patterns[] = '/^#.*/';
    $replaces[] = Converter::TYPE_SHARP;

    $patterns[] = '/^:.*/';
    $replaces[] = Converter::TYPE_COLON;

    $patterns[] = '/^;.*/';
    $replaces[] = Converter::TYPE_SEMICOLON;

    $patterns[] = '/^[ ]+[^ ]+.*$/';
    $replaces[] = Converter::TYPE_SPACE;

    $patterns[] = '/^<pre[ >].*/';
    $replaces[] = Converter::TYPE_PRE;

    $patterns[] = '/^(\{\{|\}\}).*/';
    $replaces[] = Converter::TYPE_BRACE;

    $type = preg_replace( $patterns, $replaces, $line, -1, $count );
    if ( 0 == $count ) {
        $type = Converter::TYPE_PLAIN;
    }
    return $type;
}

/**
 * Instantiates Converter object.
 *
 * Assumes already class php file was loaded.
 *
 * @param string $type Converter::TYPE_XXX
 * @return Converter object
 */
public static function get_converter( $converter_type, $type ) {
    $classname = $converter_type . $type;
    return new $classname( $type );
}

}

?>
