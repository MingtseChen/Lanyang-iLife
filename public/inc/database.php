<?php
include_once(__DIR__."/../../vendor/autoload.php");

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

    public function fetch($id)
    {
        $table = ORM::for_table('xoops2_users');
        $this->row = $table->where('uname', $id)->findOne();
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
}
$find = new Finder;
echo $find->fetch(403840308)->getUsername();
