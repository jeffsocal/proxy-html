<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

use ProxyMySQL\Transact;

class Authenticate extends Sessions
{

    protected $method_is_file;

    protected $sql_schema;

    protected $db;

    private $ini_path;

    /*
     * work around to multiple inheritance
     * FILE, SQL based methods
     */
    public function __construct($server = false, $schema = false)
    {
        parent::__construct();
        
        $this->setLogFileDirectory("auth");
        
        $this->method_is_file = is_false($server);
        
        if (! is_false($server)) {
            $this->db = new Transact($server);
            $this->sql_schema = $schema;
        }
    }

    private function cleanUserID($login)
    {
        return strtolower($login);
    }

    //
    public function isUserValid($login, $password)
    {
        $login = $this->cleanUserID($login);
        
        if (is_true($this->method_is_file)) {
            return $this->isUserValid_file($login, $password);
        } else {
            return $this->isUserValid_sql($login, $password);
        }
    }

    //
    public function createNewUser($login, $password)
    {
        $login = $this->cleanUserID($login);
        
        if (is_true($this->method_is_file)) {
            return $this->createNewUser_file($login, $password);
        } else {
            return $this->createNewUser_sql($login, $password);
        }
    }

    private function setSessionUser($login)
    {
        $this->setKey('USERID', $this->cleanUserID($login));
    }

    /*
     * SQL based authentication
     */
    private function isUserValid_sql($login, $password)
    {
        $str = 'SELECT * FROM ' . $this->sql_schema . '.credentials WHERE login = ?';
        $par = [
            'login' => $login
        ];
        $table = $this->db->transaction($str, $par);
        
        if (is_false($table)) {
            $this->addToLog("Fail", "Login not recognized.");
            return false;
        }
        
        $password_hashed = $table['password_hashed'][0];
        
        // compare password to hashed value
        if (is_false(password_verify($password, $password_hashed))) {
            $this->addToLog("Fail", "Password incorrect.");
            return false;
        }
        
        $this->setAuthenticated();
        $this->setSessionUser($login);
        $this->setRole($table['role'][0]);
        $this->addToLog("Success", "Credentials match.");
        return true;
    }

    public function getErrorMessage()
    {
        return $this->getCurrentlogDump();
    }

    //
    private function createNewUser_sql($login, $password)
    {
        
        // hash the password for sql storage
        $password = password_hash($password, PASSWORD_DEFAULT);
        
        $str = 'INSERT INTO 
					' . $this->sql_schema . '.credentials
					(login, password_hashed) 
					VALUES 
					( ? , ? )';
        
        $par = [
            'login' => $login,
            'password_hashed' => $password
        ];
        
        $sql = $this->db->transaction($str, $par);
        
        if (is_false($sql))
            $this->addToLog('Error', "Failed to create new user");
        
        return $sql;
    }

    /*
     * FILE based authentication
     */
    private function isUserValid_file($login, $password)
    {
        if (is_false(file_exists("../ini/" . $login))) {
            $this->addToLog("Error", "isUserValid_file::Login not recognized.");
            return false;
        }
        
        $password_hashed = file_get_contents("../ini/" . $login);
        
        // compare password to hashed value
        if (is_false(password_verify($password, $password_hashed))) {
            $this->addToLog("Error", "isUserValid_file::Password incorrect.");
            return false;
        }
        
        $this->setAuthenticated();
        $this->setSessionUser($login);
        $this->setRole('admin');
        $this->addToLog("Success", "isUserValid_file::Credentials match.");
        return true;
    }

    //
    private function createNewUser_file($login, $password)
    {
        
        // hash the password for sql storage
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        if (is_true(file_exists("../ini/" . $login))) {
            $this->addToLog("Error", "User already exists.");
            return false;
        }
        
        if (is_false(file_put_contents("../ini/" . $login, $password_hashed))) {
            $this->addToLog('Error', "Failed to create new user");
        }
        return true;
    }
}
?>