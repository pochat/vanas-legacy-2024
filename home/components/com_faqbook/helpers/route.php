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

jimport('joomla.application.component.helper');

class FaqBookHelperRoute
{

	function getItemRoute($id, $catid = 0)
	{
		$link = 'index.php?option=com_faqbook&view=category&id='. $id;
		if($catid) {
			$link .= '&cid='.$catid;
		}
		if($item = FaqBookHelperRoute::_findItem((int)$id)) {
			$link .= '&Itemid='.$item->id;
		}
		else if ($item = FaqBookHelperRoute::_findCategory((int)$catid, 'items')) {
			$link .= '&Itemid='.$item->id;
		}
		else if ($item = FaqBookHelperRoute::_findCategory((int)$catid, 'itemstable')) {
			$link .= '&Itemid='.$item->id;
		}
		return $link;
	}
	
	function getCategoryRoute($catid, $layout = '')
	{
	  $app = JFactory::getApplication();
		$isSef = $app->getCfg( 'sef' );
		
		$link = '';
		if ($layout) {
			
			  $link = 'index.php?option=com_faqbook&view='.$layout.'&id='.$catid;
			  if($item = FaqBookHelperRoute::_findCategory((int)$catid, $layout)) {
				  $link .= '&Itemid='.$item->id;
			  }
					
		}
		else {
		
		  if ($isSef) {
			
				$link = 'index.php?option=com_faqbook&view=category&id='.$catid.'-'.$catalias.'&Itemid='.$item->id;
				
			} else {
			
			  $link = 'index.php?option=com_faqbook&view=category&id='.$catid.'&Itemid='.$item->id;
				
			}
			
		}
		return $link;
	}

	function _findItem($id)
	{
		$component =& JComponentHelper::getComponent('com_faqbook');
		$menus	= &JApplication::getMenu('site', array());
		$items	= $menus->getItems('componentid', $component->id);
		$match = null;
		if (count($items)) {
			foreach($items as $item)
			{
				if ((@$item->query['view'] == 'item') && @$item->query['id'] == $id) {
					$match = $item;
					break;
				}
			}
		}
		return $match;
	}
	
	function _findCategory($id, $layout)
	{
		$component =& JComponentHelper::getComponent('com_faqbook');

		$menus	= &JApplication::getMenu('site', array());
		$items	= $menus->getItems('component', 'com_faqbook');

		$match = null;
		if (count($items)) {
			foreach($items as $item)
			{
				if ((@$item->query['view'] == $layout) && @$item->query['id'] == $id ) {
					$match = $item;
					break;
				}
			}
		}
		return $match;
	}
	
	function checkIndexExists() 
	{
	
	  // Query index Itemid
		$query = " SELECT id, link "
					  ." FROM #__menu "
					  ." WHERE link = 'index.php?option=com_faqbook&view=faqbook' "
						." AND published = 1 ";	
		$db =& JFactory::getDBO();
		$db->setQuery( $query );		
		$row = $db->loadObject();	
		if ($row) {	
		$index_exists = 1;
		} else {
		$index_exists = 0;
    }
		return $index_exists;		
		
	}
	
}
