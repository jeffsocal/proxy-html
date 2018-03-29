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

    protected $path_pages;

    private $index_pages;

    protected $include_path;

    public function __construct()
    {
        $this->include_path = get_include_path();
        $ini = parse_ini_file('ini/config.ini');
        $this->path_pages = $ini['pages_path'];
        $this->indexPages();
    }

    private function indexPages()
    {
        $dir = new RecursiveDirectoryIterator($this->include_path . $this->path_pages);
        $itr = new RecursiveIteratorIterator($dir);
        $rgx = new RegexIterator($itr, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        
        $this->index_pages = array();
        foreach ($rgx as $name => $obj) {
            $page_path = str_replace($this->include_path, '', $name);
            $page_name = preg_replace("/.+\/|\.php/", '', $page_path);
            
            $this->index_pages[$page_name] = $page_path;
        }
    }

    public function pageExists($page)
    {
        return array_key_exists($page, $this->index_pages);
    }

    public function getPagePath($page)
    {
        if (is_true($this->pageExists($page)))
            return $this->index_pages[$page];
        
        return $this->index_pages['Default'];
    }

    public function getPages()
    {
        return $this->index_pages;
    }

    protected function listPages()
    {
        return $this->getPages();
    }
}
?>