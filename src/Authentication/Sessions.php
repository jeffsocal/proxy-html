<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

use ProxyIO\File\Log;

class Sessions extends Log
{

    //
    public function __construct()
    {
        parent::__construct('auth');
        if (! isset($_SESSION))
            session_start();
    }

    public function start()
    {
        if (isset($_SESSION)) {
            $this->end();
        }
        session_start();
        $this->addToLog('SESSION', 'STARTED');
    }

    public function end()
    {
        // remove all session variables
        session_unset();
        // destroy the session
        session_destroy();
        $this->addToLog('SESSION', 'ENDED');
    }

    //
    public function getKey($name)
    {
        
        $name = strtoupper($name);
        
        if (! isset($_SESSION))
            return false;
        
        if (! key_exists($name, $_SESSION)) {
            // $this->addToLog('SESSION', 'Value[' . $name . '] not found.');
            return false;
        }
        
        return $_SESSION[$name];
    }

    //
    public function setKey($name, $value)
    {
        $this->addToLog('SESSION', $name . ' set as [' . $value . '].');
        $_SESSION[$name] = $value;
    }

    //
    protected function setAuthenticated()
    {
        $this->setKey("AUTHENTICATED", TRUE);
    }

    //
    protected function setRole($str_role)
    {
        $this->setKey("ROLE", $str_role);
    }

    //
    public function isAuthenticated()
    {
        $boolean = is_true($this->getKey("AUTHENTICATED"));
//         $boolean = is_false(is_false($this->getKey("AUTHENTICATED")));
        return $boolean;
    }

    //
    public function getAuthenticatedRole()
    {
        if (is_false($role = $this->getKey("ROLE")))
            $role = 'Public';
        
        return strtotitle($role);
    }
}
?>