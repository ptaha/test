<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ptaha
 * Date: 25.08.13
 * Time: 14:40
 * To change this template use File | Settings | File Templates.
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VerifyLogin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('user','',TRUE);
    }

    function index() {
        //This method will have the credentials validation
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

        if($this->form_validation->run() == FALSE) {
            //Field validation failed.
            //User redirected to login page
            $this->load->view('login_view');
        } else {
            //Go to private area
            redirect('home', 'refresh');
        }
    }

    /**
     * @param $password
     * @return bool
     */
    function check_database($password) {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');

        //query the database
        $result = $this->user->login($username, $password);

        if($result) {
            $sess_array = array();
            foreach($result as $row) {
                $sess_array = array(
                    'id' => $row->id,
                    'username' => $row->username
                );
                $this->session->set_userdata('logged_in', $sess_array);
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Invalid username or password.');
            return false;
        }
    }

    /**
     * Registration
     */
    function registration() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_already_registered');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_compare_passwords');

        if($this->form_validation->run() == FALSE) {
            $this->load->view('registration_view');
        } else {
            $user_id = $this->user->register($this->input->post('username'),$this->input->post('username'));
            if($user_id != false) {
               $sess_array = array(
                    'id' => $user_id,
                    'username' => $this->input->post('username')
               );
               $this->session->set_userdata('logged_in',$sess_array);
               redirect('home', 'refresh');
            } else {
                $this->form_validation->set_message('registration','Registration failed. Please, contact the administrator');
            }
        }
    }

    /**
     * @param $username
     * @return bool
     */
    function check_already_registered($username) {
        $already_registered = $this->user->checkLogin($username);
        if($already_registered) {
            $this->form_validation->set_message('check_already_registered', 'This user have already registered.');
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $password
     * @return bool
     */
    function compare_passwords($password) {
        $confirm = $this->input->post('password_confirm');
        $flag = \strcmp($confirm,$password);
        if($flag == 0) {
            return true;
        } else {
            $this->form_validation->set_message('compare_passwords', 'Password does not match the confirm password');
            return false;
        }

    }
}
