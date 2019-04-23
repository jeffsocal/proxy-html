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

    private $rate_per_hour;

    public function __construct(int $rate_per_hour = 10, bool $test_sql_injection = true)
    {
        parent::__construct($test_sql_injection);
        
        $this->access_key = $this->getVar("key");
        $this->invokeTokenAccess($rate_per_hour);
    }

    private function invokeTokenAccess(int $rate_per_hour)
    {
        
        /*
         * determine if the access key was even invoked
         */
        if (is_null($this->access_key))
            $this->returnError(401, [
                'error' => 'key parameter not invoked',
                'key' => $this->access_key
            ]);
        
        /*
         * determine if the access key has a bootstrapped bucket
         */
        if (! file_exists(get_include_path() . "api/token/" . $this->access_key . ".bucket"))
            $this->returnError(401, [
                'error' => 'key not found',
                'key' => $this->access_key,
                'suggest' => 'url: https://jvln.io/GetTokenKey/'
            ]);
        
        /*
         * run the token bucket algorithm
         */
        $storage = new FileStorage(get_include_path() . "api/token/" . $this->access_key . ".bucket");
        $rate = new Rate($rate_per_hour, Rate::HOUR);
        $bucket = new TokenBucket($rate_per_hour, $rate, $storage);
        
        $bucket->bootstrap($rate_per_hour);
        
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

    public function boostrapTokenAccess(int $rate_per_hour, string $token_key)
    {
        $storage = new FileStorage(get_include_path() . "api/token/" . $this->access_key . ".bucket");
        $rate = new Rate($rate_per_hour, Rate::HOUR);
        $bucket = new TokenBucket($rate_per_hour, $rate, $storage);
        
        $bucket->bootstrap($rate_per_hour);
    }
}

?>