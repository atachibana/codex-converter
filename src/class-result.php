<?php
/*****************************************************************************
 * Wrapper class to keep the migrated result.
 *
 * by Akira Tachibana
 ****************************************************************************/
// require_once( 'log4php/Logger.php' );
require_once( 'class-logger.php' );

/**
 * Wrapper class to keep the migrated result.
 *
 * This class wrapps array $data for input / output with trace message.
 */
class Result {
    private $result_array = NULL;
    private $bottom_array = NULL;
    private $logger = NULL;

    /**
     *  private consturctor to refuse user's new()
     */
    private function __construct() {
        $this->result_array = array();
        $this->bottom_array = array();
        Logger::configure( 'log-config.xml' );
        $this->logger = Logger::getLogger( 'migrate' );
    }

    /**
     *  stattic method returns singleton object
     *
     *  @return Result sigleton object
     */
    public static function get_object () {
        static $object;
        if ( !isset ($object) ) {
            $object = new Result();
        }
        return $object;
    }

    /**
     * Sets log4php instance
     *
     * This setting is option. Even if without settings, this class
     * successfully runs.
     *
     * @param Logger $logger instance of log4php Logger class.
     */
    // public function set_logger( Logger $logger ) {
    //     $this->logger = $logger;
    // }

    /**
     * add to result array
     *
     * @param string $line migrated data should be returned.
     */
    public function add( $line ) {
        $this->logger->trace( __METHOD__ . " $line" );
        array_push( $this->result_array, $line );
    }

    public function add_bottom( $line ) {
        $this->logger->trace( __METHOD__ . " $line" );
        array_push( $this->bottom_array, $line );
    }

    /**
     * return whole data
     *
     * @return array whole migrated results
     */
    public function getall_and_clear() {
        $return_value = $this->result_array;
        $this->logger->trace( __METHOD__ . " " . implode( ",", $return_value ) );
        $this->logger->trace( __METHOD__ . " bottom_array=" . implode( ",", $this->bottom_array ) );
        if ( 0 < count($this->bottom_array) ) {
            $return_value = array_merge( $return_value, $this->bottom_array );
            $this->logger->trace( __METHOD__ . " " . implode( ",", $return_value ) );
        }
        $this->logger->trace( __METHOD__ . " " . implode( ",", $return_value ) );
        $this->result_array = array();
        $this->bottom_array = array();
        return $return_value;
    }
}

?>
