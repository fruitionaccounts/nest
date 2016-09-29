<?php
require_once(dirname(__FILE__) . '/infusionsoft-php-sdk/infusionsoft.base.class.php');

class MyInfusionsoft extends Infusionsoft {	
	public $user;
	public $contact;
	public $contact2;
	public $contact_is_new;
	public $auto_opt_in;
	public $action_sets;
	public $follow_up_seqs;
	public $tags;
	public $tag_categories;
	
	public function __construct($app_name, $api_key, $merchant_acct = false, $debug = false) {
		parent::__construct($app_name, $api_key, $merchant_acct, $debug);
		$this->user = null;
		$this->contact = null;
		$this->contact2 = null;
		$this->contact_is_new = false;
		$this->auto_opt_in = true;
		$this->tags = null;
		$this->tagCategories = null;
	}

  public function isInfusionsoftAppValid() {
    $valid = false;
    	
    try {
    	$lkpUser = $this->Contact();
			$lkpUser = $lkpUser->find_by_email('dummy@test.com');
			$valid = true;
   	} 
   	catch (Exception $e) {
   		//var_dump($e);
   	}
    	
   	return $valid;
	}
    
	public function authenticateAffiliate($username, $password) {
		$authenticated = false;
		
		if (strpos($username, '@') === false) {
			$data = $this->Data('Affiliate');
			$query = array('AffCode' => $username, 'Password' => $password);
			if ($affiliate = $data->query($query)) {
				$this->affiliate = $affiliate[0];
				$this->contact = $this->Contact($affiliate[0]->ContactId);
				$authenticated = true;
			}
		}
		else {
			$contact = $this->Contact();
		
			if ($contact = $contact->find_by_email($username)) {
				$data = $this->Data('Affiliate');
				$query = array('ContactId' => $contact[0]->Id, 'Password' => $password);
				if ($affiliate = $data->query($query)) {
					$this->affiliate = $affiliate[0];
					$this->contact = $contact[0];
					$authenticated = true;
				}
			}
		}
		    
		return $authenticated;
	}
	    
	public function authenticateUser($username, $password) {
		$lkpuser = $this->Contact();
		$lkpuser = $lkpuser->find_by_email($username);

		if (!empty($lkpuser) && $lkpuser[0]->Password == $password) {
			$this->user = $lkpuser[0];
		}
		
	    /*
	    $query = "select * from `ap_user` where user_name='$username' and user_password='$password'";
		$result = do_query($query);
		$row = do_fetch_result($result);
	
	    if(!empty($row['user_id'])){
			$user_id = $row['user_id'];
		}
		*/
	    
		return $this->user->Id;
	}
	
	public function add_contact($contact, $overwrite = true, $forceNew = false) {
		return $this->addContact($contact, $overwrite, $forceNew);	
	}
	
	public function addContact($contact, $overwrite = true, $forceNew = false) {
		$infusionContact = $this->Contact();

		if(! empty($contact['Email']) && ! $forceNew &&
			$cont = $infusionContact->find_by_email($contact['Email'])) {
				
			$this->attachCustomFields($cont[0], array_keys($contact));
			
			if ($overwrite) {
				$cont[0]->attach_custom_fields(array('Leadsource'));
				$cont[0]->add_values($contact);
	      $cont[0]->save();
			}
	    
			$this->contact = $cont[0];
		}
		else {
			$this->attachCustomFields($infusionContact, array_keys($contact));
			$infusionContact->attach_custom_fields(array('Leadsource'));
		  $this->contact = $infusionContact->add($contact);
		  $this->contact_is_new = true;
		}
		
		if ($this->auto_opt_in && ! empty($contact['Email'])) {
			$infusionEmail = $this->APIEmail();
			$infusionEmail->optIn($contact['Email']);
		}

		$this->contact->remove_fields(array('Leadsource'));
		return $this->contact->Id;
	}
	
	public function attachCustomFields(&$infusionContact, $fields) {
		$customFields = array();
		
		foreach($fields as $field) {
			if(strpos($field, '_') !== false)
				$customFields[] = $field;
		}
		
		$infusionContact->attach_custom_fields($customFields);
	}
	
	public function addNotes($notes, $description = '', $type = '') {
		if ($description == '')
			$description = sprintf('Message from %s %s', $this->contact->FirstName, $this->contact->LastName);
			
		$date = date('Ymd\TH:i:s', time());
		$fields = array('ContactId' => $this->contact->Id,
						'CompletionDate' => $date,
						'ActionDescription' => $description,
						'ActionType' => $type,
						'CreationNotes' => $notes);
		$data = $this->Data('ContactAction', $fields);
		$data->save();
	}

	public function run_action_set($action_set_id) {
		$this->runActionSet($action_set_id);
	}
	
	public function runActionSet($action_set_id) {
		$this->contact->run_action_sequence(intval($action_set_id));
	}
	
	public function run_action_sets($action_set_ids) {
		$this->runActionSets($action_set_ids);
	}
	
	public function runActionSets($action_set_ids) {
		foreach($action_set_ids as $action_seq_id) {
			$this->contact->run_action_sequence(intval($action_seq_id));
		}
	}
	
	public function addToCampaign($campaign_id) {
			$this->contact->add_to_campaign(intval($campaign_id));
	}
	
	public function addToCampaigns($campaign_ids) {
		foreach($campaign_ids as $campaign_id) {
			$this->contact->add_to_campaign(intval($campaign_id));
		}
	}

	public function removeFromCampaign($campaign_id) {
  	$this->contact->remove_from_campaign($campaign_id);
 	}
 
	public function removeFromCampaigns($campaign_ids) {
		foreach($campaign_ids as $campaign_id) {
			$this->contact->remove_from_campaign(intval($campaign_id));
		}
	}
	
	public function add_to_group($group_id) {
		$this->addToGroup($group_id);
	}
	
	public function addToGroup($group_id) {
		$this->contact->add_to_group(intval($group_id));
	}
	
	public function add_to_groups($group_ids) {
		$this->addToGroups($group_ids);
	}
	
	public function addToGroups($group_ids) {
		foreach($group_ids as $group_id) {
			$this->contact->add_to_group(intval($group_id));
		}
	}
	
	public function removeFromGroups($group_ids) {
		foreach($group_ids as $group_id) {
			$this->contact->remove_from_group(intval($group_id));
		}
	}

	public function loadContact($contactId, $customFields = array()) {
		$contact = $this->Contact();
		$contact->attachCustomFields($customFields);
		$contact->load($contactId);
		$this->contact = $contact;
	}
	
	public function loadUser($user_id) {
		//$custom_fields = array('ApplicationName', 'APIKey');
		$user = $this->Contact();
		//$user->attach_custom_fields($custom_fields);
		$user->load($user_id);
		$this->user = $user;
	}
	
	public function loadActionSets() {
		$data = $this->Data('ActionSequence');
		$query = array('Id' => '%');
		$this->action_sets = subval_sort($data->query($query), 'TemplateName');		
	}
	
	public function loadFollowUpSeqs() {
		$data = $this->Data('Campaign');
		$query = array('Status' => 'Active');
		$this->follow_up_seqs = subval_sort($data->query($query), 'Name');		
	}
	
	public function load_tags() {
		$this->loadTags();	
	}
	
	public function loadTags() {	
		$data = $this->Data('ContactGroup');
		$data->returnObjects = false;  // return arrays instead of objects
		$query = array('Id' => '%');
		$this->tags = $data->query($query);
		$this->insertCategoryName();
		$this->sortTags();
	}
	
	private function insertCategoryName() {
		$data = $this->Data('ContactGroupCategory');
		$data->returnObjects = false;  // return arrays instead of objects
		$query = array('Id' => '%');
		$result = $data->query($query);
		$numTags = count($this->tags);
		
		foreach($result as $cat) {
			$categories[$cat['Id']] = $cat['CategoryName'];
		}
		
		for ($i = 0; $i < $numTags; $i++) {
			if ($this->tags[$i]['GroupCategoryId'] > 0)
				$this->tags[$i]['CategoryName'] = $categories[$this->tags[$i]['GroupCategoryId']];
			else
				$this->tags[$i]['CategoryName'] = 'NA';
		}

		asort($categories);
		$this->tag_categories = $categories;
	}
	
	private function sortTags() {
		foreach($this->tags as $key => $value) {
			$categoryName[$key] = $value['CategoryName'];
			$groupName[$key] = $value['GroupName'];
		}
		
		array_multisort($categoryName, SORT_ASC, $groupName, SORT_ASC, $this->tags);
	}
	
	public function getTags($tagCategoryId = 0) {
		$data = $this->Data('ContactGroup');
		$data->returnObjects = false;  // return arrays instead of objects
		$query = array('Id' => '%');
		
		if ($tagCategoryId > 0) {
			$query['GroupCategoryId'] = $tagCategoryId;
		}
		
		$tags = array();
		$page = 0;
		
		while(1) {
			$temp = $data->query($query, $page);
			$tags = array_merge($tags, $temp);
			$page++;
		
			if(sizeof($temp) < 1000) break;
		}
		
		return $tags;
	}
	
	public function getTagId($tagName) {
		$data = $this->Data('ContactGroup');
		$query = array('GroupName' => $tagName);
		$result = $data->query($query);
		
		return $result[0]->Id;
	}
	
	public function createTag($tagName, $tagCategoryId = 0) {
		$fields = array(
			'GroupName' => $tagName,
			'GroupCategoryId' => $tagCategoryId);
		$data = $this->Data('ContactGroup', $fields);
		$tagId = $data->save();
		
		return $tagId;
	}
	
	public function findLatestRecord($list) {
		$count = count($list);
		
		if (!$count)
			return false;
			
		$id = (int) $list[0]->Id;
		$idIndex = 0;
			
		for ($i = 1; $i < $count; $i++) {
				$chkId = (int) $list[$i]->Id;
				
				if ($chkId > $id) {
					$id = $chkId;
					$idIndex = $i;
				}
		}
		
		return $list[$idIndex];
	}
	
	public function getReferralAffiliateId($contactId = 0, $referralPartnerChoice = 'last referring') {
		return $this->getReferralPartnerId($contactId, $referralPartnerChoice);
	}
	
	public function getReferralPartnerId($contactId = 0, $referralPartnerChoice) {
		$contactId = $contactId == 0 ? $this->contact->Id : $contactId;
		
		$infusionReferral = $this->Data('Referral');
		$infusionReferral->returnObjects = false;
		$referral = $infusionReferral->find_by_field('ContactId', $contactId);
		$refCount = count($referral);
		
		if ($refCount > 0) {
			$referralId = (int) $referral[0]['Id'];
			$referralIndex = 0;
	
			for ($i = 1; $i < $refCount; $i++) {
				$chkId = (int) $referral[$i]['Id'];
					
				if ($referralPartnerChoice == 'last referring' && $chkId > $referralId) {
					$referralId = $chkId;
					$referralIndex = $i;
				}
				else if ($referralPartnerChoice == 'first referring' && $chkId < $referralId) {
					$referralId = $chkId;
					$referralIndex = $i;
				}
			}
	
			return $referral[$referralIndex]['AffiliateId'];
		}
		
		return 0;
	}
}