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
 * FaqBook Model
 */
class FaqBookModelFaqBook extends JModelItem
{ 
	var $_data = null;
	var $_subcategories = null;
	var $_popular_faqs 		= null;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * Method to get Categories
	 * @access public
	 * @return array
	 */
	function getData() {
   
		$db =& $this->getDBO();			

		$where = $this->_buildWhere();	

		$query = "SELECT * FROM #__categories ". $where ." ORDER BY rgt ";		
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		return $rows;
		
	}
	
	/**
	 * Method to get Query Where
	 * @access public
	 * @return array
	 */
	function _buildWhere() {
		
		$where = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$where[] = 'access IN ('.$groups.')';
		//}
		
		$where[] = 'parent_id = 1';
		$where[] = 'extension = "com_faqbook" ';
		$where[] = 'published = 1';
				
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;	
	}
	
	/**
	 * Method to get Subcategories
	 * @access public
	 * @return array
	 */ 
	 function getIndexSubCategories($id){
			
			$db =& $this->getDBO();		
			
			$subwhere = $this->_buildSubWhere($id);	

		  $query = "SELECT * FROM #__categories ". $subwhere ." ORDER BY rgt ";		
			
			$db->setQuery( $query );
		  $subcategories = $db->loadObjectList();
		
		return $subcategories;
	}
	 
	/**
	 * Method to get Query SubWhere
	 * @access public
	 * @return array
	 */
	function _buildSubWhere($id) {
		
		$subwhere = array();
		
		// Filter by access level.
		//if ($access = $this->getState('filter.access')) {
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$subwhere[] = 'access IN ('.$groups.')';
		//}
		
		$subwhere[] = 'extension = "com_faqbook" ';
		$subwhere[] = 'published = 1';
		$subwhere[] = "parent_id = ".$id;
					
		$subwhere 		= ( count( $subwhere ) ? ' WHERE ' . implode( ' AND ', $subwhere ) : '' );

		return $subwhere;	
	}
	
}