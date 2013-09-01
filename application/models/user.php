<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ptaha
 * Date: 25.08.13
 * Time: 14:38
 * To change this template use File | Settings | File Templates.
 */

Class User extends CI_Model
{
    function login($username, $password) {
        $this -> db -> select('id, username, password');
        $this -> db -> from('users');
        $this -> db -> where('username', $username);
        $this -> db -> where('password', MD5($password));
        $this -> db -> limit(1);

        $query = $this -> db -> get();

        if($query -> num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    function checkLogin($username) {
        $this -> db -> select('id, username, password')
                    -> from('users')
                    -> where('username', $username)
                    -> limit(1);
        $query = $this -> db -> get();

        if($query -> num_rows() == 1) {
            return true;
        } else {
            return false;
        }
    }

    function register($username, $password) {
        $success = $this -> db -> insert('users',
            array(
                'username' => $username,
                'password' => MD5($password)
            )
        );
        return $success == 1 ? $this->db->insert_id() : false;
    }

}
