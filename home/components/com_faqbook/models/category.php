<?php
/**
* @title   Minitek FAQ Book
* @version   1.5.2
* @copyright   Copyright (C) 2011-2012 Minitek, All rights reserved.
* @license   GNU General Public License version 2 or later.
* @author url   http://www.minitek.gr/
* @author email   info@minitek.gr
* @developer   Ioannis Maragos - minitek.gr
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
 
/**
 * Category Model
 */
class FaqBookModelCategory extends JModelItem
{ 
	//var $_data = null;
	var $_subcategories = null;
	
	function __construct() {
	
		parent::__construct();
		
	}
	
	/**
	 * Method to check Parent access
	 * @access public
	 * @return array
	 */
	function checkParentAccess($id) {
		
		$db =& $this->getDBO();			
		$subwhere = $this->_checkParentAccessSubwhere($id);	
		$app =& JFactory::getApplication();
    
		// Query index Itemid
		$query = " SELECT id, link "
					  ." FROM #__menu "
					  ." WHERE link = 'index.php?option=com_faqbook&view=faqbook' "
						." AND published = 1 ";	
		$db =& JFactory::getDBO();
	  $db->setQuery( $query );		
		$row = $db->loadObject();	
		if ($row) {	
		  $index_itemid = $row->id;
		  $link = JRoute::_('index.php?option=com_faqbook&view=faqbook&Itemid='.$index_itemid);
		} else {
		  $link = JRoute::_('index.php?option=com_faqbook&view=faqbook');
	  }
		
		$query = "SELECT * FROM #__categories WHERE id=".$id." ";			
		$db->setQuery( $query );
		$parent_exists = $db->loadObject();
		
		if ($parent_exists) {	
		
		  $query = "SELECT * FROM #__categories ". $subwhere ." ";			
		  $db->setQuery( $query );
		  $checkaccess = $db->loadObject();
		
      if ($checkaccess) {
		    $has_access = $checkaccess->access;
		    return $has_access;	
		  } else {  
		    $msg = JText::_( 'NO_ACCESS_TO_CATEGORY' );
			  $app->redirect(str_replace('&amp;', '&', $link), $msg);	
		  }
		
		} else {
		  $msg = JText::_( 'INVALID_CATEGORY_ID' );
			$app->redirect(str_replace('&amp;', '&', $link), $msg);	
			
		}
			
	}
	
	/**
	 * Method to check Parent access subwhere
	 * @access public
	 * @return array
	 */
	function _checkParentAccessSubwhere($id) {
		
		$subwhere = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$subwhere[] = 'access IN ('.$groups.')';
		//}
		
		$subwhere[] = "id = ".$id;
		$subwhere[] = 'extension = "com_faqbook" ';
		$subwhere[] = 'published = 1';
				
		$subwhere 		= ( count( $subwhere ) ? ' WHERE ' . implode( ' AND ', $subwhere ) : '' );

		return $subwhere;	
	}
	
	/**
	 * Method to get Subcategories
	 * @access public
	 * @return array
	 */
	function getSubCategories($id) {
   
		$db =& $this->getDBO();			

		$subwhere = $this->_buildSubwhere($id);	

		$query = "SELECT * FROM #__categories ". $subwhere ." ORDER BY rgt ";		
		
		$db->setQuery( $query );
		$subcategories = $db->loadObjectList();

		return $subcategories;		
	}
	
	/**
	 * Method to get Query Subwhere
	 * @access public
	 * @return array
	 */
	function _buildSubwhere($id) {
		
		$subwhere = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$subwhere[] = 'access IN ('.$groups.')';
		//}
		
		$subwhere[] = "parent_id = ".$id;
		$subwhere[] = 'extension = "com_faqbook" ';
		$subwhere[] = 'published = 1';
				
		$subwhere 		= ( count( $subwhere ) ? ' WHERE ' . implode( ' AND ', $subwhere ) : '' );

		return $subwhere;	
	}
	
	/**
	 * Method to get Subcategory Items
	 * @access public
	 * @return array
	 */
	function getSubCategoryItems($id) {
   
		$db =& $this->getDBO();			

		$itemsubwhere = $this->_buildItemSubwhere($id);	

		$query = "SELECT * FROM #__faqbook_items ". $itemsubwhere ." ORDER BY ordering ";		
		
		$db->setQuery( $query );
		$faqs = $db->loadObjectList();

		return $faqs;		
	}
	
	/**
	 * Method to get Query ItemSubwhere
	 * @access public
	 * @return array
	 */
	function _buildItemSubwhere($id) {
		
		$itemsubwhere = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$itemsubwhere[] = 'access IN ('.$groups.')';
		//}
		
		$itemsubwhere[] = "catid = ".$id;
		$itemsubwhere[] = 'published = 1';
				
		$itemsubwhere 		= ( count( $itemsubwhere ) ? ' WHERE ' . implode( ' AND ', $itemsubwhere ) : '' );

		return $itemsubwhere;	
	}
	
	/**
	 * Method to get Query Parent Category Name
	 * @access public
	 * @return array
	 */
	
	function getCategoryName($id) {
   
		$db =& $this->getDBO();			

		$query = "SELECT * FROM #__categories WHERE id=$id ";		
		
		$db->setQuery( $query );
		$categoryname = $db->loadObject();

		return $categoryname;		
	}
	
	/**
	 * Method to store Faq Rating
	 * @access public
	 * @return array
	 */
	function storeRating($id, $type) {
		$db = &JFactory::getDBO();
		$user = JFactory::getUser();

		if ($type == 1) {
			$query = 'update #__faqbook_items set votes_up = votes_up +1 where id=' . $id;
			$db->setQuery($query);
			$db->query();
    }
		if ($type == 0) {
		  $query = 'update #__faqbook_items set votes_down = votes_down +1 where id=' . $id;
			$db->setQuery($query);
			$db->query();
    }
			
	}
	
	/**
	 * Method to store New Faq
	 * @access public
	 * @return array
	 */
	function store()
	{
	
		$row =& $this->getTable();
		$data = JRequest::get( 'post' );
		$params = &JComponentHelper::getParams( 'com_faqbook' );
		
		
		// Send email notification for new faq
		if ($params->get( 'send_email' )) {
		$mailer =& JFactory::getMailer();
		$config =& JFactory::getConfig();
    $sender = array( 
    $config->getValue( 'config.mailfrom' ),
    $config->getValue( 'config.fromname' ) );
    $mailer->setSender($sender);
		
    $recipient = $params->get( 'write_email' );
    $mailer->addRecipient($recipient);
		
		$body   = "A new FAQ has been submitted to your website. Please login at your website Administration area to check it out.";
		$mailer->setSubject('New FAQ submitted');
		$mailer->setBody($body);
		
		$send =& $mailer->Send();
		if ( $send !== true ) {
    //echo 'Error sending email: ';
		} else {
    //echo 'Mail sent';
		}
		}
		
		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the  record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			return false;
		}

		return true;
	}
	
	/* Function to get Index Itemid 
	*/
	
	function getIndexItemid() {
   
		// Query index Itemid
		$query = " SELECT id, link "
					  ." FROM #__menu "
					  ." WHERE link = 'index.php?option=com_faqbook&view=faqbook' "
						." AND published = 1 ";	
		$db =& JFactory::getDBO();
		$db->setQuery( $query );		
		$row = $db->loadObject();	
		if ($row) {	
		$index_itemid = $row->id;
    
		return $index_itemid;		
		}
	}
		
	/* Function to submit a new FAQ in subcategory
	*/
	
	function getSubmitSubcategory($this_catid) {
   
		// Query subcategories to insert in form select box																
		$db =& $this->getDBO();			
    
		$submitsubwhere = $this->_buildSubmitSubwhere($this_catid);	

		$query = "SELECT * FROM #__categories ". $submitsubwhere ." ";		
		
		$db->setQuery( $query );
		$submitsubcats = $db->loadObjectList();

		return $submitsubcats;		
	}	
	
	function _buildSubmitSubwhere($this_catid) {
		
		$subwhere = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$submitsubwhere[] = 'access IN ('.$groups.')';
		//}
		
		$submitsubwhere[] = "parent_id = ".$this_catid;
		$submitsubwhere[] = 'extension = "com_faqbook" ';
		$submitsubwhere[] = 'published = 1';
				
		$submitsubwhere 		= ( count( $submitsubwhere ) ? ' WHERE ' . implode( ' AND ', $submitsubwhere ) : '' );

		return $submitsubwhere;	
	}
		
}