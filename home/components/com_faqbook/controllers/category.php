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

// import Joomla controller library
jimport('joomla.application.component.controller');
 
/**
 * FAQ Book Component Controller
 */
class FaqBookControllerCategory extends JController {

 		function __construct() {		
			parent::__construct();	
			$this->registerTask('thumbup', 'ajxThumbUp');
		  $this->registerTask('thumbdown', 'ajxThumbDown');	
			$this->registerTask('save', 'save');	
	  }
		
		function ajxThumbUp(){
		  $this->ajxStoreRating(1);
		}
		
		function ajxThumbDown(){
		  $this->ajxStoreRating(0);
	  }

		private function ajxStoreRating($type){
		$user = &JFactory::getUser();
		
		$id = JRequest::getVar('id',0,'','INT');
			if($id){
				$model = &$this->getModel('category');
				$data = $model->storeRating($id, $type);
				if($data){
					echo json_encode(array('rating'=>$data));
				}else{
					$error = $model->getError();		
			  }
		  }
		
		jexit();
		}
	
	function save() {
		
		$model = $this->getModel('category');		
		if ($model->store($post)) {					
			$msg = JText::_( 'ADD_FAQ_OK');									
		} else {	
			$msg = JText::_( 'ADD_FAQ_ERROR' );
		} 
	
		$uri = & JFactory::getURI();
		$current = $uri->toString();
		$link = $current;
		$this->setRedirect($link, $msg);	
	
	}
	
}