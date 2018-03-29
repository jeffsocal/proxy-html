<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML;

use ProxyHTML\Authentication\Sessions;
use ProxyIO\File\Read;

class Construct extends Sessions
{

    //
    private $htm;

    //
    public function __construct()
    {
        parent::__construct();
        
        $this->htm = array();
        
        $authRole = $this->getAuthenticatedRole();
        $parts = array(
            "Main",
            "Body",
            "Footer",
            "Navbar"
        );
        
        foreach ($parts as $part) {
            $filename = '../src/layouts/' . $authRole . '/' . $part . '.htm';
            if (! file_exists($filename))
                $filename = '../src/layouts/Default/' . $part . '.htm';
            
            $this->htm[$part] = new Read($filename);
        }
    }

    //
    private function getSection($section)
    {
        return $this->htm[$section]->getContents();
    }

    //
    public function getMain()
    {
        return $this->getSection('Main');
    }

    //
    public function getBody()
    {
        return $this->getSection('Body');
    }

    //
    public function getFooter()
    {
        return $this->getSection('Footer');
    }

    //
    public function getNavbar()
    {
        return $this->getSection('Navbar');
    }

    /*
     * this needs to point to both the layout directory
     * and the layout file i.e. 'Default/Body'
     */
    public function getOther($layout)
    {
        $authRole = $this->getAuthenticatedRole();
        
        $filename = '../src/layouts/' . $authRole . '/' . $layout . '.htm';
        if (! file_exists($filename))
            $filename = '../src/layouts/Default/' . $layout . '.htm';
        
        if (! file_exists($filename))
            return '';
        
        $htm = new Read($filename);
        return $htm->getContents();
    }
}
?>