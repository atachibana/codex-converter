<?php
/**
 * Poor log4php.
 *
 * Only for a removal of external library dependency. You should use log4php
 * if it is possible. To use log4php
 * 1) replace require_once in class-codex.php
 * 2) rename sample-log-config.xml to log-config.xml
 *
 * It does not use log-config.xml. To change log level or logfile name,
 * change below const or variable.
 * Performance is terrible if you set LOGGERLEVEL_TRACE for long article.
 *
 * @link		https://github.com/atachibana/codex-converter/
 * @author		Akira Tachibana
 */

/**
 * poor log4php.
 *
 * Provides minimum const and interfaces.
 */
class Logger {

	/**
	 * minimum const.
	 *
	 * @var string log file name and log level.
	 */
	const LOGFILE = 'codex-converter.log';
	const LOGGERLEVEL_TRACE = 1;
	const LOGGERLEVEL_INFO  = 2;
	const LOGGERLEVEL_ERROR = 3;

	private $level = Logger::LOGGERLEVEL_INFO;
	private $name;

	/**
	 * dummy function.
	 *
	 * It just probides compatibility with log4php. Input parameter $configure
	 * is not used.
	 *
	 * @param string $configure config xml file (not used in this method)
	 */
	public static function configure( $configure ) {
		// NOP
	}

	/**
	 * Returns static Logger object.
	 *
	 * Instantiates static Logger object it is not yet, and returns.
	 *
	 * @param string $name name of logger.
	 */
    public static function getLogger( $name ) {
		static $logger;
		if ( !isset ( $logger ) ) {
			$logger = new Logger( $name );
		}
        return $logger;
    }

	/**
	 * Initialized log environment.
	 *
	 * @param string $name name of logger.
	 */
	public function __construct ( $name ) {
		$this->name = $name;
		error_reporting( E_ALL );
		ini_set( 'display_errors', 'Off' );
		ini_set( 'log_errors', 'On' );
		ini_set( 'error_log', Logger::LOGFILE );
	}

	/**
	 * Outputs most low level log.
	 *
	 * In codex-converter, trace() is used to show the each result of
	 * conversion.
	 *
	 * @param string $message log text.
	 */
    public function trace( $message ) {
		if ( Logger::LOGGERLEVEL_TRACE >= $this->level ) {
			error_log( $this->format( $message ), 3, Logger::LOGFILE );
		}
	}

	/**
	 * Outputs middle level log.
	 *
	 * In codex-converter, info() is used to show whole input and output text
	 * in arrays data.
	 *
	 * @param string $message log text.
	 */
    public function info( $message ) {
		if ( Logger::LOGGERLEVEL_INFO >= $this->level ) {
			error_log( $this->format( $message ) , 3, Logger::LOGFILE );
		}
	}

	/**
	 * Outputs critical level log.
	 *
	 * In codex-converter, error() is used to tell the runtime error
	 *
	 * @param string $message log text.
	 */
    public function error( $message ) {
		if ( Logger::LOGGERLEVEL_ERROR >= $this->level ) {
			error_log( $this->format( $message ), 3, Logger::LOGFILE );
		}
	}

	/**
	 * Adds timestamp and New Line.
	 *
	 * @param string $message log text.
	 * @return string formatted log text.
	 */
	private function format ( $message ) {
		return date( 'Y-m-d H:i:s' ) . " [$this->name] " . rtrim( $message ) . PHP_EOL;
	}
}

?>
