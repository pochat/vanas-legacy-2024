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

// Show page title
if ( $this->params->def( 'show_page_heading', 1 ) ) {
  if ( $this->params->def( 'page_heading' ) ) {
	echo '<h2 class="componentheading'. $this->params->get( 'pageclass_sfx' ).'">'
	    . $this->params->get('page_heading')
	    . '</h2>';
	} else {
	echo '<h2 class="componentheading'. $this->params->get( 'pageclass_sfx' ).'">'
	    . $this->params->get('page_title')
	    . '</h2>';
	}
} ?>

<div id="index_categories">	

  <div id="welcome-msg">
     <?php		
		 if ($this->params->get('show_message')==1) 
		 { ?>
	 	 	 <h1><?php echo $this->params->get('message_name'); ?></h1>
		 <?php } ?>
	</div>
		 
		 <?php
		 // Show 1st level Categories
		 if (!empty($this->rows)) { ?>
		    
						 <ul>		
						 
		 				 <?php	
						 foreach ( $this->rows as $row) { 
						
						 // Decode json for image path
						 $json = $row->params;
						 $obj = json_decode($json);
						 
						 // Get Subcategories
						 $model			= &$this->getModel();
						 $subcategories = $model->getIndexSubCategories($row->id);	 	 
						 ?>
						 
						 <li>					 		    
						 
								 <div class="index_cat">
								 
								 <?php 								 
										// Show Subcategories number
										if ($this->params->get('subcategories_number')==1) { ?>
								 	
									 <div class="parent_left">
																
										<?php 
										// Show parent image
										if ($this->params->get('parent_image')) { ?>
										<div class="parent_img">
												 <img src="<?php echo $obj->{'image'};?>" title="<?php echo $row->title;?>" width="<?php echo $this->params->get('parent_image_width'); ?>" alt="" />
										</div>
						 		 
								 		<div class="parent_data_82">
										  <a href="<?php echo JRoute::_(FaqBookHelperRoute::getCategoryRoute($row->id, 'category')); ?>">
								 				 <?php echo $row->title;
												 if ($this->params->get('parent_description')) { ?>
						 		 				 <span><?php echo strip_tags($row->description);?></span>		 
						 		 				 <?php } ?>
											</a>
										</div>
										
										<?php 
										// No parent image
										} else { ?>
										<div class="parent_data_100">
										  <a href="<?php echo JRoute::_(FaqBookHelperRoute::getCategoryRoute($row->id, 'category')); ?>">
								 				 <?php echo $row->title;
						 		 				 if ($this->params->get('parent_description')) { ?>
						 		 				 <span><?php echo strip_tags($row->description);?></span>		 
						 		 				 <?php } ?>
											</a>
										</div>
										<?php } ?>
									 
									 </div><!--parent_left-->
									 						 
									 <div class="parent_right">
									 
									 			<div class="subcats_num">
												<?php 										
												$numsubcat=0;
						            foreach ( $subcategories as $subcategory ) {										            		
						 						$numsubcat++;										
						 						}
												if ($numsubcat==0) { ?>
												<span class="str"><?php echo JText::_('NOTE: You must create subcategories in this category');?></span>
												<?php } else {
												if ($numsubcat>1) { ?>
												<span class="num"><?php echo $numsubcat;?></span>
												<span class="str"><?php echo JText::_('SUBCATEGORIES');?></span>
												<?php } ?>
												<?php if ($numsubcat==1) { ?>
												<span class="num"><?php echo $numsubcat;?></span>
												<span class="str"><?php echo JText::_('SUBCATEGORY');?></span>
												<?php }
												} ?>
												</div>
									 
									 </div><!--parent_right-->
									 
									 <?php } ?>
									 
									 <?php 
										// Don't show Subcategories number
										if ($this->params->get('subcategories_number')==0) { ?>
								 	
									 <div class="parent_left_wide">
																
										<?php 
										// Show parent image
										if ($this->params->get('parent_image')==1) { ?>
										<div class="parent_img">
												 <img src="<?php echo $obj->{'image'};?>" title="<?php echo $row->title;?>" width="<?php echo $this->params->get('parent_image_width'); ?>" alt="" />
										</div>
						 		 
								 		<div class="parent_data_82">
										  <a href="<?php echo JRoute::_(FaqBookHelperRoute::getCategoryRoute($row->id, 'category')); ?>">
								 				 <?php echo $row->title;
												 if ($this->params->get('parent_description')) { ?>
						 		 				 <span><?php echo strip_tags($row->description);?></span>		 
						 		 				 <?php } ?>
											</a>
										</div>
										
										<?php 
										// No parent image
										} else { ?>
										<div class="parent_data_100">
										  <a href="<?php echo JRoute::_(FaqBookHelperRoute::getCategoryRoute($row->id, 'category')); ?>">
								 				 <?php echo $row->title;
						 		 				 if ($this->params->get('parent_description')) { ?>
						 		 				 <span><?php echo strip_tags($row->description);?></span>		 
						 		 				 <?php } ?>
											</a>
										</div>
										<?php } ?>
										
										<div id="empty-cat">
										<?php 										
										$numsubcat=0;
						        foreach ( $subcategories as $subcategory ) {										            		
						 			  $numsubcat++;										
						 			  }
										if ($numsubcat==0) { ?>
												<span class="str"><?php echo JText::_('NOTE: You must create subcategories in this category');?></span>
										<?php } ?>
										</div>
									 
									 </div><!--parent_left_wide-->
									 
									 <?php } ?>
									 
								 </div><!--index_cat-->		
						 </li>
						 
						 <?php } ?>
						 
						 </ul>
				
		 <?php } ?>
		 
</div>