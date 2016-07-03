<?php
/**
 * Codex class.
 *
 * Main class of codex-converter.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */
require_once( 'class-result.php' );
require_once( 'interface-converter.php' );

/**
 * Only for a removal of a external library dependency, class-logger.php was
 * introduced. Use log4php, if it is possible. Refer the comment in
 * class-logger.php about usage.
 */
require_once( 'class-logger.php' );
// require_once( 'log4php/Logger.php' );

/**
 * Main class of class-codex.
 *
 * Instantiates ConverterManager and calls converter method per one line.
 */
class Codex {
    /**
	 * target name
	 *
	 * @var string TO_XXX indicates the target of conversion.
	 */
    const TO_HELPHUB = 'HelpHub';
    const TO_JACODEX = 'JaCodex';

    private $logger = null;
    private $converter_type = "";

    /**
     * Initializes ConverterManager and Logger
     *
     * @param string $converter_type of const TO_XXX.
     */
    public function __construct ( $converter_type ) {
        // NOTES. Delay load caused runtime error on PHPUnit reporting
        // functions.
        $include_file = strtolower( $converter_type ) . '/class-' . strtolower( $converter_type ) . '-converter.php';
        require_once( $include_file );
        $this->converter_type = $converter_type;

        // Dummy function call to keep the compatibility with log4php.
        // Read header comment of class-logger.php
        Logger::configure( dirname(__FILE__) . '/log-config.xml' );
        $this->logger = Logger::getLogger( 'codex-converter' );
    }

    /**
     * converts Codex format to specifid format
     *
     * For each line in array, selects Converter object depending on its line
     * type, calls convert, and returns gathered results as array.
     *
     * The previous line's Converter is re-used if
     * 1) Previous Converter says it should be kept (ex. in the <pre> tag), or
     * 2) Previous Converter's type and the current line type are the same
     *
     * @param array $data_array Input data
     * @return array migrated data
     */
    public function convert( $data_array ) {
        $this->logger->info( __METHOD__ . " >>> input : " . implode( ",", $data_array ) );

        $converter = null;
        foreach( $data_array as $line ) {
            $type = $this->get_type( $line );

            if ( $converter ) {
                // previous Converter says it should be kept or the previous
                // line type and current line type is the same
                if ( ( $converter->keep_format() ) ||
                     ( $type == $converter->get_type() ) ) {
                         // NOP. same Converter object is re-used.
                } else {
                    // explicitly calls destructor. Otherwise, the order of end
                    // tag and start tag must be wrong.
                    unset( $converter );
                    $converter = $this->get_converter( $type );
                }
            } else {
                // 1st line.
                $converter = $this->get_converter( $type );
            }

            $this->logger->trace( __METHOD__ . ' [' . $converter->get_type() . '] ' . $line );
            $converter->convert( $line );
            // converted result is stored in Result object.
        }

        // explicitly calls destuctore to enforce the end tag output.
        if ( $converter ) {
            unset( $converter );
        }

        $result_array = Result::get_result()->getall_and_clear();
        $this->logger->info( __METHOD__ . " <<< return : " . implode( ",", $result_array ) );
        return $result_array;
    }

    /**
     * Investigates line type from first column or pattern
     *
     * @param string $line wiki format text
     * @return string migrator class name. Converter::TYPE_XXX
     */
    private function get_type( $line ) {
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

        $patterns[] = '/^[ ]+(.+)$/';
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
     * @param string $type Converter::TYPE_XXX
     * @return Converter object
     */
    private function get_converter( $type ) {
		$classname = $this->converter_type . $type;
		return new $classname( $type );
	}
}

?>
