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
}

// Get category id
$this_catid = JRequest::getVar( 'id', '' );

// Get Itemid
$itemid = JRequest::getVar( 'Itemid', '' );

// Get current url
$uri = & JFactory::getURI();
$current = $uri->toString();

// Get user id
$user = &JFactory::getUser();
$user_id = $user->id;

// Get Subcategories
$model = &$this->getModel();
$checkaccess = $model->checkParentAccess($this_catid);
$subcategories = $model->getSubCategories($this_catid);	
$submitsubcats = $model->getSubmitSubcategory($this_catid);
$categoryname = $model->getCategoryName($this_catid);
$this_cat = $categoryname->title; 
?>

<?php if ($this->params->get('load_jquery')) { ?>
  <script type="text/javascript" src="components/com_faqbook/js/jquery-1.6.min.js"></script>
<?php } ?>
<script type="text/javascript" src="components/com_faqbook/js/jquery.scrollTo-1.4.2-min.js"></script>
<script type="text/javascript" src="components/com_faqbook/js/jquery.localscroll-1.2.7-min.js"></script>
<?php if ($this->params->get('subcategories_left_list_scroll')) { ?>
<script type="text/javascript">
     jQuery.noConflict();																	
		 jQuery(document).ready(function(){	
		    <?php	if ($this->params->get('flicker_fix')) { ?>
		    stop();					
				<?php } ?>											
			  jQuery.localScroll();								
		 });			
</script>		
<?php } ?>

<div id="faq-nav">
  <div id="nav-bar">
	  <?php if ($this->params->get('subcategories_index')) { ?>
		  <?php if (FaqBookHelperRoute::checkIndexExists()) { ?>
	      <a href="<?php echo JRoute::_('index.php?option=com_faqbook&view=faqbook&Itemid='.$this->indexitemid); ?>" class="nav-link"><?php echo JText::_('FAQ_INDEX'); ?><i></i></a>
		  <?php } ?>
		<?php } ?>
		<a href="<?php echo JRoute::_(FaqBookHelperRoute::getCategoryRoute($this_catid, 'category')); ?>" class="nav-link"><?php echo $this_cat; ?></a>
	</div>  	    
	              
	<?php if ($this->params->get('new_faq') && ($user_id!=0 || ($user_id==0 && $this->params->get('new_faq_guest')) )) { ?>			
	<a href="javascript:void(0);" id="ask-new" rel="nofollow"><i></i><?php echo JText::_('Ask a new question'); ?></a>
	<?php } ?>
  <?php if (($user_id==0 && !$this->params->get('new_faq_guest')) && $this->params->get('new_faq_guest_msg')) { ?>				
	<a href="javascript:void(0);" id="ask-new-msg" rel="nofollow"><i></i><?php echo JText::_('Ask a new question'); ?></a>
	<?php } ?>
  <?php if ($this->params->get('new_faq') && ($user_id!=0 || ($user_id==0 && $this->params->get('new_faq_guest')) )) { ?>				
					<script type="text/javascript">
					//<![CDATA[
					function submitbutton() {
					var f = document.faqForm; 					
					if((f.title.value) == '' || (f.title.value) == '<?php echo $this->params->get('new_faq_text'); ?>' ) {
					alert("<?php echo (JText::_( 'ALERT_MISSING_QUESTION' ));?>");
					f.title.focus();			
					}
					else{
					f.submit();
					}
					}			
					//]]>			 
					</script>		
					<div id="ask">					
							<div id="new-faq-form" style="display:none;">					
									<form action="<?php echo $current; ?>" method="post" name="faqForm" id="faqForm" >									
										<fieldset>									
											<textarea name="title" id="title" rows="2" cols="" onfocus="if (this.value == '<?php echo $this->params->get('new_faq_text'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo $this->params->get('new_faq_text'); ?>';}"><?php echo $this->params->get('new_faq_text'); ?></textarea>									
											<input type="button" class="button_submit" value="<?php echo (JText::_( 'Submit question' )); ?>" onclick="javascript:submitbutton();" />
											<input type="button" class="button_cancel" value="<?php echo (JText::_( 'Cancel' )); ?>" onclick="javascript:void(0);" id="ask-cancel" />																	
											<input type="hidden" name="option" value="com_faqbook" />
											<input type="hidden" name="creator" id="creator" value="<?php echo $user_id;?>" />
											<input type="hidden" name="catid" id="catid" value="<?php echo $this->params->get('specified_cat'); ?>"/>
											<input type="hidden" name="published" id="published" value="0"/>
											<input type="hidden" name="access" id="access" value="1"/>
											<input type="hidden" name="task" value="save" />		
										</fieldset>	
	  							</form>							
							</div>
					</div>	
					
	        <script type="text/javascript">																					
									jQuery(document).ready(function(){																	
											jQuery('#ask-new').click(function () {																																											
													jQuery('#new-faq-form').slideToggle();
													jQuery('#ask').addClass('active');		
													jQuery('#cat-search-form').hide();		
													jQuery('#cat-search-div').removeClass('active');	
													jQuery('#close').hide();															
											});	
											jQuery('#ask-cancel').click(function () {																																										
													jQuery('#new-faq-form').slideToggle();
													jQuery('#ask').removeClass('active');													
											});																																	
			 					 	});										
	        </script>		
	<?php } ?>		
	<?php if (($user_id==0 && !$this->params->get('new_faq_guest')) && $this->params->get('new_faq_guest_msg')) { ?>				
	<script type="text/javascript">																					
									jQuery(document).ready(function(){																	
											jQuery('#ask-new-msg').click(function () {																																											
													jQuery('#ask-msg-inner').slideToggle();
													jQuery('#ask-close').fadeToggle();	
													jQuery('#ask-msg').addClass('active');		
													jQuery('#cat-search-form').hide();		
													jQuery('#cat-search-div').removeClass('active');	
													jQuery('#close').hide();															
											});	
											jQuery('#ask-close').click(function () {																																										
													jQuery('#ask-msg-inner').slideToggle();		
													jQuery('#ask-close').fadeToggle();												
											});																																	
			 					 	});										
	</script>	
	<div id="ask-msg">
	 <div id="ask-msg-inner" style="display:none;">
	 <?php echo $this->params->get('new_faq_guest_msg_text') ?>
	 </div>
	 <a href="javascript:void(0);" id="ask-close" title="<?php echo JText::_('CLOSE'); ?>" style="display:none" rel="nofollow"></a>
	</div>
	<?php } ?>	

</div>

<div id="category">

		 <?php if ($this->params->get('subcategories_left_list')) { ?>
		  
		 <div id="left-list">	 
		 <div id="navigation">	 
		 											
		 			<ul id="left-cats">		
					    <?php if (!$subcategories) { ?>
					    <span class="str"><?php echo JText::_('You must create Subcategories in this Category and put FAQs inside Subcategories'); ?></span>
					    <?php } ?>
							
							<?php	foreach ( $subcategories as $subcategory) { ?>						
							<li>																	
							<a href="<?php echo $current; ?>#subcat<?php echo $subcategory->id; ?>" rel="nofollow">
							<?php 
							echo $subcategory->title;
							?>
							<i></i>
							</a>												
							</li>						
							<?php } ?>					
					</ul>
					
					<?php if ($this->params->get('animate_left_list')) { ?>				
	          <script type="text/javascript">		
						jQuery(function() {
						  jQuery('#left-cats').jScroll({ top : 12, speed : 1500 });
						}); 																			
				      				
	         </script>	
		     <?php } ?>
							
		 </div><!--navigation-->
		 
		 </div>
		 <?php } else { ?>
		 <div id="navigation"></div>	 
		 <?php } ?>
		 <div id="<?php if ($this->params->get('subcategories_left_list')) { echo 'right-list'; } else { echo 'one-list'; } ?>">
		 	
							<?php	foreach ( $subcategories as $subcategory) { ?>					
							<div id="subcat<?php echo $subcategory->id; ?>">
							<h3>
							<?php echo $subcategory->title; ?>
							<a href="<?php echo $current; ?>#faq-nav" rel="nofollow" title="<?php echo JText::_('Top');?>"><i></i></a>
							</h3>
							</div>				
							<?php 
							// Get subcategory items
							$model = &$this->getModel();
							$items = $model->getSubCategoryItems($subcategory->id);	?>									
							<ul>					
									<?php foreach ($items as $item) { ?>	
									<li>						
									<script type="text/javascript">			
									//<![CDATA[																		
									jQuery(document).ready(function(){								
											jQuery('#answerButton<?php echo $item->id; ?>').click(function () {																	
													<?php if ($this->params->get('faq_slide')==1) { ?>												
													jQuery('#answer<?php echo $item->id; ?>').slideToggle('slow');
													<?php } else { ?>
													jQuery('#answer<?php echo $item->id; ?>').toggle();
													<?php } ?>
													jQuery('#answerButton<?php echo $item->id; ?>').toggleClass('active');																									
													jQuery('#answer<?php echo $item->id; ?>start').toggle();		
											});																													
			 					 	});				
									//]]>								
									</script>						
																
									<div id="faq<?php echo $item->id; ?>">
									<a href="javascript:void(0);" id="answerButton<?php echo $item->id; ?>" class="answerButton" rel="nofollow">
									<i></i>							
									<?php echo $item->title; ?>								
									</a>										 
											 <div id="answer-box<?php echo $item->id; ?>" class="answer-box">									 
											 <?php if ($this->params->get('faq_introtext') && !$this->params->get('faq_slide')) { ?>
											 <div id="answer<?php echo $item->id; ?>start" class="answer-introtext">
											 <?php echo strip_tags(JHtml::_('string.truncate', $item->content, $this->params->get('faq_introtext_chars'))); ?>
											 </div>
											 <?php } ?>									 
											 <div id="answer<?php echo $item->id; ?>" style="display:none;" class="answer">
											 <?php echo $item->content; ?>	
											 												 				 
											       <?php if ($this->params->get('faq_voting') && ($user_id!=0 || ($user_id==0 && $this->params->get('guest_voting')) )) { ?>
											 			 <div id="vote<?php echo $item->id;?>" class="vote">
														 			<div class="vote-div">
																	     <p><?php echo JText::_('Was this helpful');?></p>
																			 <?php $this_faq = $item->id; ?>
																			 <script type="text/javascript">																		 
																			 		 jQuery(document).ready(function(){																					 
																			 				 jQuery("#thumb_up<?php echo $item->id; ?>, #thumb_down<?php echo $item->id; ?>").click(function(){
            																	 							 var element = jQuery(this).get(0);
            																								 jQuery.ajax({
               																							  url         : jQuery(this).attr('href'),              																							
                																							dataType:   'json'
            																									});																										
																															jQuery('#vote<?php echo $item->id;?>').hide();	
																							     						jQuery('#feedback<?php echo $item->id; ?>').toggle();																													
            																									return false;																													
        																			 });																					 
																			     });	
																			 </script>
				 															 <div id="vote-box<?php echo $item->id; ?>" class="vote-box">																					 
																		 	 		<div class="thumb-box">																		
																					<a id="thumb_up<?php echo $item->id; ?>" class="thumb_up" onclick="javascript: return false;" href="<?php echo JRoute::_("index.php?option=com_faqbook&view=category&task=thumbup&id=".$item->id."&Itemid=".$itemid);?>" title="<?php echo jText::_('Vote up');?>" rel="nofollow">                                   			
                                					<i></i>
																					<span><?php echo $item->votes_up;?></span>
																					</a>																				
																					</div>
																					<div class="thumb-box">
                                					<a id="thumb_down<?php echo $item->id; ?>" class="thumb_down" onclick="javascript: return false;" href="<?php echo JRoute::_("index.php?option=com_faqbook&view=category&task=thumbdown&id=".$item->id."&Itemid=".$itemid);?>" title="<?php echo jText::_('Vote down');?>" rel="nofollow">									       			
                                					<i></i>
																					<span><?php echo $item->votes_down;?></span>
																					</a>																			
																					</div>																		
																			 </div>
																	</div>															
											 			 </div><!--vote-->									 				
														 <div id="feedback<?php echo $item->id; ?>" class="feedback" style="display:none;">
														 			<i></i><p><?php echo JText::_('Thank you for the feedback');?></p>
														 </div>
														 <?php } ?>																	 
											 </div>									 									 
											 </div><!--answer-box-->									 
									</div>																			
									</li>							
									<?php } ?>					
							</ul>							
							<?php } ?>		
				
				 </div>
		 
</div>	 