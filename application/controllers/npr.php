<?php

class NPR extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function stemtostern($feedname) {
	    $this->retrieve($feedname, false);
	    $this->process($feedname, "", false);
	    $this->email($feedname, "", false);
	}
	
	public function retrieve($feedname="", $doview=true) {
	    
	    $this->load->database();
	    
	    $feed = false;
	    if ($feedname == "dianerehm") {
	        $feed = file_get_contents('http://www.npr.org/rss/podcast.php?id=510071');
	        $feed = str_replace('<![CDATA[', '', $feed);
	        $feed = str_replace(']]>', '', $feed);
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
            
            $feeddata = array();
            
            $doc = new DOMDocument;
            // We don't want to bother with white spaces
            $doc->preserveWhiteSpace = false;
            $doc->loadXML($feed);
            $xpath = new DOMXPath($doc);
            $xpath->registerNamespace ( "atom", "http://www.w3.org/2005/Atom" );
            $xpath->registerNamespace ( "npr", "http://www.npr.org/rss/" );
            $xpath->registerNamespace ( "nprml", "http://api.npr.org/nprml" );
            $xpath->registerNamespace ( "itunes", "http://www.itunes.com/dtds/podcast-1.0.dtd" );
            $xpath->registerNamespace ( "content", "http://purl.org/rss/1.0/modules/content/" );

            // We starts from the root element
            $query = '//rss/channel/item';
            $entries = $xpath->query($query);
            foreach ($entries as $entry) {
                $fd = array();
                foreach ($entry->childNodes as $e) {
                    if ($e->nodeName == "title") {
                        $fd["title"] = $e->nodeValue;
                    } else if ($e->nodeName == "pubDate") {
                        $fd["pubDate"] = $e->nodeValue;
                    } else if ($e->nodeName == "description") {
                        $fd["description"] = $e->textContent;
                    } else if ($e->nodeName == "guid") {
                        $fd["link"] = $e->nodeValue;
                        $fd["entryID"] = $e->nodeValue;
                    }
                }
                $block = "Title: " . $fd["title"] . "\n";
                $block .= "Date: " . date("Y-m-d", strtotime($fd["pubDate"])) . "\n";
                $block .= "Description: " . $fd["description"] . "\n";
                $block .= "Link: " . $fd["link"] . "\n\n";
                $fd["block"] = $block;
                array_push($feeddata, $fd);
            }
            //echo "<pre>";
            //print_r($feeddata);
            //echo "</pre>";
            //exit;
            
            foreach ($feeddata as $fd) {
                $query = $this->db->get_where('data', array('manageID' => $manageID, 'entryID' => $fd["entryID"]), 1);
                if ($query->num_rows() == 0) {
                    $d = array(
                        'manageID' => $manageID,
                        'entryID' => $fd["entryID"],
                        'data' => $fd["block"]
                    );
                    $this->db->insert('data', $d);
                }
            }
            
            $this->db->update('manage', array('manageNew' => count($feeddata)), array('manageID' => $manageID));
            
            if ($doview) {
                if (isset($_GET["serialize"]) && $_GET["serialize"] == "text") {
                    header("Content-type: text/plain");
                    echo "Retrieving Feed: " . $feedname . "\nFeed ID: " . $manageID . "\nTotal in feed: " . count($addeduris) . "\n\n";
                } else {
                    $data['page_title'] = 'Retrieve feed: ' . $feedname;
		            $data['page_lead'] = '';
    		        $data['html'] = '
	    	            Feed: ' . $feedname . '<br />
		                Feed ID: ' . $manageID . '<br />
		                Total in feed: ' . count($feeddata) . '<br />
		                ';
		
    		        $this->load->view('templates/htmlhead', $data);
	    	        $this->load->view('templates/basic_page', $data);
		            $this->load->view('templates/htmlfoot');
                }
            }
	    }
	}
	
	public function process($feedname="", $manageID="", $doview=true) {
	    // not used, see email
	}
	
	
	
	public function email($feedname="", $manageID="", $doview=true) {
	    
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
                    
            $block = $row["data"];
            array_push($entries, $block);
	               
            //echo "<pre>";
	        //print ($entry);
	        //echo "</pre>";
        }
        
        
        $body = "No new entries found for NPR: $feedname. \n\n";
        if ( count($entries) > 0 ) { 
            $body = implode($entries, "\n\n");
        }
            
        echo "<pre>";
	    print ($body);
	    echo "</pre>";
	    
	    $to      = 'kford@3windmills.com, sford@3windmills.com';
        $subject = 'NPR Feed - ' . $feedname;
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