<?php
include_once(__DIR__ . "/../../vendor/autoload.php");

class NoUserException extends Exception
{
    public function errorMessage()
    {
        //error message
        $errorMsg = "No user record";
        return $errorMsg;
    }
}

class Finder
{
    private $row;

    function __construct()
    {
        ORM::configure(array(
            'connection_string' => 'mysql:host=163.13.21.1;dbname=xoops',
            'username' => 'chenmt',
            'password' => '403840308',
            'charset' => 'utf8mb4',
            'driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
            'return_result_sets', true
        ));
    }

    /**
     * @throws NoUserException
     */
    public function fetch($id)
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $table = ORM::forTable('xoops2_users')->selectMany('xoops2_users.uid', 'email', 'name', 'uname', 'xoops2_groups_users_link.groupid')->innerJoin('xoops2_groups_users_link', array('xoops2_users.uid', '=', 'xoops2_groups_users_link.uid'));
        $this->row = $table->where('uname', $id)->findOne();
        if (is_bool($this->row))
            throw new NoUserException();
        return $this;
    }

    public function getUsername()
    {
        if ($name = $this->row->name)
            return $name;
        else
            return false;
    }

    public function getUID()
    {
        if ($uid = $this->row->uid)
            return $uid;
        else
            return false;
    }

    public function getMail()
    {
        if ($email = $this->row->email)
            return $email;
        else
            return false;
    }

    public function getRank()
    {
        if ($gid = $this->row->groupid)
            return $gid;
        else
            return false;
    }

}

