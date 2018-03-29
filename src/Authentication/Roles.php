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

    private $roles_json;

    private $list_roles_by;

    public function __construct()
    {
        parent::__construct();
        
        $ini = parse_ini_file('ini/config.ini');
        $this->roles_json = $this->include_path . $ini['roles_json'];
        $this->list_roles_by = $ini['list_roles_by'];
    }

    public function getRoles()
    {
        if ($this->list_roles_by == 'json')
            return $this->getRolesByConfig();
        
        return $this->getRolesByInference();
    }

    private function getRolesByInference()
    {
        $pages = $this->getPages();
        $roles = array();
        foreach ($pages as $name => $path) {
            $role = str_replace($this->path_pages, '', $path);
            $role = preg_replace("/\/.+\.php$/", '', $role);
            $roles[$role][] = $name;
        }
        return $this->mergeRoles($roles);
    }

    private function getRolesByConfig()
    {
        $roles = json_decode(file_get_contents($this->roles_json), TRUE);
        
        return $this->mergeRoles($roles);
    }

    /*
     * Merge Public with each of the specified Roles
     * This could be removed if some Public functions one
     * wanted to keep away from a User .. seems silly however
     */
    private function mergeRoles($roles)
    {
        foreach ($roles as $role => $array) {
            if (strstr('User|Admin', $role))
                $array = array_merge($array, $roles['Public']);
            
            if (strstr('Admin', $role))
                $array = array_merge($array, $roles['User']);
            
            sort($array);
            $roles[$role] = array_values(array_unique($array));
        }
        return $roles;
    }
}
?>