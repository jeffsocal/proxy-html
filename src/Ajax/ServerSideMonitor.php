<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\ajax;

use ProxyIO\File\Read;

class ServerSideMonitor extends Read
{

    //
    public function __construct()
    {
        parent::__construct(absPath('../ajax/process_serverside.js'));
    }

    public function getScriptElement($tag = 'ssp', $page = 'Ajax.php', $target = 'target')
    {
        $this_html = $this->getContents();
        
        // insert TAG
        $this_html = preg_replace("/#TAG/", $tag, $this_html);
        
        // insert PAGE
        $this_html = preg_replace("/#PAGE/", $page, $this_html);
        
        // insert TARGET
        $this_html = preg_replace("/#TARGET/", urlencode($target), $this_html);
        
        return $this_html;
    }
}
?>