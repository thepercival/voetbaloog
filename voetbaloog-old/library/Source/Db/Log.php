<?php

/**
 * @copyright  2007 Coen Dunnink
 * @license    http://www.gnu.org/licenses/gpl.txt
 * @version    $Id: Log.php 4554 2019-08-12 14:37:34Z thepercival $
 *
 * @package    Source
 */

/**
 * @package Source
 */
class Source_Db_Log
{
    protected function __construct()
    {
    }


	public static function tail( $filename, $sSearch, $lines = 50, $buffer = 4096 )
	{
	    // Open the file
	    $f = fopen($filename, "rb");
		if ( !is_resource($f) ) {
			return 'could not open file `' . basename($filename) . '`';
		}

	    // Jump to last character
	    fseek($f, -1, SEEK_END);

	    // Read it and adjust line number if necessary
	    // (Otherwise the result would be wrong if file doesn't end with a blank line)
	    if(fread($f, 1) != "\n") $lines -= 1;

	    // Start reading
	    $output = '';
	    $chunk = '';

	    // While we would like more
	    while( ftell($f) > 0 and $lines >= 0)
	    {
	        // Figure out how far back we should jump
	        $seek = min(ftell($f), $buffer);

	        // Do the jump (backwards, relative to where we are)
	        fseek($f, -$seek, SEEK_CUR);

	        // Read a chunk and prepend it to our output
	        $output = ($chunk = fread($f, $seek)).$output;

	        // Jump back to where we started reading
	        fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

	        // Decrease our line counter
	        $lines -= substr_count($chunk, "\n");
	    }

	    fclose($f);

	    // While we have too many lines
	    // (Because of buffer size we might have read too many)
	    while($lines++ < 0)
	    {
	        // Find first newline and remove all text before that
	        $output = substr($output, strpos($output, "\n") + 1);
	    }

	    $nStartPos = strpos ( $output , $sSearch );
	    if ( $nStartPos !== false )
	    {
	    	$nLength = 200;
	    	$nEndPos = strpos ( $output , "\n", $nStartPos );
	    	if ( $nEndPos !== false )
	    		$nLength = $nEndPos - $nStartPos;
	    	return substr( $output, $nStartPos, $nLength );
	    }

	    return "no error message found!";
	}
}