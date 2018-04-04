<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML;

use ProxyHTML\UserInterface\Action;

class Display
{

    //
    protected $html_title;

    protected $html_main;

    protected $html_body;

    protected $html_section;

    protected $html_js;

    protected $html_body_header;

    protected $html_body_footer;

    protected $html_message;

    //
    public function __construct($main = NULL, $body = NULL)
    {
        $ini = parse_ini_file('ini/config.ini');
        $pgs = new Action();
        $page = $pgs->getPageVariable();
        if (strstr($page, 'Default'))
            $page = '';
        
        $this->html_body = "";
        $this->html_section = array();
        $this->html_page = "";
        $this->html_js = "";
        $this->html_message = false;
        
        $this->setMain($main);
        $this->setBody($body);
        $this->setTitle(trim($ini['site_name'] . ' - ' . $page, ' - '));
    }

    public function sp($n = 1)
    {
        return str_repeat('&nbsp', $n);
    }

    //
    public function finalHTML()
    {
        // print_r($this->html_section);
        return $this->buildPage();
    }

    //
    private function buildElement($element)
    {
        foreach ($this->html_section as $name => $contents) {
            // insert n
            $element = preg_replace("/<!--\s*#" . $name . "\s*-->/", $contents, $element);
        }
        return $element;
    }

    //
    private function buildPage()
    {
        $this_html = preg_replace("/<!--\s*#BODY\s*-->/", $this->html_body, $this->html_main);
        
        // insert TITLE
        $this_html = preg_replace("/<!--\s*#TITLE\s*-->/", $this->html_title, $this_html);
        
        // insert BODY
        $this_html = $this->buildElement($this_html);
        
        $this_html = preg_replace("/[\s\n]+/", " ", $this_html);
        // $this_html = preg_replace ("/\>[\s\n]+/", ">", $this_html);
        // $this_html = preg_replace ("/[\s\n]+\</", "<", $this_html);
        return $this_html;
    }

    //
    public function addToSection($section = "", $text = "")
    {
        $section = strtoupper($section);
        
        if (! key_exists($section, $this->html_section))
            $this->html_section[$section] = "";
        
        $this->html_section[$section] .= $text . PHP_EOL;
    }

    //
    public function addToContent($text = "")
    {
        $this->addToSection("content", $text);
    }

    //
    public function addToJS($text = "")
    {
        $this->addToSection("jscript", $text);
    }

    //
    public function addToUserMessages($text)
    {
        $this->html_message[] = $text . PHP_EOL;
    }

    //
    protected function setMain($text)
    {
        $this->html_main = $text;
    }

    //
    protected function setBody($text)
    {
        $this->html_body = $text;
    }

    //
    protected function setTitle($title)
    {
        $this->html_title = $title;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}

?>