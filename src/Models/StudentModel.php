<?php

class Student
{
    private $row;

    public function fetch($id)
    {
        $table = ORM::forTable('students')->where('uname', $id)->findArray();
        if (!empty($table)) {
            $this->row = $table[0];
        } else {
            $this->row = false;
        }
        return $this;
    }

    public function getUsername()
    {
        if ($this->row) {
            //remove space
            $name = $this->row['name'];
            $trimmedName = trim($name, '　');
            return trim($trimmedName);
        } else {
            return false;
        }
    }

    public function getUID()
    {
        if ($this->row) {
            $uid = $this->row['uname'];
            return $uid;
        } else {
            return false;
        }
    }

    public function getPrimaryMail()
    {
        if ($this->row) {
            $email = $this->row['secondary_email'];
            if ($email == '') {
                $email = $this->row['email'];
            }
            return $email;
        } else {
            return false;
        }
    }

    public function getMail()
    {
        if ($this->row) {
            $email = $this->row['email'];
            return $email;
        } else {
            return false;
        }
    }

    public function getSecondaryMail()
    {
        if ($this->row) {
            $email = $this->row['secondary_email'];
            return $email;
        } else {
            return false;
        }
    }

    public function addSecondaryMail($id, $email)
    {
        $record = ORM::forTable('students')->where('uname', $id)->findOne();
        $record->set('secondary_email', $email);
        $record->save();
    }

    public function read()
    {
        return ORM::forTable('students')->findArray();
    }
}
