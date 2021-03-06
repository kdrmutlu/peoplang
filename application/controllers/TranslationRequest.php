<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TranslationRequest extends CI_Controller {
	
	function __construct() {
        parent::__construct();
        $this->load->model("UserControl");
        $this->load->model("TranslationModel");
        $this->load->model("SettingsModel");
    }

	public function index($trID = null)
	{
        $userID = get_cookie("User");
        if ($userID == null) {
			redirect("/landing");
		}

        $user = $this->UserControl->getUserByID($userID);
		if($user->registerStatus == "f"){
			redirect("/settings");
		}
        $userInformation = $this->SettingsModel->getProfile($userID);

        $data["name"] = $userInformation["name"];
        $data['content'] = "translationDetail/index";
        $data["translationRequest"] = $this->TranslationModel->getTR($trID);
        $data["answers"] = $this->TranslationModel->getAnswers($trID);
        $data["avatar"] = $this->UserControl->getUserAvatar($data["translationRequest"]->userID);
        $data["visitorAvatar"] = $this->UserControl->getUserAvatar($userID);
        $data["counterAnswers"] = count($data["answers"]);

        $this->load->view('layouts/appLayout', $data);
    }

    public function saveAnswer()
    {        
        $answer = $this->input->post("answer");
        $dt = time();
        $userID = get_Cookie("User");

        $newAnswer = array(
            "userID" 	 => $userID,
            "date"       => $dt,
            "text"       => $answer["answer"],
            "questionID" => $answer["questionID"]
        );

        $this->TranslationModel->insertAnswer($newAnswer);

        $question = $this->db->get_where("TranslationRequests", array("ID" => $answer["questionID"]));
        $question = $question->first_row();

        if ($userID != $question->userID)
        {
            $notif = array(
                "userID" => $userID,
                "nUserID" => $question->userID,
                "notification" => " responded to the request for translation.",
                "read" => false
            );
    
            $this->db->insert("Notifications", $notif);
        }
    }
    
    public function getAnswers()
    {
        $questionID = $this->input->post("questionID");

        $answers = $this->TranslationModel->getAnswers($questionID["qID"]);

        echo json_encode($answers);
    }
}