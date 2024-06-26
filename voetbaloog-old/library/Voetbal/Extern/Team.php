<?php

/**
 * @package Voetbal
 */
class Voetbal_Extern_Team extends Patterns_Idable_Object
{
    /**
     * @var string
     */
    protected $m_sName;

    public function __construct( string $vtId )
    {
        parent::__construct();
        $this->putId( $vtId );
    }

    public function getName(): ?string
    {
        return $this->m_sName;
    }

    public function putName( string $sName )
    {
        $this->m_sName = $sName;
    }
}
