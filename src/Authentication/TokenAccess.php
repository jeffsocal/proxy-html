<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

use ProxyHTML\IO\ApplicationInterface;
use bandwidthThrottle\tokenBucket\storage\FileStorage;
use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\TokenBucket;

class TokenAccess extends ApplicationInterface
{

    private $access_key;

    private $token_path;

    private $rate_per_hour;

    public function __construct(int $rate_per_hour = 10, bool $test_sql_injection = true)
    {
        parent::__construct($test_sql_injection);
        
        if ($rate_per_hour == - 1)
            return NULL;
        
        $this->access_key = $this->getVar("key");
        $this->access_key = preg_replace("/\_|\-|\+|\s|\.|\/|\\]/", "", $this->access_key);
        if ($this->access_key == '')
            $this->access_key = NULL;
        /*
         * did (key) get called?
         */
        $this->keyArgInstantiated();
        $this->setTokenPath($this->access_key);
        
        /*
         * does the physical (key) exists?
         */
        $this->keyExists();
        
        /*
         * return a succes if testing a token is valid
         */
        if ($rate_per_hour == 0)
            $this->keyIsValid();
        
        /*
         * other wise depleate the token bucket
         */
        $this->invokeToken($rate_per_hour);
    }

    private function setTokenPath(string $token)
    {
        $this->token_path = get_include_path() . "api/tokens/" . $token . ".bucket";
    }

    public function getTokenPath()
    {
        return $this->token_path;
    }

    private function invokeToken(int $rate_per_hour)
    {
        /*
         * run the token bucket algorithm
         */
        $storage = new FileStorage($this->token_path);
        $rate = new Rate($rate_per_hour, Rate::HOUR);
        $bucket = new TokenBucket($rate_per_hour, $rate, $storage);
        
        /*
         * determine if the bucket is empty and return to user the time to wait
         */
        if (! $bucket->consume(1, $seconds))
            $this->returnError(429, [
                'error' => 'too many requests',
                'suggest' => 'retry-after ' . time_toString($seconds),
                'wait_sec' => $seconds
            ]);
    }

    private function keyArgInstantiated()
    {
        /*
         * determine if the access key was even invoked
         */
        if (is_null($this->access_key))
            $this->returnError(401, [
                'error' => 'key parameter not invoked',
                'input' => 'key: ' . $this->access_key
            ]);
    }

    private function keyExists()
    {
        /*
         * determine if the access key has a bootstrapped bucket
         */
        if (! file_exists($this->token_path))
            $this->returnError(401, [
                'error' => 'key not valid',
                'input' => 'key: ' . $this->access_key,
                'suggest' => 'url: https://jaevalen.com/Register/'
            ]);
    }

    private function keyIsValid()
    {
        $this->returnError(200, [
            'success' => 'key is valid'
        ]);
    }

    public function boostrapTokenAccess(int $rate_per_hour, string $token)
    {
        $this->setTokenPath($token);
        
        $storage = new FileStorage($this->token_path);
        $rate = new Rate($rate_per_hour, Rate::HOUR);
        $bucket = new TokenBucket($rate_per_hour, $rate, $storage);
        
        $bucket->bootstrap($rate_per_hour);
        
        return TRUE;
    }
}

?>