<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

use ProxyHTML\IO\Input;
use ProxyHTML\Authentication\Roles;

class Forms extends Input
{

    //
    public function __construct($test_sql_injection = true)
    {
        parent::__construct($test_sql_injection);
    }

    //
    private function preselectArray($name, $array)
    {
        $selected = $this->getVariable($name);
        
        if (! is_array($selected)) {
            if (! is_false($key = array_search($selected, $array)))
                $array[$key] = '*' . $array[$key];
            
            return $array;
        }
        
        foreach ($selected as $select) {
            if (! is_false($key = array_search($select, $array)))
                $array[$key] = '*' . $array[$key];
        }
        return $array;
    }

    //
    public function select($name, $array, $type = "drop", $height = 5)
    {
        $array = $this->preselectArray($name, $array);
        
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<select
				id="' . $id . '"
				name="' . $name . '">';
        
        // box | drop | multi
        if (strstr($type, 'onSelect')) {
            $change = 'onChange="location = this.options[this.selectedIndex].value;">';
            $htm = str_replace("<select", '<select ' . $change, $htm);
        }
        
        //
        if (stristr($type, 'multi')) {
            $htm = preg_replace("/\"\>/", '[]" multiple>', $htm);
        }
        //
        if (stristr($type, 'box')) {
            $change = 'size=' . $height;
            $htm = str_replace("<select", '<select ' . $change, $htm);
        }
        
        //
        foreach ($array as $variable => $value) {
            $htm .= '<option';
            if (preg_match("/^\*/", $value)) {
                $htm .= ' selected';
                $value = str_replace("*", "", $value);
            }
            $htm .= ' value="' . $value . '">' . $value . '</option>';
        }
        $htm .= "</select>";
        return $htm;
    }

    //
    public function radio($name, $array)
    {
        return $this->clickBox('radio', $name, $array);
    }

    //
    public function checkbox($name, $array)
    {
        return $this->clickBox('checkbox', $name, $array);
    }

    //
    protected function clickBox($type, $name, $array)
    {
        $array = $this->preselectArray($name, $array);
        
        $htm = '';
        foreach ($array as $variable => $value) {
            
            $variable = strtolower(preg_replace("/\s+/", "_", $variable));
            $htm .= '<input';
            $htm .= ' type="' . $type . '"';
            $htm .= ' name="' . $name;
            if ($type == 'checkbox')
                $htm .= '[]';
            $htm .= '"';
            if (preg_match("/^\*/", $value)) {
                $htm .= ' checked';
                $value = str_replace("*", "", $value);
            }
            $htm .= ' id="' . $variable . '"
					 value="' . $value . '">  ' . $value . '<br>';
        }
        $htm = preg_replace("/\<br\>$/", "", $htm);
        return $htm;
    }

    //
    public function hidden($name, $str = '')
    {
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<input
				type=hidden
				name="' . $name . '" id="' . $id . '"
				value="' . $str . '">';
        return $htm;
    }

    //
    public function submit($name, $str = '')
    {
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<input
				type=submit
				name="' . $name . '" id="' . $id . '"
				value="' . $str . '">';
        return $htm;
    }

    //
    public function textline($name, $str = '', $width = 20)
    {
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<input size=' . $width . '
				type="text"
				name="' . $name . '" id="' . $id . '"
				value="' . $str . '">';
        return $htm;
    }

    //
    public function passwordline($name, $str = '', $width = 20)
    {
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<input size=' . $width . '
				type="password"
				name="' . $name . '" id="' . $id . '"
				value="' . $str . '">';
        return $htm;
    }

    //
    public function textarea($name, $str = '', $width = 20, $height = 5)
    {
        $id = strtolower(preg_replace("/\s+/", "_", $name));
        $htm = '<textarea
				name="' . $name . '" id="' . $id . '"
				cols="' . $width . ' " rows="' . $height . '">
				' . $str . '</textarea>';
        return $htm;
    }

    //
    public function date()
    {
        $this->datepickerCount ++;
        $this->datepicker = true;
        $htm = "<input type=text name=$name id=\"dpc_edit" . $this->datepickerCount . "\" size=11 value=\"$value\">";
    }

    //
    public function form($inputs, $action = NULL, $method = "post")
    {
        $ui = new Action();
        
        if(is_null($action))
            $action = $ui->getVar('page');
        
        $action = $ui->getPageVariable($action);
        
        $htm = '<form 
					 action=".?page=' . $action . '"
					 method="' . $method . '">' . $inputs . '</form>';
        return $htm;
    }
}
?>