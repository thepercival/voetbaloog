<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 14-12-15
 * Time: 23:00
 */

class Apps_Helper_JsonOutput extends Zend_Controller_Action_Helper_Abstract
{
    public function direct( $arrData, $nCode, $sMessage )
    {
        $this->getResponse()->setHeader('Content-Type', ($this->getRequest()->getParam('callback') ? 'application/javascript' : 'application/json'), true);

        $aOutput = array(
            "data" => $arrData,
            "code" => $nCode,
            "message" => $sMessage
        );

        $sOutput = json_encode($aOutput, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        if ($this->getRequest()->getParam('callback')) {
            $sOutput = $this->getRequest()->getParam('callback') . "(" . $sOutput . ");";
        }
        $this->getResponse()->setBody($sOutput);
    }
}