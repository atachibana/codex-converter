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
require_once( 'class-util.php' );

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
     * If input type is string, split by "\n" and passed to the main logic.
     * Return value is also backed to string separated by "\n". Pre-processor
     * of Codex Template enhancement is supported only for string input type.
     *
     * @param array or string $data Input data
     * @return array or string of migrated data
     */
    public function convert( $data ) {
        $data_array = $this->input_preprocess( $data );
        $this->logger->info( __METHOD__ . " >>> input : " . implode( ",", $data_array ) );

        $converter = null;
        foreach( $data_array as $line ) {
            $type = Util::get_type( $line );

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
                    $converter = Util::get_converter( $this->converter_type, $type );
                }
            } else {
                // 1st line.
                $converter = Util::get_converter( $this->converter_type, $type );
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

        if ( is_array ($data) ) {
            return $result_array;
        } else {
            return implode( "\n", $result_array );
        }
    }

    /**
     * Converts input data to array
     *
     * From input data, converts line end to "\n" and converts to array.
     *
     * @param string|array $data input data
     * @return array data
     */
    private function input_preprocess( $data ) {
        $data_array = null;
        if ( is_array( $data ) ) {
            $data_array = $data;
        } else {
            // if magic_quotes is on, double quotation and some characters are
            // automatically escaped. Strip them.
            if ( get_magic_quotes_gpc() ) {
                $data = stripslashes( $data );
            }
            $cr = array( "\r\n", "\r" );
            $data = str_replace( $cr, "\n", $data );
            $data_array = explode( "\n", $data );
        }
        return $data_array;
    }
}
?>
