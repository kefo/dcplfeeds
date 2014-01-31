<?php

class Runs_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
		$this->load->helper('shared_funcs');
	}
	
	public function get_all()
	{
		$query = $this->db->get('runs');
		$results = $query->result_array();
		for ($i=0;$i<count($results);$i++) {
			$results[$i] = run_augment($results[$i]);
		}
		//echo "<pre>";
		//print_r($results);
		//echo "</pre>";
		//exit;
		return $results;
	}
	
}
