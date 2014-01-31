<?php

class Runs extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('runs_model');
                $this->load->model('run_model');
                $this->load->library('session');
	}

        public function redirect()
	{
            header("Location: " . RELATIVEPATH . "runs/");
            exit;
	}
        
	public function all()
	{
		$data['runs'] = $this->runs_model->get_all();
		$data['page_title'] = 'All Runs';
                $data['userdata'] = $this->session->all_userdata();
                
		$this->load->view('templates/htmlhead', $data);
		$this->load->view('templates/runs', $data);
		$this->load->view('templates/htmlfoot');
	}
        
	public function processtcx()
	{   
            $uData = $this->session->all_userdata();
            if (!$uData["logged_in"]) {
                Header("Location: " . RELATIVEPATH . "manage/login?status=2");
                exit;
            }
            date_default_timezone_set('GMT');
            
            $tcxfiles = array();
            foreach (glob( DATAPATH . "tcx-unprocessed/*.tcx") as $filename) {
                $tcx = array(
                    "filename"=>$filename,
                    "filesize"=>filesize($filename)
                );
                //echo "Processing " . $run["filename"] . "\n";
	
                // http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2
                $xmldom = new DOMDocument();
                if ($xmldom->load( $filename )) {
                    $xp = new DOMXPath($xmldom);
                    $xp->registerNamespace('tcx' , 'http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2');

                    $result = $xp->query("/tcx:TrainingCenterDatabase/tcx:Activities/tcx:Activity/tcx:Id");
                    $eID = $result->item(0)->nodeValue;
                    $tcx["eID"] = $eID;
    	
                    $id = strftime("%Y%m%dT%H%M%S", strtotime($eID));
    	
                    $tcx["id"] = $id;
    	
                    $xmldom->save( DATAPATH . "tcx/" . $id . ".xml" );
                }
                array_push($tcxfiles, $tcx);
            }
                
            $data['tcxfiles'] = $tcxfiles;
            $data['page_title'] = 'Process TCX Files';
            $data['userdata'] = $this->session->all_userdata();
                
            $this->load->view('templates/htmlhead', $data);
            $this->load->view('templates/process-tcx', $data);
            $this->load->view('templates/htmlfoot');
	}

        public function unprocessedruns()
	{   
            $uData = $this->session->all_userdata();
            if (!$uData["logged_in"]) {
                Header("Location: " . RELATIVEPATH . "manage/login?status=2");
                exit;
            }
            date_default_timezone_set('GMT');
            
            $xmlfiles = array();
            foreach (glob( DATAPATH . "tcx/*.xml") as $filename) {
                $fparts = explode("/", $filename);
                $fname = end($fparts);
                $fname = str_replace(".xml", "", $fname);
                if (!$this->run_model->run_exists($fname)) {
                    $xmlfile = array(
                        "id"=>$fname,
                        "filename"=>$filename,
                        "filesize"=>filesize($filename)
                    );
                    //echo "Processing " . $run["filename"] . "\n";
                    array_push($xmlfiles, $xmlfile);
                }
            }
                
            $data['xmlfiles'] = $xmlfiles;
            $data['page_title'] = 'Unprocessedd Runs';
            $data['userdata'] = $this->session->all_userdata();
                
            $this->load->view('templates/htmlhead', $data);
            $this->load->view('templates/unprocessed-runs', $data);
            $this->load->view('templates/htmlfoot');
	}
}

?>
