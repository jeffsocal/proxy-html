<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\IO;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class Pages
{

    private $index_pages;

    private $default_page;

    public $pages_path;

    public $include_path;

    public function __construct()
    {
        $this->include_path = get_include_path();
        $this->pages_path =  $_SERVER['INI']['pages_path'];
        $this->default_page =  $_SERVER['INI']['site_default'];
        $this->indexPages();
    }

    private function indexPages()
    {
        $dir = new RecursiveDirectoryIterator($this->include_path . $this->pages_path);
        $itr = new RecursiveIteratorIterator($dir);
        $rgx = new RegexIterator($itr, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        
        $this->index_pages = array();
        foreach ($rgx as $name => $obj) {
            $page_path = str_replace($this->include_path, '', $name);
            $this->index_pages[] = $page_path;
        }
    }

    public function getDefaultPagePath()
    {
        return $this->include_path . $this->pages_path . $this->default_page;
    }

    public function getDefaultPage()
    {
        return preg_replace("/\w+\/|\.php/", '', $this->default_page);
    }

    protected function getPages()
    {
        return $this->index_pages;
    }
}
?>