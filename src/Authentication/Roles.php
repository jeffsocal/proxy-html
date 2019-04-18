<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

use ProxyHTML\IO\Pages;

class Roles extends Pages
{

    private $index_roles;

    private $site_roles;

    public function __construct($role = 'Public')
    {
        parent::__construct();
        
        $ini = parse_ini_file('ini/config.ini');
        $this->site_roles = explode(",", $ini['site_roles']);
        
        $this->indexRolePages($role);
    }

    public function getPagePath($page)
    {
        if (is_null($page))
            $page = $this->getDefaultPage();
        
        if (! key_exists($page, $this->index_roles))
            $page = $this->getDefaultPage();
        
        if (! file_exists($this->include_path . $this->index_roles[$page]))
            $page = $this->getDefaultPage();
        
        return $this->include_path . $this->index_roles[$page];
    }

    public function getPageVariable($page)
    {
        if (is_null($page))
            $page = $this->getDefaultPage();
        
        if (! key_exists($page, $this->index_roles))
            $this->pageNotFound();
        
        if (! file_exists($this->include_path . $this->index_roles[$page]))
            $this->pageNotFound();
        
        return $page;
    }

    private function pageNotFound()
    {
        http_response_code(404);
        exit();
    }

    protected function getRolePages()
    {
        return $this->index_roles;
    }

    private function indexRolePages($role = 'Public')
    {
        $pages = $this->getPages();
        $roles = array();
        foreach ($this->site_roles as $this_role) {
            $roles[$this_role] = array_values(preg_grep("/\/$this_role\//", $pages));
        }
        
        $index_roles = $this->mergeRoles($roles);
        
        $this->index_roles = $index_roles[$role];
    }

    /*
     * Merge Public with each of the specified Roles
     * This could be removed if some Public functions one
     * wanted to keep away from a User .. seems silly however
     */
    private function mergeRoles($roles)
    {
        foreach ($roles as $role => $array) {
            
            if (strstr('User', $role))
                $array = array_merge($array, $roles['Public']);
            
            if (strstr('Admin', $role))
                $array = array_merge($array, $roles['User']);
            
            $array = array_values($array);
            
            $pages = array_unique(preg_replace("/.+\/|\.php/", '', array_values($array)));
            $array = array_combine($pages, array_intersect_key($array, $pages));
            
            $roles[$role] = $array;
        }
        
        return $roles;
    }
}
?>