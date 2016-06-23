<?php
/*****************************************************************************
 * poor log4php. If you can, you should use Apache's log4php instead of this.
 *
 * by Akira Tachibana
 ****************************************************************************/

/**
 * poor log4php
 *
 * to be independent from other library. If you can, please replace this
 * class by Apache's log4php.
 */
class Logger {
	const LOGFILE = 'migrator.log';
	const LOGGERLEVEL_TRACE = 1;
	const LOGGERLEVEL_INFO  = 2;
	const LOGGERLEVEL_ERROR = 3;

	private $level = Logger::LOGGERLEVEL_INFO;
	private $name;

	public static function configure( $configure ) {
		// NOP - to keep compatibility with log4php.
	}

    public static function getLogger( $name ) {
		static $logger = null;
		if ( $logger == null ) {
			$logger = new Logger( $name );
		}
        return $logger;
    }

	public function __construct ( $name ) {
		$this->name = $name;
		error_reporting( E_ALL );
		ini_set( 'display_errors', 'Off' );
		ini_set( 'log_errors', 'On' );
		ini_set( 'error_log', Logger::LOGFILE );
	}

    public function trace($message) {
		if ( Logger::LOGGERLEVEL_TRACE >= $this->level ) {
			error_log( $this->format( $message ), 3, Logger::LOGFILE );
		}
	}

    public function info($message) {
		if ( Logger::LOGGERLEVEL_INFO >= $this->level ) {
			error_log( $this->format( $message ) , 3, Logger::LOGFILE );
		}
	}

    public function error($message) {
		if ( Logger::LOGGERLEVEL_ERROR >= $this->level ) {
			error_log( $this->format( $message ), 3, Logger::LOGFILE );
		}
	}

	private function format ( $message ) {
		return date( "Y-m-d H:i:s" ) . " [$this->name] " . rtrim( $message ) . PHP_EOL;
	}
}

?>
