<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\IO;

use ProxyHTML\Display;
use ProxyHTML\Authentication\Authenticate;
use ProxyIO\Args;
use ProxyMySQL\Base\DetectHack;

class Input extends Args
{

    public function __construct($test_sql_injection = true)
    {
        parent::__construct();
        
        $this->getWebVariables();
        
        /*
         * detect for SQL injection when not using
         * prepared statements
         */
        if (is_true($test_sql_injection)) {
            $this->sql_hack = new DetectHack();
            $this->testWebVariables($this->array_vars);
        }
    }

    private function loadHtmlVariables($obj)
    {
        foreach ($obj as $name => $value) {
            if (! is_array($value))
                $value = trim($value);
            
            $this->array_vars[$name] = urldecode($value);
        }
    }

    // test every input variable for sql injection
    private function testWebVariables($array)
    {
        foreach ($array as $name => $value) {
            if (is_array($value)) {
                $this->testWebVariables($value);
            } else {
                if (is_true($this->sql_hack->is_sqlinject($value))) {
                    
                    $this->clearWebVariables();
                    $htm = new Display();
                    $ath = new Authenticate('localhost');
                    $ath->end();
                    $htm->redirect(".");
                }
            }
        }
    }

    private function getWebVariables()
    {
        $this->loadHtmlVariables($_GET);
        $this->loadHtmlVariables($_POST);
        $this->loadHtmlVariables($_FILES);
    }

    private function clearWebVariables()
    {
        $_GET = [];
        $_POST = [];
        $_FILES = [];
    }
}
?>