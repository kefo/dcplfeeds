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
                'manageRetrievalDate' => $retrieval_date,
                'manageStartDT' => $retrieval_st
            );
            $this->db->insert('manage', $data); 
            $manageID = $this->db->insert_id();
            echo "Manage ID is: " . $manageID . "<br />";
            
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
            
	    }
	}
	
	public function process($feedname="", $manageID="") {
	    
	    $this->load->database();

	    $feed = false;
	    $urisToProcess = array();
	    if ($feedname == "owls") {
	        
	        $query = $this->db->get_where('data', array('manageID' => $manageID, 'data' => NULL), 5);
	        foreach ($query->result_array() as $row) {
	            array_push($urisToProcess, $row["entryID"]);
	        }
	    }

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
	                echo "Retrieved " . $u . "<br />";
	                sleep(3);
	            }
	        }
	    }
	}
	
	public function email($feedname="", $manageID="") {
	    
	    $this->load->database();

	    $feed = false;
	    $urisToProcess = array();
	    if ($feedname == "owls") {

	        $query = $this->db->get_where('data', array('manageID' => $manageID, 'data IS NOT NULL' => NULL), 3);
            
            $entries = array();
	        foreach ($query->result_array() as $row) {
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
        
        $body = implode($entries, "\n\n");
        echo "<pre>";
	    print ($body);
	    echo "</pre>";
        
        }

	}
	
}

?>