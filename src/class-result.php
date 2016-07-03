<?php
/**
 * Converted result keeper.
 *
 * Get Result object by Result::get_result() static method and call add() or
 * add_bottom() method.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

/**
 * Wrapper class to keep the converted result.
 *
 * Internally it has two array
 *   - result_array : usual converted line. Converter object appends its result
 *                    at the bottom of this array
 *   - bottom_array : Some text should be output at the end of contents. In
 *                    case of Japanese codex, language locator is in bottom.
 */
class Result {
    private $result_array = array();
    private $bottom_array = array();
    private $logger = NULL;

    /**
     * Private constructor to force factory method call.
     *
     * Initializes logger.
     */
    private function __construct() {
        $this->logger = Logger::getLogger( 'codex-converter' );
    }

    /**
     * Returns Result object.
     *
     * If not yet, instantiate the object and returns.
     *
     * @return Result object.
     */
    public static function get_result () {
        static $result;
        if ( !isset ( $result ) ) {
            $result = new Result();
        }
        return $result;
    }

    /**
     * Adds converted text to array.
     *
     * @param string $line converted text should be returned.
     */
    public function add( $line ) {
        $this->logger->trace( __METHOD__ . " $line" );
        array_push( $this->result_array, $line );
    }

    /**
     * Adds converted text should be bottom contents.
     *
     * @param string $line converted text should be returned as bottom
     *        contents.
     */
    public function add_bottom( $line ) {
        $this->logger->trace( __METHOD__ . " $line" );
        array_push( $this->bottom_array, $line );
    }

    /**
     * Returns whole data as array and clear the internal area.
     *
     * Two internal arrays are concatinated before its return.
     *
     * @return array whole converted results.
     */
    public function getall_and_clear() {
        $return_value = $this->result_array;
        if ( 0 < count($this->bottom_array) ) {
            $return_value = array_merge( $return_value, $this->bottom_array );
        }
        $this->logger->trace( __METHOD__ . ' ' . implode( ',', $return_value ) );
        $this->result_array = array();
        $this->bottom_array = array();
        return $return_value;
    }
}

?>
