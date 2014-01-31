<?php

class User_model extends CI_Model {
	
    /*
    CREATE TABLE IF NOT EXISTS `users` (
        `userID` int(11) NOT NULL auto_increment,
        `userEmail` varchar(50) NOT NULL,
        `userPassword` char(32) NOT NULL,
        `userLastLogin` datetime NULL,
        `userInfoUpdated` timestamp NOT NULL default CURRENT_TIMESTAMP,
        PRIMARY KEY  (`userID`),
        UNIQUE KEY `userEmail` (`userEmail`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE="utf8_general_ci" AUTO_INCREMENT=1 ;
    */

    public function __construct()
    {
        $this->load->database();
    }
	
    public function add_user($userEmail, $uPassword)
    {
        
        $data = array(
            'userEmail' => $userEmail,
            'userPassword' => md5($uPassword),
            '$userLastLogin' => "0000-00-00 00:00:00"
        );

        return $this->db->insert('users', $data);
    }
        
    public function get_user($userID = FALSE)
    {
        if ($userID === FALSE) return false;
	$query = $this->db->get_where('runs', array('userID' => int($userID)));
	$results = $query->row_array();
	return $results;
    }
    
    public function login_user($userEmail, $uPassword)
    {
        if ($userEmail === FALSE || $uPassword == FALSE) return false;
        $data = array('userEmail' => $userEmail, 'userPassword' => md5($uPassword));
	//$query = $this->db->get_where('users', $data);
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->where('userEmail', $userEmail);
        $query = $this->db->where('userPassword', md5($uPassword));
        $query = $this->db->get();
	$results = $query->row_array();
        //print_r(count($results));
        //exit;
        if ( count($results) > 0) {
            $newdata = array(
                'userid'  => $results["userID"],
                'email'     => $results["userEmail"],
                'logged_in' => TRUE
            );
            $this->session->set_userdata($newdata);
            
            $this->db->update('users', array("userLastLogin" => date("Y-m-d H:i:s", strtotime("now"))));
            
            return true;
        }
	return false;
    }
    
    public function logout_user()
    {
       $this->session->sess_destroy();
    }

}

?>
