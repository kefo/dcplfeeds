<?php

class Feed extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function retrieve($feedname="") {
	    
	    $this->load->database();
	    
	    $feed = false;
	    if ($feedname == "owls") {
	        $feed = file_get_contents('https://catalog.dclibrary.org/client/rss/hitlist/dcpl/qu=owls&qf=SUBJECT%09Subject%09Owls+--+Fiction.%09Owls+--+Fiction.');
	        //echo $feed;
	    }
	    
	    if ($feed) {
	        $now = strtotime("now");
	        $retrieval_date = date("Y-m-d", $now);
	        $retrieval_st = date("Y-m-d H:i:s", $now);
	        
            $data = array(
                'manageFeedName' => $feedname,
                'manageRetrievalDate' => $retrieval_date,
                'manageStartDT' => $retrieval_st
            );
            $this->db->insert('manage', $data); 
            $manageID = $this->db->insert_id();
            
            $feeduris = array();
            
            $doc = new DOMDocument;
            // We don't want to bother with white spaces
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($feed);
            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace ( "atom", "http://www.w3.org/2005/Atom" );

            // We starts from the root element
            $query = '//atom:feed/atom:entry/atom:id';
            $entries = $xpath->query($query);
            foreach ($entries as $entry) {
                // echo $entry->nodeValue . "<br />";
                array_push($feeduris, $entry->nodeValue);
            }
            
            $addeduris = array();
            foreach ($feeduris as $u) {
                $query = $this->db->get_where('data', array('manageID' => $manageID, 'entryID' => $u), 1);
                if ($query->num_rows() == 0) {
                    $d = array(
                        'manageID' => $manageID,
                        'entryID' => $u
                    );
                    $this->db->insert('data', $d);
                    array_push($addeduris, $u);
                }
            }
            
            $this->db->update('manage', array('manageNew' => count($addeduris)), array('manageID' => $manageID));
            
            if (isset($_GET["serialize"]) && $_GET["serialize"] == "text") {
                header("Content-type: text/plain");
                echo "Retrieving Feed: " . $feedname . "\nFeed ID: " . $manageID . "\nTotal in feed: " . count($addeduris) . "\n\n";
            } else {
                $data['page_title'] = 'Retrieve feed: ' . $feedname;
		        $data['page_lead'] = '';
		        $data['html'] = '
		            Feed: ' . $feedname . '<br />
		            Feed ID: ' . $manageID . '<br />
		            Total in feed: ' . count($addeduris) . '<br />
		            ';
		
		        $this->load->view('templates/htmlhead', $data);
		        $this->load->view('templates/basic_page', $data);
		        $this->load->view('templates/htmlfoot');
            }
	    }
	}
	
	public function process($feedname="", $manageID="") {
	    
	    $this->load->database();

	    $feed = false;
	    $urisToProcess = array();
	    $query = "";
	    if ($manageID != "") {
	        $this->db->from('data');
	        $this->db->from('manage');
	        $this->db->where('data', NULL);
            $this->db->where('data.manageID = "' . $manageID . '"');
            $this->db->where('data.manageID = manage.manageID');
            $this->db->where('manage.manageFeedName = "' . $feedname . '"');
            $query = $this->db->get();
            //echo $this->db->last_query();
            //echo count($query->result_array());
            //exit;
	    } else {
	        //$query = $this->db->get_where('data', array('manageFeedName' => $feedname, 'manageID' => $manageID, 'data' => NULL), 5);
	        $this->db->from('data');
	        $this->db->from('manage');
	        $this->db->where('data', NULL);
            $this->db->where('manage.manageFeedName = "' . $feedname . '"');
            $this->db->where('data.manageID = manage.manageID');
            $query = $this->db->get();
            //echo $this->db->last_query();
            //echo count($query->result_array());
            //echo "<pre>";
            //print_r($query->result_array());
            //echo "</pre>";
            //exit;
	    }
	    foreach ($query->result_array() as $row) {
	        array_push($urisToProcess, $row["entryID"]);
	    }

        $processed = array();
	    if ( count($urisToProcess) > 0 ) {
	        foreach ($urisToProcess as $u) {
	            //echo $u . "<br />";
	            $uParts = explode(":", $u);
	            $catKey = end($uParts);
	            // echo $catKey . "<br />";
	            //$d = file_get_contents("http://dcpl.sirsi.net/dcpl_symws/rest/standard/lookupTitleInfo?clientID=DS_CLIENT&titleID=" . $catKey . "&includeOPACInfo=false&marcEntryFilter=all&json=false&callback=?");
	            $d = file_get_contents("http://dcpl.sirsi.net/dcpl_symws/rest/standard/lookupTitleInfo?clientID=DS_CLIENT&titleID=" . $catKey . "&includeOPACInfo=false&marcEntryFilter=all&json=true");
	            if ($d) {
	                $this->db->update('data', array('data' => $d), array('manageID' => $manageID, 'entryID' => $u));
	                array_push($processed, "Retrieved: " . $u);
	            } else {
	                array_push($processed, "FAILED: " . $u);
	            }
	            sleep(3);
	        }
	    }
	    
	    if (isset($_GET["serialize"]) && $_GET["serialize"] == "text") {
            header("Content-type: text/plain");
            echo 'Processing feed: ' . $feedname . "\n\n" . implode($processed, "\n") . "\n\n";
        } else {
            $data['page_title'] = 'Processing feed: ' . $feedname;
	        $data['page_lead'] = '';
	        $data['html'] = implode($processed, "<br />");
	        $this->load->view('templates/htmlhead', $data);
        	$this->load->view('templates/basic_page', $data);
	        $this->load->view('templates/htmlfoot');
            }
	}
	
	
	
	public function email($feedname="", $manageID="") {
	    
	    $this->load->database();

	    $feed = false;
	    $urisToProcess = array();
	    
	    $query = "";
	    if ($manageID != "") {
	        $this->db->from('data');
	        $this->db->from('manage');
	        $this->db->where('data IS NOT NULL');
            $this->db->where('data.manageID = "' . $manageID . '"');
            $this->db->where('data.manageID = manage.manageID');
            $this->db->where('manage.manageFeedName = "' . $feedname . '"');
            $this->db->where('manage.manageEmailed = "0"');
            $query = $this->db->get();
            //echo $this->db->last_query();
            //echo count($query->result_array());
            //exit;
	    } else {
	        //$query = $this->db->get_where('data', array('manageFeedName' => $feedname, 'manageID' => $manageID, 'data' => NULL), 5);
	        $this->db->from('data');
	        $this->db->from('manage');
	        $this->db->where('data IS NOT NULL');
            $this->db->where('manage.manageFeedName = "' . $feedname . '"');
            $this->db->where('data.manageID = manage.manageID');
            $this->db->where('manage.manageEmailed = "0"');
            $query = $this->db->get();
            //echo $this->db->last_query();
            //echo count($query->result_array());
            //echo "<pre>";
            //print_r($query->result_array());
            //echo "</pre>";
            //exit;
	    }

        $manageIDs = array();
        $entries = array();
        //$results = $query->result_array();
        foreach ($query->result_array() as $row) {
            // This is for later.
            if (!in_array($row["manageID"], $manageIDs)) {
                array_push($manageIDs, $row["manageID"]);
            }
                    
            $obj = json_decode($row["data"], true);
	        //echo "<pre>";
	        //print_r($obj);
	        //echo "</pre>";
	        
	        $uTitle = "";
	        $mainTitle = "";
	        $contributors = "";
	        $pubdate = "";
	        $description = "";
	        $url = "https://catalog.dclibrary.org/client/en_US/dcpl/search/detailnonmodal?d=" . $row["entryID"] . "~ILS~0~72"; // ent%3A%2F%2FSD_ILS%2F278%2FSD_ILS%3A278478~ILS~0~72
	        foreach ($obj["TitleInfo"][0]["BibliographicInfo"]["MarcEntryInfo"] as $d) {
                if (
                    $d["entryID"] == "100" ||
	                $d["entryID"] == "110" ||
	                $d["entryID"] == "700" ||
	                $d["entryID"] == "710" ||
	                $d["entryID"] == "511" ||
	                $d["entryID"] == "508"
	                ) {
                    $contributors = $d["text"] . "; ";
                }
	            if ($d["entryID"] == "240") {
	                $uTitle = $d["text"];
	            }
	            if ($d["entryID"] == "245") {
	                $mainTitle = $d["text"];
	            }
	            if ($d["entryID"] == "260") {
	                $pubdate = $d["text"];
	            }
	            if ($d["entryID"] == "520") {
	                $description = $d["text"];
	            }
	        }
	        $entryA = array(
	            "Title: $mainTitle",
	            // "Uniform Title: $uTitle",
	            "Contributors: $contributors",
	            "Publication: $pubdate",
	            "Description: $description",
	            "URL: $url"
	            );
	                
	        $entry = implode($entryA, "\n");
            array_push($entries, $entry);
	               
            //echo "<pre>";
	        //print ($entry);
	        //echo "</pre>";
        }
        
        
        $body = "No new entries found for $feedname. \n\n";
        if ( count($entries) > 0 ) { 
            $body = implode($entries, "\n\n");
        }
            
        echo "<pre>";
	    print ($body);
	    echo "</pre>";
	    
	    $to      = 'kford@3windmills.com';
        $subject = 'DCPL Feed - ' . $feedname;
        $message = $body;
        $headers = 'From: feeds@3windmills.com' . "\r\n" .
                'Reply-To: kford@3windmills.com';
        $success = mail($to, $subject, $message, $headers);
        //$success = true;
        if ($success) {
            // manageIDs was gathered above.
            foreach ($manageIDs as $mID) {
                $this->db->update('manage', array('manageStatus' => '2', 'manageEmailed' => '1'), array('manageID' => $mID));
            }
        } else {
            echo "\n\nEmail failed.";
        }
	    
	    // Need to email the results and 
	    // then set manageEmail to 1
	    // and status to 1
	    

	}
	
}

?>