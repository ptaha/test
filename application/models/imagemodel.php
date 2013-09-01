<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ptaha
 * Date: 27.08.13
 * Time: 23:41
 * To change this template use File | Settings | File Templates.
 */
class Imagemodel extends CI_Model {
    /**
     * Save record in database
     * @param $imgData
     * @return bool
     */
    public function saveImage($imgData) {
        $success = $this -> db -> insert('images', array(
                'user_id' => $imgData['user'],
                'name' => $imgData['image_name'],
                'type' => $imgData['type'],
                'date' => \date("Y-m-d H:i:s",$imgData['date']),
                'full_name' => $imgData['full_name']
            )
        );
        return $success == 1 ? $this->db->insert_id() : false;
    }

    /**
     * Select images for user
     * @param $user_id
     */
    public function loadImages($user_id, $start, $limit){

        $this-> db -> select ('id, name, type, date, full_name')
                             -> from ('images')
                             -> where ('user_id',$user_id)
                             -> limit($limit, $start);
        $query = $this -> db -> get();
        $result = array(
            'images'=> array(),
            'totalCount' => 0
        );
        if($query->num_rows() > 0){
            $images = $query->result('array');
            foreach ($images as $image) {
                array_push($result['images'],array(
                    'id' => $image['id'],
                    'imagename' => $image['name'],
                    'imagetype' => $image['type'],
                    'date' => $image['date'],
                    'fullpath' => $image['full_name']
                ));
            }
            $totalCount = $this->getImageCount($user_id);

            $result['totalCount'] = isset($totalCount->count) ? $totalCount->count : 0;
        }
        return $result;
    }

    /**
     * Get count of images for the user
     * @param $user_id
     * @return bool
     */
    public function getImageCount($user_id) {
        $this-> db -> select ('count(id) as count')
            -> from ('images')
            -> where ('user_id',$user_id)
            -> limit(1, 0);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1) {
            $result = $query->result();
            return \current($result);

        } else {
            return false;
        }
    }

    /**
     * @param $imagesIds
     */
    public function deleteImages($imagesIds) {
        //delete images from the disc
        $this->db->from('images')
            ->where_in('id',$imagesIds);
        $query = $this->db->get();
        $results = $query->result('array');


        if(\count($results)>0){
            foreach($results as $result) {
                if(\file_exists($result['full_name'])) {
                    \unlink($result['full_name']);
                    $this->db->delete('images',array('id'=>$result['id']));
                }
            }
        } else {
            return false;
        }
    }

    /** get image info by id
     * @param $imageId
     * @return bool|mixed
     */
    public function getImageById($imageId) {
        $this->db->from('images')
            ->where('id',$imageId);
        $query = $this -> db -> get();
        if($query -> num_rows() == 1) {
            $result = $query->result('array');
            return \current($result);

        } else {
            return false;
        }
    }
}