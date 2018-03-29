<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

class LookupIP
{

    protected $arr_location;

    //
    public function __construct($ipaddress)
    {
        $array = json_decode(file_get_contents('http://freegeoip.net/json/' . $_SERVER['REMOTE_ADDR']));
        $this->setLocationData($array);
    }

    //
    private function setLocationData($array)
    {
        $this->arr_location = $array;
    }

    //
    public function getIP()
    {
        return ($this->arr_location->ip);
    }

    //
    public function getCountry()
    {
        return ($this->arr_location->country_name);
    }

    //
    public function getCity()
    {
        return ($this->arr_location->city);
    }

    //
    public function getZipcode()
    {
        return ($this->arr_location->zipcode);
    }

    //
    public function getLatitude()
    {
        return ($this->arr_location->latitude);
    }

    //
    public function getLongitude()
    {
        return ($this->arr_location->longitude);
    }
}

?>