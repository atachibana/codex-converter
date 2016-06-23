<?php
/*****************************************************************************
 * Migrator from Codex article to HelpHub, localized codex or others
 *
 * by Akira Tachibana
 ****************************************************************************/
// require_once( 'class-converter.php' );
require_once( 'class-result.php' );
// require_once( 'log4php/Logger.php' );
require_once( 'class-logger.php' );
require_once( 'helphub/class-helphub-converter.php');
require_once( 'jacodex/class-jacodex-converter.php');
/*
function my_autoloader( $class ) {
    require_once( strtolower( $class ) . '/class-' . strtolower( $class ) . '.php' );
}
spl_autoload_register('my_autoloader');
*/

/**
 * Converts Codex styled articles to HelpHub styled or localized resources
 *
 * Codex or wiki format (ex. '==My Article==' ) is migrated to other style.
 */
class CodexConverter {

    const TO_HELPHUB = 'HelpHub';
    const TO_JA_CODEX = 'JaCodex';

    private $logger = null;
    // private $type;
    private $hander = null;

    public function __construct ( $adapter_type ) {
        Logger::configure( 'log-config.xml' );
        $this->logger = Logger::getLogger( 'migrate' );
        // $this->type = $type;
        // $this->handler = new ConverterHandler( $adapter_type );
        $this->handler = new HelpHubConverterMgr ( $adapter_type );
        // $this->handler = new JaCodexConverterMgr ( $adapter_type );
    }

    /**
     * converts Codex format to HelpHub format or others
     *
     * migrates Codex or wiki format to HelpHub or WordPress/HTML format
     *
     * @param array $data_array Input data
     * @return array migrated data
     */
    public function convert( $data_array ) {
        $this->logger->info( __METHOD__ . " >>> input : " . implode( ",", $data_array ) );
        // $handler = new $this->type();
        // $handler = new ConverterHandler( $this->type );

        $converter = null;
        foreach( $data_array as $line ) {
            $type = $this->get_type( $line );

            if ( $converter ) {
                if ( ( $converter->keep_format() ) ||
                     ( $type == $converter->get_type() ) ) {
                         // NOP. Can use the same converter object.
                } else {
                    // $converter->close();
                    unset( $converter );
                    $converter = $this->handler->get_converter( $type );
                }
            } else {
                $converter = $this->handler->get_converter( $type );
            }

            $this->logger->trace( __METHOD__ . ' [' . $converter->get_type() . '] ' . $line );
            $converter->convert( $line );
        }

        if ( $converter ) {
            // $converter->close();
            unset( $converter );
        }

        $result_array = Result::get_object()->getall_and_clear();
        $this->logger->info( __METHOD__ . " <<< return : " . implode( ",", $result_array ) );
        return $result_array;
    }

    /**
     * Investigates line type from first column.
     *
     * @param string $line wiki format text
     * @return string migrator class name. Refer above consts.
     */
    private function get_type( $line ) {
        $patterns = array( "/^=.*/",
                           "/^\*.*/",
                           "/^#.*/",
                           "/^:.*/",
                           "/^;.*/",
                           "/^[ ]+(.+)$/",
                           "/^<pre[ >].*/",
                           "/^(\{\{|\}\}).*/" );
        $replaces = array( Converter::TYPE_TITLE,
                           Converter::TYPE_STAR,
                           Converter::TYPE_SHARP,
                           Converter::TYPE_COLON,
                           Converter::TYPE_SEMICOLON,
                           Converter::TYPE_SPACE,
                           Converter::TYPE_PRE,
                           Converter::TYPE_BRACE );
        $type = preg_replace( $patterns, $replaces, $line );
        if ( $type == $line ) {
            $type = Converter::TYPE_PLAIN;
        }
        return $type;
    }
}

?>
