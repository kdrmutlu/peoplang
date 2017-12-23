<?php 

class PostModel extends CI_Model
{
    function __construct() {
        parent::__construct();

        $this->load->model("UserControl");
    }

    
    public function insertPost($newPost)
    {
        $this->db->insert('Post', $newPost);
    }
}
