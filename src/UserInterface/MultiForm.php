<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

class MultiFroms extends Forms
{

    private $htm_multiform;

    private $htm_messages;

    //
    public function addElement($title, $variable, $value, $type = '', $required = true)
    {
        $htm_multiform[$variable]['value'] = $value;
        $htm_multiform[$variable]['title'] = $title;
        $htm_multiform[$variable]['type'] = $type;
        $htm_multiform[$variable]['error'] = '';
        
        if (is_true($required) and ($value == '' or is_null($value)))
            $htm_multiform[$variable]['error'] = '<b><font color="red">( ! )</font></b>';
    }

    //
    public function multiforms()
    {}

    //
    private function validateMultiForm()
    {}
}
?>