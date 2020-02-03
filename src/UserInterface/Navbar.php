<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

use ProxyHTML\Authentication\Roles;
use ProxyHTML\Bootstrap;

class Navbar extends Roles
{

    private $array_main;

    private $array_dropdown;

    private $array_dropdown_sub;

    private $array_nav;

    public function __construct()
    
    {
        $act = new Action();
        parent::__construct($act->getSessionRole());
        
        $this->array_nav = array();
    }

    public function getNavbar($align = 'right')
    {
        $regex = 'Nav[a-z]*';
        
        $pages = $this->getRolePages();
        $pages = preg_grep('/\/Nav\//', $pages);
        $pages = preg_replace('/.+\/Nav\/*|\/*\w+\.php/', '', $pages);
        
        if (key_exists('Logout', $pages))
            unset($pages['Login']);
        
        foreach ($pages as $page_name => $menu) {
            
            if ($menu == '') {
                $this->addToNav($page_name);
                continue;
            } elseif (strstr($menu, '/')) {
                $menu = explode('/', $menu);
                $this->addToNav($page_name, $menu[0], $menu[1]);
            } else {
                $this->addToNav($page_name, $menu);
            }
        }
        
        return $this->assembleNavHTML($align);
    }

    private function addToNav($page, $dropdown = NULL, $subgroup = NULL)
    {
        if (strstr("Login|Logout", $page)) {
            $htm = new Bootstrap();
            $this->array_nav['logio'][] = $htm->button($page, '/' . $page ."/");
        } elseif ($dropdown == NULL) {
            $this->array_nav['main'][] = '<li class="nav-item"><a class="nav-link" href="/' . $page . '/">' . $page . '</a></li>';
        } elseif ($subgroup == NULL) {
            $this->array_nav[$dropdown][] = '<a class="dropdown-item" href="/' . $page . '/">' . $page . '</a>';
        } else {
            $this->array_nav[$dropdown][$subgroup][] = '<a class="dropdown-item" href="/' . $page . '/">' . $page . '</a>';
        }
    }

    private function assembleNavHTML($align = 'right')
    {
        $align = substr($align, 0, 1);
        
        $htm = '<ul class="navbar-nav m' . $align . '-auto">' . PHP_EOL;
        
        $htm_logio = '';
        foreach ($this->array_nav as $element => $group) {
            if ($element == 'logio') {
                $htm_logio = array_tostring($group, PHP_EOL, '') . PHP_EOL;
            } elseif ($element == 'main') {
                $htm .= array_tostring($group, PHP_EOL, '') . PHP_EOL;
            } else {
                $htm .= preg_replace("/\s+/", ' ', '
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
		                href="/" id="dropdown00" data-toggle="dropdown"
		                aria-haspopup="true" aria-expanded="false">' . $element . '</a>
		                <div class="dropdown-menu" aria-labelledby="dropdown00">');
                foreach ($group as $drop => $subgroup) {
                    if (is_array($subgroup)) {
                        $header = array_tostring(array_keys($subgroup), ' ', '');
                        $htm .= '<a class="dropdown-header">' . $drop . '</a>' . PHP_EOL;
                        $htm .= array_tostring($subgroup, PHP_EOL, '') . PHP_EOL;
                    } else {
                        $htm .= $subgroup . PHP_EOL;
                    }
                }
                $htm .= '</div></li>';
            }
        }
        $htm .= '</ul>' . PHP_EOL;
        $htm .= $htm_logio . PHP_EOL;
        return $htm;
    }
}
?>
