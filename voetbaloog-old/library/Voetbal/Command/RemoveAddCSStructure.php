<?php
/**
 * Created by PhpStorm.
 * User: coen
 * Date: 30-11-15
 * Time: 16:36
 */

class Voetbal_Command_RemoveAddCSStructure extends Voetbal_Command
{
    private $m_oCompetitionSeason;
    private $m_arrCSStructure;

    private $m_oBus;

    public function __construct( $oCompetitionSeason, $arrCSStructure )
    {
        $this->m_oCompetitionSeason = $oCompetitionSeason;
        $this->m_arrCSStructure = $arrCSStructure;
    }

    public function getCompetitionSeason(){ return $this->m_oCompetitionSeason; }
    /**
        "rounds": [
            {
                "number": 0,
                "poules": [
                    {
                        "number": 0,
                        "places": [
                            {
                                "number": 0,
                                "fromqualifyrule": null,
                                "toqualifyrule": {
                                    "frompouleplaces": [
                                        "object:20",
                                        "object:28"
                                    ],
                                    "topouleplaces": [
                                        "object:50"
                                    ],
                                    "confignr": 0
                                },
                                "poule": "number : 0",
                                "$$hashKey": "object:20"
                            },
                            {
                                "number": 1,
                                "fromqualifyrule": null,
                                "toqualifyrule": null,
                                "poule": "number : 0",
                                "$$hashKey": "object:21"
                            },
                            {
                                "number": 2,
                                "fromqualifyrule": null,
                                "toqualifyrule": null,
                                "poule": "number : 0",
                                "$$hashKey": "object:22"
                            }
                        ],
                        "round": "number : 0",
                        "$$hashKey": "object:14"
                    },
                    {
                        "number": 1,
                        "places": [
                            {
                                "number": 0,
                                "fromqualifyrule": null,
                                "toqualifyrule": {
                                "frompouleplaces": [
                                    "object:20",
                                    "object:28"
                                ],
                                    "topouleplaces": [
                                    "object:50"
                                ],
                                    "confignr": 0
                                },
                                "poule": "number : 1",
                                "$$hashKey": "object:28"
                            },
                            {
                                "number": 1,
                                "fromqualifyrule": null,
                                "toqualifyrule": null,
                                "poule": "number : 1",
                                "$$hashKey": "object:29"
                            },
                            {
                                "number": 2,
                                "fromqualifyrule": null,
                                "toqualifyrule": null,
                                "poule": "number : 1",
                                "$$hashKey": "object:30"
                            }
                        ],
                        "round": "number : 0",
                        "$$hashKey": "object:15"
                    }
                ],
                "type": 1,
                "fromqualifyrules": [],
                "toqualifyrules": [
                    {
                        "frompouleplaces": [
                        "object:20",
                        "object:28"
                    ],
                        "topouleplaces": [
                        "object:50"
                    ],
                        "confignr": 0
                    }
                ],
                "$$hashKey": "object:3"
            },
            {
                "number": 1,
                "poules": [
                    {
                        "number": 0,
                        "places": [
                            {
                                "number": 0,
                                "fromqualifyrule": {
                                "frompouleplaces": [
                                    "object:20",
                                    "object:28"
                                ],
                                    "topouleplaces": [
                                    "object:50"
                                ],
                                    "confignr": 0
                                },
                                "toqualifyrule": null,
                                "poule": "number : 0",
                                "$$hashKey": "object:50"
                            }
                        ],
                        "round": "number : 1"
                    }
                ],
                "type": 2,
                "fromqualifyrules": [
                    {
                        "frompouleplaces": [
                        "object:20",
                        "object:28"
                    ],
                        "topouleplaces": [
                        "object:50"
                    ],
                        "confignr": 0
                    }
                ],
                "toqualifyrules": [],
                "$$hashKey": "object:45"
            }
        ]
     *
     * @return mixed
     */
    public function getCSStructure(){ return $this->m_arrCSStructure; }

    public function getBus(){ return $this->m_oBus; }
    public function putBus( $oBus ){ $this->m_oBus = $oBus; }
}