<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML;

class Tables
{

    protected $is_striped;

    //
    public function __construct($striped = false)
    {
        $this->is_striped = $striped;
    }

    //
    public function table_ashtml($table_array, $print_header = true)
    {
        $len = table_length($table_array);
        $head = table_header($table_array);
        $html = "<table class=\"table ";
        if (is_true($this->is_striped))
            $html .= "table-striped ";
        $html .= "table-condensed\">" . PHP_EOL;
        
        /*
         * HEADER
         */
        if (is_true($print_header)) {
            $html .= "\t<thead>" . PHP_EOL;
            $html .= "\t\t<tr>" . PHP_EOL;
            foreach ($head as $col) {
                $html .= "\t\t\t<th>" . $col . "</th>" . PHP_EOL;
            }
            $html .= "\t\t</tr>" . PHP_EOL;
            $html .= "\t</thead>" . PHP_EOL;
        }
        /*
         * TABLE BODY
         */
        $html .= "\t<tbody>" . PHP_EOL;
        for ($i = 0; $i < $len; $i ++) {
            $html .= "\t\t<tr>" . PHP_EOL;
            foreach ($head as $col) {
                $html .= "\t\t\t<td>" . $table_array[$col][$i] . "</td>" . PHP_EOL;
            }
            $html .= "\t\t</tr>" . PHP_EOL;
        }
        $html .= "\t</tbody>" . PHP_EOL;
        $html .= "</table>" . PHP_EOL;
        return $html;
    }
}
?>