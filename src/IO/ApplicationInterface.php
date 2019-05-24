<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\IO;

class ApplicationInterface
{

    private $Input;

    public function __construct(bool $test_sql_injection = true)
    {
        $this->Input = new Input($test_sql_injection);
    }

    public function getVar($variable, $value = NULL, bool $required = FALSE)
    {
        return $this->getVariable($variable, $value, $required);
    }

    public function getVariable($variable, $value = NULL, bool $required = FALSE)
    {
        $val = $this->Input->getVariable($variable, $value);
        if ($required == TRUE and is_null($val))
            $this->returnError(422, [
                'error' => '(' . $variable . ') parameter undefined'
            ]);
        
        return $val;
    }

    protected function returnError(int $code, array $message)
    {
        $this->returnJson($code, $message);
    }

    public function returnJson(int $code, array $array)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET");
        header("Access-Control-Allow-Headers: content-type");
        header("Content-Type: application/json; charset=UTF-8");
        
        http_response_code($code);
        echo json_encode($array, JSON_NUMERIC_CHECK);
        exit();
    }
}
?>