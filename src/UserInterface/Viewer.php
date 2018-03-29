<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

use ProxyHTML\Authentication\LookupIP;
use ProxyIO\File\Log;

class Viewer
{

    private $log;

    private $ipa;

    private $viewer_whois;

    private $viewer_track;

    function __construct()
    {
        $ini = parse_ini_file('ini/config.ini');
        $this->viewer_track = is_true($ini['viewer_track'] == 'yes');
        $this->viewer_whois = is_true($ini['viewer_whois'] == 'yes');
    }

    function whois()
    {
        if (is_false($this->viewer_whois))
            return false;
        
        $ipl = new LookupIP($_SERVER['REMOTE_ADDR']);
        
        $trace = '';
        $trace .= $ipl->getIP();
        $trace .= ' ' . $ipl->getCity();
        $trace .= ' ' . $ipl->getCountry();
        $trace .= ' ' . $ipl->getLatitude();
        $trace .= ' ' . $ipl->getLongitude();
        
        $log = new Log('traffic');
        $log->addToLog($trace);
    }

    function track()
    {
        if (is_false($this->viewer_track))
            return false;
        
        $trace = '';
        $trace .= $_SERVER['REMOTE_ADDR'];
        $trace .= ' ' . $_SERVER['REQUEST_URI'];
        
        $log = new Log('tracking');
        $log->addToLog($trace);
    }
}

?>