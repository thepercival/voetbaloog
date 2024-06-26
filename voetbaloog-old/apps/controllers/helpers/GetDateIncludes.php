<?php

/**
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_GetDateIncludes extends Zend_Controller_Action_Helper_Abstract
{
	public function direct( $sType, $bLazyStyle = true )
	{
		$sIncludes = "";
		if ( $bLazyStyle === true ) {
			if ($sType === "js") {

				$sIncludes .= "js.push( \"" . Zend_Registry::get("baseurl") . "jsthirdparties/eternicode-bootstrap-datepicker/js/bootstrap-datepicker.js\" );";
				$sIncludes .= "js.push( \"" . Zend_Registry::get("baseurl") . "jsthirdparties/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.nl.js\" );";
			} else if ($sType === "css") {
				$sIncludes = "css.push( \"" . Zend_Registry::get("baseurl") . "jsthirdparties/eternicode-bootstrap-datepicker/css/datepicker3.css\" );";
			}
		}
		else {
			if( $sType === "js" ) {
				$sIncludes = "
					<script src=\"".Zend_Registry::get("baseurl")."jsthirdparties/eternicode-bootstrap-datepicker/js/bootstrap-datepicker.js\" ></script>
					<script src=\"".Zend_Registry::get("baseurl")."jsthirdparties/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.nl.js\" ></script>
				";
			}
			else if( $sType === "css" ) {
				$sIncludes = "
					<link rel=\"stylesheet\" type=\"text/css\" href=\"".Zend_Registry::get("baseurl")."jsthirdparties/eternicode-bootstrap-datepicker/css/datepicker3.css\">
				";
			}
		}
		return $sIncludes;
	}
}
?>