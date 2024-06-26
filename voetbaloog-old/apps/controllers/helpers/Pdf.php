<?php

/**
 * Action Helper for loading pdfs
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Apps_Helper_Pdf extends Zend_Controller_Action_Helper_Abstract
{

	public function direct( $vtPdf, $sPdfFileName, $sDisposition )
	{
		$oActionController = $this->getActionController();
		$oActionController->getResponse()->clearBody();
		$oActionController->getHelper("viewRenderer")->setNoRender();
		if ( $vtPdf instanceof Zend_Pdf )
			$vtPdf = $vtPdf->render();
		header("Cache-Control: must-revalidate");
		header("Pragma: public");
		header("Content-Type: application/pdf");
		header("Content-Disposition: " . $sDisposition . "; filename=\"" . $sPdfFileName . "\";");
		header("Content-Length: " . strlen( $vtPdf ) );
		exit( $vtPdf );
	}

}
