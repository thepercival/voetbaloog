<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Email.php 4558 2019-08-13 08:54:29Z thepercival $
 *
 * @package    Email
 */

/**
 * @package Email
 */
class RAD_Email // implements Email_Interface
{
	public function __construct()
	{
	}

	public static function send( $vtMailAddress, $szSubject, $szMessage, $szRecType = null, $vtAttachments = null )
	{
		return self::sendHelper( $vtMailAddress, $szSubject, $szMessage, $szRecType, $vtAttachments, false );
	}

	public static function sendHtml( $vtMailAddress, $szSubject, $szMessage, $szRecType = null, $vtAttachments = null )
	{
		return self::sendHelper( $vtMailAddress, $szSubject, $szMessage, $szRecType, $vtAttachments, true );
	}

    protected static function sendHelper( $vtMailAddress, $szSubject, $szMessage, $szRecType, $vtAttachments, $bHtml )
    {

        if ( is_array( $vtMailAddress ) )
        {
            $vtMailAddress = implode( ",", $vtMailAddress );
        }

        /*if ( is_string( $vtAttachments ) )
            $vtAttachments = explode( ",", $vtAttachments );
        if ( is_array( $vtAttachments ) )
        {
            foreach ( $vtAttachments as $szAttachment )
            {
                if ( !is_string( $szAttachment ) )
                    continue;

                $mail->addAttachment( trim( $szAttachment ) );
            }
        }*/


        /*if ( $bHtml !== true )
            $mail->AltBody = $szMessage;
        else {
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Body = $szMessage;
        }*/

        $cfgEmail = new Zend_Config_Ini(APPLICATION_PATH . "/configs/config.ini", "email");

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: ".$cfgEmail->get("fromname")." <".$cfgEmail->get("from").">" . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        $params = "-r ".$cfgEmail->get("from");

        if ( !mail( $vtMailAddress, $szSubject, $szMessage, $headers, $params) ) {
            throw new Exception( "EMAIL ERROR", E_ERROR );
        }
        return true;
    }
}