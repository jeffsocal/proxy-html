<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML;

class Sitemap
{

    private $map_array;

    //
    public function __construct()
    {
        $this->map_array = array();
    }

    protected function addUrl($loc, $lastmod = NULL, $changefreq = 'weekly', $priority = 0.5)
    {
        if (is_null($lastmod))
            $lastmod = date("Y-m-d");
        
        $this->map_array[] = array(
            'loc' => 'http://' . $_SERVER['SERVER_NAME'] . '/' . $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority
        );
    }

    protected function getXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($this->map_array as $element) {
            $this_element = "<url>\n";
            $this_element .= "<loc>#loc</loc>\n";
            $this_element .= "<lastmod>#mod</lastmod>\n";
            $this_element .= "<changefreq>#frq</changefreq>\n";
            $this_element .= "<priority>#pri</priority>\n";
            $this_element .= "</url>\n";
            
            $this_element = preg_replace("/\#loc/", $element['loc'], $this_element);
            $this_element = preg_replace("/\#mod/", $element['lastmod'], $this_element);
            $this_element = preg_replace("/\#frq/", $element['changefreq'], $this_element);
            $this_element = preg_replace("/\#pri/", $element['priority'], $this_element);
            
            $xml .= $this_element . "\n";
        }
        $xml .= '</urlset>';
        
        return $xml;
    }
}
?>