<?php

/**
 * Action Helper for loading forms
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class Admin_GebruikersbeheerController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $arrOptions = array();

        $nAuth = Source_Db::ACTION_UPDATE;
        if ( array_key_exists( "auth", $arrOptions ) and is_int( $arrOptions["auth"] ) )
            $nAuth += $arrOptions["auth"];
        $this->view->auth = $nAuth;

        $this->view->m_nRoleAuth = Source_Db::ACTION_INSERT + Source_Db::ACTION_DELETE;
        if ( array_key_exists( "roleauth", $arrOptions ) and is_int( $arrOptions["roleauth"] ) )
            $this->view->m_nRoleAuth = $arrOptions["roleauth"];

        $oRoleOptions = null;
        if ( array_key_exists( "roleoptions", $arrOptions ) and $arrOptions["roleoptions"] instanceof Construction_Option_Collection )
            $oRoleOptions = $arrOptions["roleoptions"];

        $this->view->pageTitle = "Gebruikersbeheer";

        $this->initUser();

        if ( $this->view->objSelectedUser !== null )
        {
            $oOptions = Construction_Factory::createOptions();
            if ( $oRoleOptions !== null )
                $oOptions = $oRoleOptions;
            $oOptions->addFilter( "RAD_Auth_Role::Id", "StartsWith", APPLICATION_NAME . "_" );
            $oOptions->addFilter( "RAD_Auth_Role::System", "EqualTo", 0 );
            $this->view->oInputRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase( $oOptions );
        }

        $this->view->message = $this->handleAction();
    }

    protected function initUser()
    {
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addOrder( "RAD_Auth_User::Name", false );
        $this->view->oUsers = RAD_Auth_User_Factory::createObjectsFromDatabase( $oOptions );

        $sUserId = $this->getRequest()->getParam("userid");
        if ( strlen( $sUserId ) > 0 )
            $this->view->objSelectedUser = $this->view->oUsers[ $sUserId ];
    }

    protected function handleAction()
    {
        $vtPreActionRetVal = null;
        $arrVars = get_object_vars( $this );
        if ( array_key_exists( "bDoCustomAction", $arrVars ) and $this->bDoCustomAction === true )
            $vtPreActionRetVal = $this->doCustomPreAction();

        $vtRetVal = null;
        if ( strlen( $this->getRequest()->getParam("btnadd") ) > 0 )
        {
            $vtRetVal = $this->insert();
        }
        elseif ( strlen( $this->getRequest()->getParam("btnedit") ) > 0 )
        {
            $vtRetVal = $this->update();
        }
        elseif ( strlen( $this->getRequest()->getParam("btnremove") ) > 0 )
        {
            $vtRetVal = $this->delete();
        }

        if ( array_key_exists( "bDoCustomAction", $arrVars ) and $this->bDoCustomAction === true )
            $this->doCustomPostAction( $vtPreActionRetVal );

        return $vtRetVal;
    }

    protected function insert()
    {
        $cfgAuth = new Zend_Config_Ini(APPLICATION_PATH.'/configs/config.ini', 'auth');
        if ( $cfgAuth->logintype === "database" )
        {
            $this->view->password = $this->getRequest()->getParam('password');
            if ( strlen ( $this->view->password ) < 8 )
                return "<div class=\"alert alert-danger\">Wachtwoord moet minimaal uit 8 karakters bestaan!</div>";

            $this->view->repeatpassword = $this->getRequest()->getParam('repeatpassword');
            if ( $this->view->repeatpassword !== $this->view->password )
                return "<div class=\"alert alert-danger\">Wachtwoorden zijn niet gelijk aan elkaar!</div>";
        }

        $this->view->newuserid = $this->getRequest()->getParam('newuserid');
        if ( strlen ( $this->view->newuserid ) === 0 )
            return "<div class=\"alert alert-danger\">Geen gebruikersnaam ingevuld!</div>";

        $oUser = $this->view->oUsers[ $this->view->newuserid ];
        if ( $oUser !== null )
            return "<div class=\"alert alert-danger\">Gebruikersnaam bestaat al!</div>";

        $objRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();
        if ( $objRoles[ $this->view->newuserid ] !== null )
            return "<div class=\"alert alert-danger\">Er is al een rol met gebruikersnaam ".$this->view->newuserid."!</div>";

        $oUserDbWriter = RAD_Auth_User_Factory::createDbWriter();

        $this->view->oUsers->addObserver( $oUserDbWriter );

        $oUser = RAD_Auth_User_Factory::createObject();
        $oUser->putId( $this->view->newuserid );
        $oUser->putSystem( false );
        if ( $cfgAuth->logintype === "database" )
        {
            $oUser->putPassword( hash( $cfgAuth->hashtype, $this->view->password ) );
        }
        $this->view->oUsers->add( $oUser );

        if ( $oUserDbWriter->write() === true )
        {
            $this->view->objSelectedUser = $oUser;
        }
    }

    protected function update()
    {
        /*
        // Start : Pas gebruiker aan
        $cfgAuth = new Zend_Config_Ini(APPLICATION_PATH.'/configs/config.ini', 'auth');
        $this->view->password = $this->getRequest()->getParam('password');
        if ( $cfgAuth->logintype === "database" and strlen ( $this->view->password ) > 0 )
        {
            if ( strlen ( $this->view->password ) < 8 )
                return "<div class=\"message-error\">Wachtwoord moet minimaal uit 8 karakters bestaan!</div>";

            $this->view->repeatpassword = $this->getRequest()->getParam('repeatpassword');
            if ( $this->view->repeatpassword !== $this->view->password )
                return "<div class=\"message-error\">Wachtwoorden zijn niet gelijk aan elkaar!</div>";

            $objDbWriter = RAD_Auth_User_Factory::createDbWriter();
            $this->view->objSelectedUser->addObserver( $objDbWriter );
            $this->view->objSelectedUser->putPassword( mpasdaan5 ( $this->view->password ) );
            $objDbWriter->write();
        }
        // End : Pas gebruiker aan
        $this->view->objSelectedUser->flushObservers();
        */

        // FLUSH ROLES ONLY FOR THIS APPLICATION!!!!!
        $oOptions = Construction_Factory::createOptions();
        $oOptions->addFilter( "RAD_Auth_Role::Id", "StartsWith", APPLICATION_NAME . "_" );
        $oUserRoles = RAD_Auth_Role_Factory::createObjectsFromDatabaseExt( $this->view->objSelectedUser, $oOptions );

        $oUserRoleDbWriter = RAD_Auth_Role_Factory::createUserDbWriter( $this->view->objSelectedUser );
        $oUserRoles->addObserver( $oUserRoleDbWriter );
        $oUserRoles->removeCollection( $this->view->oInputRoles );

        // Start : Loop door de post variabelen om te kijken welke rollen de gebruiker moet krijgen
        $objRoles = RAD_Auth_Role_Factory::createObjectsFromDatabase();
        foreach( $objRoles as $szRoleId => $objRole )
        {
            $szNewRole = $this->getRequest()->getParam( 'roles-'.$szRoleId );
            if ( strlen ( $szNewRole ) > 0 )
                $oUserRoles->add( $objRole );
        }
        // End : Loop door de post variabelen om te kijken welke rollen de gebruiker moet krijgen

        if ( $oUserRoleDbWriter->write() === true )
        {
            //$this->view->oUsers = RAD_Auth_User_Factory::createObjectsFromDatabase(); // commented because of bug
            // $this->view->objSelectedUser = $this->view->oUsers[$this->view->objSelectedUser->getId()];

            // verwijder menu van cache
            {
                // $cache = ZendExt_Cache::getCache( null, APPLICATION_PATH  . "/cache" );
                // $cache->remove( 'acl' );
            }
            return "<div class=\"alert alert-success\">Rollen aangepast voor gebruiker ".$this->view->objSelectedUser->getName()."</div>";
        }
        else
        {
            return "<div class=\"alert alert-danger\">Rollen konden niet worden aangepast voor gebruiker ".$this->view->objSelectedUser->getName()."</div>";
        }
        // End : Pas rollen aan
    }

    protected function delete()
    {
        if ( $this->view->objSelectedUser === null )
            return "<div class=\"alert alert-dangererror\">Gebruiker ".$this->getRequest()->getParam("userid")." kon niet worden gevonden.</div>";

        $objDbWriter = RAD_Auth_User_Factory::createDbWriter();
        $this->view->oUsers->addObserver( $objDbWriter );

        $this->view->oUsers->remove( $this->view->objSelectedUser );

        if ( $objDbWriter->write() === true )
        {
            $sName = $this->view->objSelectedUser->getName();
            $this->view->objSelectedUser = null;
            return "<div class=\"alert alert-success\">Gebruiker ".$sName." verwijderd.</div>";
        }
        else
        {
            return "<div class=\"alert alert-danger\">Gebruiker ".$this->view->objSelectedUser->getName()." kon niet worden verwijderd.</div>";
        }
    }
}
