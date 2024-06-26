<?php

/**
 * @copyright	2007 Coen Dunnink
 * @license		http://www.gnu.org/licenses/gpl.txt
 * @version		$Id: Membership.php 776 2014-03-05 08:37:12Z thepercival $
 * @package		Voetbal
 */

/**
 * @package Voetbal
 */
class Voetbal_Team_Line implements JsonSerializable, Patterns_Idable_Interface
{
	use Patterns_Idable_Trait;

	CONST KEEPER = 1;
	CONST DEFENSE = 2;
	CONST MIDFIELD = 4;
	CONST ATTACK = 8;

	public function jsonSerialize()
	{
		return (string) $this;
	}

	public function __toString()
	{
		if ( $this->getId() == static::KEEPER )
			return "keeper";
		else if ( $this->getId() == static::DEFENSE )
			return "verdediging";
		else if ( $this->getId() == static::MIDFIELD )
			return "middenveld";
		else if ( $this->getId() == static::ATTACK )
			return "aanval";
	}

	public function getName()
	{
		return (string) $this;
	}

	public static function getAbb( int $nLine ): string {
        if ( $nLine == static::KEEPER )
            return "K";
        else if ( $nLine == static::DEFENSE )
            return "V";
        else if ( $nLine == static::MIDFIELD )
            return "M";
        // else if ( $nLine == static::ATTACK )
        return "A";
    }
}