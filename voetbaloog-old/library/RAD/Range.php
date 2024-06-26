<?php

/**
 * @package RAD
 */
class RAD_Range
{
    /**
     * @var int
     */
    protected $m_nStart;
    /**
     * @var int
     */
    protected $m_nEnd;

    public function __construct( int $nStart, int $nEnd ){
        $this->m_nStart = $nStart;
        $this->m_nEnd = $nEnd;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return $this->m_nStart;
    }

    /**
     * @return int
     */
    public function getEnd(): int
    {
        return $this->m_nEnd;
    }
}