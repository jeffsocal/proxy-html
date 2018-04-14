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

    private $htm;

    private $include_path;

    private $layouts_path;

    public function __construct()
    {
        parent::__construct();
        
        $this->include_path = get_include_path();
        $ini = parse_ini_file('ini/config.ini');
        $this->layouts_path = $ini['layouts_path'];
        
        $this->htm = array();
        
        $authRole = $this->getAuthenticatedRole();
        $parts = array(
            "Main",
            "Body",
            "Footer",
            "Navbar"
        );
        
        foreach ($parts as $part) {
            $filename = '../' . $this->layouts_path . $authRole . '/' . $part . '.htm';
            if (! file_exists($filename))
                $filename = '../' . $this->layouts_path . 'Default/' . $part . '.htm';
            
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
        
        $filename = '../' . $this->layouts_path . $authRole . '/' . $layout . '.htm';
        
        if (! file_exists($filename))
            $filename = '../' . $this->layouts_path . 'Default/' . $layout . '.htm';
            
        
        if (! file_exists($filename))
            return '';
        
        $htm = new Read($filename);
        return $htm->getContents();
    }
}
?>