<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\UserInterface;

use ProxyHTML\Authentication\Roles;

class Navbar extends Roles
{

    private $Action;

    private $array_main;

    private $array_dropdown;

    private $array_dropdown_sub;

    public function __construct()
    
    {
        parent::__construct();
        
        $this->Action = new Action();
    }

    public function getNavbar()
    {
        $regex = 'Nav[a-z]*';
        
        $sr = $this->Action->getSessionRole();
        
        $pages = $this->getRoles();
        $roles = array_keys($pages);
        $pages = array_intersect_key($this->getPages(), array_flip($pages[$sr]));
        $pages = str_replace($this->path_pages, '', $pages);
        $pages = preg_replace('/(' . array_tostring($roles, '|', '') . ')\//', '', $pages);
        $pages = preg_replace("/$regex|\.php/", '', $pages);
        $pages = preg_replace("/^\w+$/", '', $pages);
        $pages = preg_replace("/^\//", '', $pages);
        
        if (array_search('Logout', $pages))
            unset($pages[array_search('Login', $pages)]);
        
        $pages = array_unique($pages);
        sort($pages);
        
        foreach ($pages as $page) {
            if ($page == '')
                continue;
            
            $page = explode('/', $page);
            
            if (sizeof($page) == 1)
                $this->addToNav($page[0]);
            
            if (sizeof($page) == 2)
                $this->addToNav($page[1], $page[0]);
            
            if (sizeof($page) >= 3)
                $this->addToNav($page[2], $page[0], $page[1]);
        }
        
        return $this->assembleNavHTML($this->array_nav);
    }

    private function addToNav($page, $dropdown = NULL, $subgroup = NULL)
    {
        if ($dropdown == NULL) {
            $this->array_nav['main'][] = '<li class="nav-item"><a class="nav-link" href=".?page=' . $page . '">' . $page . '</a></li>';
        } elseif ($subgroup == NULL) {
            $this->array_nav[$dropdown][] = '<a class="dropdown-item" href=".?page=' . $page . '">' . $page . '</a>';
        } else {
            $this->array_nav[$dropdown][$subgroup][] = '<a class="dropdown-item" href=".?page=' . $page . '">' . $page . '</a>';
        }
    }

    private function assembleNavHTML()
    {
        $htm = '<ul class="navbar-nav mr-auto">' . PHP_EOL;
        foreach ($this->array_nav as $element => $group) {
            if ($element == 'main') {
                $htm .= array_tostring($group, PHP_EOL, '') . PHP_EOL;
            } else {
                $htm .= preg_replace("/\s+/", ' ', '
                        <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
		                href="./" id="dropdown00" data-toggle="dropdown"
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
        return $htm;
    }
}
?>
