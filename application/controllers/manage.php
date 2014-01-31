<?php

class Manage extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}

    /*
        This creates two tables.  
            1) The management table
            2) The data table
        
        ID, date of retrieval, date/time started, date/time finished, status, number of new records, emailed
        Management table:
            CREATE TABLE IF NOT EXISTS `manage` (
                `manageID` int(11) NOT NULL auto_increment,
                `manageRetrievalDate` date NOT NULL,
                `manageStartDT` datetime NOT NULL,
                `manageEndDT` datetime NULL,
                `status` char(10) NULL,
                `new` int(4) NULL,
                `emailed` int(1) DEFAULT 0,
                PRIMARY KEY  (`manageID`),
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE="utf8_general_ci" AUTO_INCREMENT=1 ;
            
        ID, SirsiID, data
            CREATE TABLE IF NOT EXISTS `data` (
                `dataID` int(11) NOT NULL auto_increment,
                `manageID` int(11) NOT NULL,
                `sirsiID` char(40) NOT NULL,
                `data` TEXT NOT NULL,
                PRIMARY KEY  (`dataID`),
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE="utf8_general_ci" AUTO_INCREMENT=1 ;
    */
	public function instantiate()
	{
	    $this->load->database();
	    $this->load->dbforge();
	    
	    $manage_fields = array(
            'manageID' => array(
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'manageRetrievalDate' => array(
                'type' =>'DATE'
            ),
            'manageStartDT' => array(
                'type' =>'DATETIME'
            ),
            'manageEndDT' => array(
                'type' =>'DATETIME',
                'null' => TRUE,
                'default' => '000000T000000'
            ),
            'manageStatus' => array(
                'type' =>'CHAR',
                'constraint' => '10',
                'null' => TRUE
            ),
            'manageNew' => array(
                'type' =>'INT',
                'constraint' => '4',
                'default' => '0'
            ),
            'manageEmailed' => array(
                'type' =>'INT',
                'constraint' => '1',
                'default' => '0'
            )
        );
        
        $this->dbforge->add_field($manage_fields);
        $this->dbforge->add_key('manageID');
        $manage_table = $this->dbforge->create_table('manage', TRUE); // TRUE will check if it exists
        
        
        $data_fields = array(
            'dataID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'manageID' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'entryID' => array(
                'type' =>'CHAR',
                'constraint' => '40'
            ),
            'data' => array(
                'type' =>'TEXT',
                'null' => TRUE
            )
        );
        
        $this->dbforge->add_field($data_fields);
        $this->dbforge->add_key('dataID');
        $data_table = $this->dbforge->create_table('data', TRUE); // TRUE will check if it exists
        
		$data['page_title'] = 'Instantiate DB';
		$data['page_lead'] = 'Installation time.';
		
		$data['manage_table'] = $manage_table;
		$data['data_table'] = $data_table;
                
		$this->load->view('templates/htmlhead', $data);
		$this->load->view('templates/instantiate', $data);
		$this->load->view('templates/htmlfoot');
	}
	
	
	public function reset()
	{
	    $this->load->database();
	    $this->load->dbforge();

        $this->db->truncate("manage");
        $this->db->truncate("data");
        
		$data['page_title'] = 'Reset DB';
		$data['page_lead'] = 'Resetting time.';
		
		$data['reset'] = 1;
                
		$this->load->view('templates/htmlhead', $data);
		$this->load->view('templates/reset', $data);
		$this->load->view('templates/htmlfoot');
	}
	
}


?>