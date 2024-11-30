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
 
class TableCategory extends JTable {

  /** @var int Primary key */
  var $id = 0;
  /** @var string */
  var $title = '';
  /** @var string */
  var $catid = '';
  /** @var string */
  var $published = '';
  /** @var string */
  var $creator = 0;
  /** @var string */
  var $access = '';
  
  /** Constructor
  *
  * @param object Database connector object
  */
  function TableCategory(& $db) {
    parent::__construct('#__faqbook_items', 'id', $db);
  }
	
}
?>
