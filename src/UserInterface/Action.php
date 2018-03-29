<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

use ProxyHTML\Authentication\Roles;
use ProxyHTML\Authentication\Sessions;
use ProxyHTML\IO\Input;

class Action extends Input
{

    private $is_Authenticated;

    private $role_Authenticated;

    private $Roles;

    public function __construct()
    {
        parent::__construct();
        $auth = new Sessions();
        
        $this->setSessionAuth($auth->isAuthenticated());
        $this->setSessionRole($auth->getAuthenticatedRole());
        
        $this->Roles = new Roles();
    }

    protected function defaultPage()
    {
        return 'Default';
    }

    private function setSessionAuth($boolean)
    {
        $this->is_Authenticated = $boolean;
    }

    /*
     * manage roles
     */
    private function setSessionRole($str_role)
    {
        $this->role_Authenticated = $str_role;
    }

    public function getSessionRole()
    {
        return $this->role_Authenticated;
    }

    /*
     * filter to ROLE allowed pages
     */
    public function pageValidate($page)
    {
        /*
         * does the page exists
         */
        if (is_false($this->Roles->pageExists($page)))
            return $this->defaultPage();
        
        /*
         * has the session been tagged with a role
         */
        if (is_false($session_role = $this->getSessionRole()))
            return $this->defaultPage();
        
        $index_roles = $this->Roles->getRoles();
        
        /*
         * does the role exist
         */
        if (! key_exists($session_role, $index_roles))
            return $this->defaultPage();
        
        /*
         * can the role access that page
         */
        if (is_false(array_search($page, $index_roles[$session_role])))
            return $this->defaultPage();
        
        return $page;
    }

    /*
     * get the page from the GET variable
     */
    public function getPageVariable()
    {
        $page = $this->getVariable('page');
        if (is_null($page)) 
            $page = $this->defaultPage();
        
            return $this->pageValidate($page);
    }

    /*
     * if AUTH = false, filter to public domian
     */
    public function getPagePath()
    {
        return $this->Roles->getPagePath($this->getPageVariable());
    }
    
    

    public function formSubmitted()
    {
        $args = $this->listVariables();
        
        if (! is_false($k = array_search('page', $args)))
            unset($args[$k]);
        
        return (sizeof($args) > 0);
    }
}
?>
