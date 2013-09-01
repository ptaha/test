<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ptaha
 * Date: 25.08.13
 * Time: 14:41
 * To change this template use File | Settings | File Templates.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Home extends CI_Controller {
    function __construct(){
        parent::__construct();
    }

    function index(){
        if($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $this->load->view('home_view', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    function logout(){
        $this->session->unset_userdata('logged_in');
        session_destroy();
        redirect('home', 'refresh');
    }

    /**
     * Get images
     */
    function getImages(){
        $this->load->model('imagemodel','', TRUE);
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        if($start !== false && $limit !== false) {
            $user_data = $this->session->userdata('logged_in');
            echo json_encode($this->imagemodel->loadImages($user_data['id'],$start, $limit));
        } else {
            echo json_encode(array($start === false ? true: false,$limit === false ? true : false));
        }
    }
    function deleteImages() {
        $imagesIds = $this->input->post('images');
        $this->load->model('imagemodel','',TRUE);
        if($imagesIds != false){
            $this->imagemodel->deleteImages(json_decode($imagesIds));
            echo json_encode(array(
                'success' => true
            ));
        } else {
            echo json_encode(array(
               'success' => false
            ));
        }

    }
    function imageUpload(){
        $this->load->model('imagemodel','',TRUE);
        $files = isset($_FILES['photo-path']) ?  $_FILES['photo-path'] : null;
        if($files == null) {
            echo json_encode(array(
                "success" => false,
                "message" => 'Files not loaded.'
            ));
            return;
        }
        $names = $files['name'];
        $tmp_names = $files['tmp_name'];
        $error = $files['error'];
        $types = $files['type'];
        $sizes = $files['size'];

        $files_count = \count($names);
        $answer = array();
        $uploaded_count = 0;
        for($i = 0; $i < $files_count; $i++){
            if($error[$i] == 0) { // normal file upload
                $timestamp = \strtotime('now');
                $uploadfile = UPLOAD_DIR . $timestamp.'_'.basename($names[$i]);
                if(move_uploaded_file($tmp_names[$i], $uploadfile)) {
                    $uploaded_count++;
                    //save record to database
                    $user_data = $this->session->userdata('logged_in');
                    if($user_data == FALSE) {
                        continue;
                    }
                    $img_id = $this->imagemodel->saveImage(array(
                        'user' => $user_data['id'],
                        'image_name' => $names[$i],
                        'type' => $types[$i],
                        'date' => $timestamp,
                        'full_name' => UPLOAD_DIR . $timestamp.'_'.basename($names[$i])
                    ));
                    //prepare answer
                    $answer[] = array(
                        'image_id' => $img_id,
                        'image_name' => $names[$i],
                        'image_type' => $types[$i],
                        'image_size' => $sizes[$i],
                        'image_date' => date('d-m-Y, H:i:s',$timestamp)
                    );
                }

            }
        }
        if($uploaded_count>0){
            echo json_encode(
                array(
                    'success' => true,
                    'count' => $uploaded_count,
                    'store' => $answer,
                    "message" => 'Files loaded.'
                )
            );
        } else {
            echo json_encode(
                array(
                    'success'=> false,
                    "message" => 'Files not loaded.'
                )
            );
        }
    }

    /**
     * Check access to image
     */
    function imageAccess() {
        $id = $this->input->get('id',true);
        if($id!==false) {
            $this->load->model('imagemodel','',TRUE);
            $imageData = $this->imagemodel->getImageById($id);
            if($imageData === false || !$this->session->userdata('logged_in')){
                show_404();
            } else {
                $logged_in = $this->session->userdata('logged_in');
                if($logged_in['id'] != $imageData['user_id']) {
                    show_404();
                } else {
                    $this->load->view('image_access',array(
                        'image'=>$imageData['full_name'],
                        'username' => $logged_in['username']
                    ));
                }
            }
        }
    }
}

