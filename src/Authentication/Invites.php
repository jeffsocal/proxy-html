<?php

/*
 * Written by Jeff Jones (jeff@socalbioinformatics.com)
 * Copyright (2016) SoCal Bioinformatics Inc.
 *
 * See LICENSE.txt for the license.
 */
namespace ProxyHTML\Authentication;

use ProxyIO\File\Log;
use ProxyMySQL\Parameterized;

class Invites extends Log
{

    private $invite_email;

    protected $method_is_file;

    protected $sql_schema;

    protected $db;

    /*
     * work around to multiple inheritance
     * FILE, SQL based methods
     */
    public function __construct($server = false, $schema = false)
    {
        parent::__construct("auth");
        
        $this->method_is_file = is_false($server);
        
        if (! is_false($server)) {
            $this->db = new Parameterized($server);
            $this->sql_schema = $schema;
        }
    }

    //
    public function isInviteValid($id)
    {
        if (is_true($this->method_is_file)) {
            return $this->isInviteValid_file($id);
        } else {
            return $this->isInviteValid_sql($id);
        }
    }

    //
    public function createNewInvite($email)
    {
        if (is_true($this->method_is_file)) {
            return $this->createNewInvite_file($email);
        } else {
            return $this->createNewInvite_sql($email);
        }
    }

    //
    public function getInviteEmail($id)
    {
        return $this->invite_email;
    }

    /*
     * SQL based authentication
     */
    private function isInviteValid_sql($id)
    {
        $sql_string = 'SELECT * FROM ' . $this->sql_schema . '.invites
                        WHERE id = ?';
        $this->db->setStatement($sql_string);
        $this->db->addVarible('id', urldecode($id));
        
        $table = $this->db->paramGet();
        
        if (is_false($table)) {
            $this->addToLog("Error", "SQL::Invite not recognized => ".urldecode($id));
            return false;
        }
        
        $this->invite_email = $table['email'][0];
        
        $this->addToLog("Success", "Invite recognized.");
        return $this->invite_email;
    }

    //
    private function createNewInvite_sql($email)
    {
        
        $str_random = randomString(60);
        
        $sql_string = 'INSERT INTO ' . $this->sql_schema . '.invites
                        (email, id) VALUES ( ? , ? )';
        
        $this->db->setStatement($sql_string);
        $this->db->addVarible('email', $email);
        $this->db->addVarible('id', $str_random);
        
        $sql = $this->db->paramPut();
        
        if (is_false($sql)) {
            $this->addToLog('Error', "SQL::Failed to create new invitation");
            return false;
        }
        return $str_random;
    }

    /*
     * FILE based authentication
     */
    private function isInviteValid_file($id)
    {
        if (is_false(file_exists("../ini/" . $id))) {
            $this->addToLog("Error", "Invite not recognized.");
            return false;
        }
        
        $this->invite_email = file_get_contents("../ini/" . $id);
        
        /*
         * remove the invite
         */
        unlink("../ini/" . $id);
        
        $this->addToLog("Success", "Invite recognized.");
        return $this->invite_email;
    }

    //
    private function createNewInvite_file($email)
    {
        
        // hash the password for sql storage
        $str_random = preg_replace('/\!|\@|\#|\$|\&/', '', randomString(60));
        $str_random = substr($str_random, 0, 16);
        
        if (is_true(file_exists("../ini/" . $email))) {
            $this->addToLog("Error", "User already exists.");
            return false;
        }
        
        if (is_false(file_put_contents("../ini/" . $str_random, $email))) {
            $this->addToLog('Error', "Failed to create new invitation");
            return false;
        }
        return $str_random;
    }
}
?>