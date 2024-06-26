<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: CompetitionSeason.php 1199 2019-08-13 11:22:19Z thepercival $
 * @link		http://www.voetbaloog.nl/
 * @since		File available since Release 1.0
 * @package		VoetbalOog
 */

/**
 * @package VoetbalOog
 */
class VoetbalOog_Pdf_Document_CompetitionSeason extends Zend_Pdf
{
	protected $m_oCompetitionSeason;	// Voetbal_CompetitionSeason

	public function __construct( $oCompetitionSeason )
	{
		parent::__construct();
		$this->m_oCompetitionSeason = $oCompetitionSeason;
	}

	public function render( $newSegmentOnly = false, $outputStream = null )
	{
		$this->fillContents();
		// die();
		return parent::render( $newSegmentOnly, $outputStream );
	}

	public function getCompetitionSeason()
	{
		return $this->m_oCompetitionSeason;
	}

	protected function fillContents()
	{
		$oPage = new VoetbalOog_Pdf_Page_CompetitionSeason( 595 * 2, 842 /* A3 */ );

		$oFont = VoetbalOog_Pdf_Factory::getFont();
		$oPage->setFont( $oFont, 12 );
		$oPage->putParent( $this );

		$this->pages[] = $oPage;

		$oPage->draw();
	}
}