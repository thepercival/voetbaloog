<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 2-7-16
 * Time: 11:17
 */

abstract class Voetbal_Extern_System_Abstract
{
    /**
     * @var int
     */
    private const MaxNrOfRetries = 2;
    /**
     * @var int
     */
    private const NrOfSecondsSleepBeforeRetry = 2;

	protected function getContentForUrl( string $sUrl, int $nrOfRetries = 0 ): string
	{
        $curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $sUrl );
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36');
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);

        $logger = ( defined('CURL_CUTOM_LOG') && CURL_CUTOM_LOG === true ) ? new Zend_Log( new Zend_Log_Writer_Stream(sys_get_temp_dir() . '/superelf/curl.log') ) : null;

        $cfgProxy = new Zend_Config_Ini( APPLICATION_PATH . '/configs/config.ini', 'import');
        if( $cfgProxy->get("proxy") !== null ) {
            curl_setopt($curl_handle,CURLOPT_PROXY,
                        $cfgProxy->get("proxy")->username . ":" .
                        $cfgProxy->get("proxy")->password . "@" .
                        $cfgProxy->get("proxy")->host . ":" .
                        $cfgProxy->get("proxy")->port );
            $logger->log("proxy used at " . date('Y-m-d H:i:s'), Zend_Log::INFO);
        }

		$sHtml = curl_exec($curl_handle);

        if ($sHtml === false || strlen($sHtml) === 0 ) {
            if( $nrOfRetries < self::MaxNrOfRetries ) {
                if( $logger ) {
                    $logger->log("sleeping and retrying " . $sUrl, Zend_Log::INFO);
                }
                sleep( self::NrOfSecondsSleepBeforeRetry );
                return $this->getContentForUrl( $sUrl, ++$nrOfRetries );
            }
            $sMessage = "curl: ".curl_error($curl_handle). "(". curl_errno($curl_handle).")";
            curl_close($curl_handle);
            if( $logger ) {
                $logger->log( $sMessage, Zend_Log::ERR );
            }
            throw new Exception( $sMessage, E_ERROR );
        } else if( $logger ) {
            $logger->log("received data size: " . $this->formatBytes(mb_strlen($sHtml)), Zend_Log::INFO);
            $logger->log( $sUrl, Zend_Log::INFO );
        }

		// Check if any error occurred
		if($logger && curl_errno( $curl_handle) > 0) {
			$info = curl_getinfo($curl_handle);
			$logger->log( $info["http_code"] . " : " . $sUrl, Zend_Log::WARN );
		}

		curl_close($curl_handle);
		return $sHtml;
	}

    protected function formatBytes($bytes, $precision = 2): string {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = (int)min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}