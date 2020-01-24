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

        $this->Roles = new Roles($this->getSessionRole());
    }

    protected function defaultPage()
    {
        return $this->Roles->getDefaultPage();
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
     * if AUTH = false, filter to public domian
     */
    public function getPagePath()
    {
        $page = $this->getVariable('page');

        return $this->Roles->getPagePath($page);
    }

    public function getRoutePath()
    {
        $page = $_SERVER['REQUEST_URI'];
        $page = preg_replace("/\?.*/", "", $page);
        $page = trim($page, "/");

        return $this->Roles->getPagePath($page);
    }

    public function getDefaultPagePath()
    {
        return $this->Roles->getDefaultPagePath();
    }

    public function getPageVariable($page = NULL)
    {
        if (is_null($page))
            $page = $this->getVariable('page');

        return $this->Roles->getPageVariable($page);
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
