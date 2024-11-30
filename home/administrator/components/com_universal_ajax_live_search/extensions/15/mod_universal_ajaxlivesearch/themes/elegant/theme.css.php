<?php 
/*------------------------------------------------------------------------
# mod_universal_ajaxlivesearch - Universal AJAX Live Search 
# ------------------------------------------------------------------------
# author    Janos Biro 
# copyright Copyright (C) 2011 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/
?>
<?php
  $searchareawidth = $this->params->get('searchareawidth', 150);
  if($searchareawidth[strlen($searchareawidth)-1] != '%'){
    $searchareawidth.='px';
  }
?>
#offlajn-ajax-search{
  width: <?php print $searchareawidth; ?>;
  float: <?php echo $this->params->get('searchareaalign', 'left'); ?>
}

#offlajn-ajax-search .offlajn-ajax-search-container{
  background-color: #e4eaee;
  padding: <?php echo intval($this->params->get('borderw', 4)); ?>px;
  margin:0;
  <?php if($this->params->get('rounded')):?>  
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  border-radius: 5px;
  <?php endif; ?>
}

#search-form div{
  margin:0;
  padding:0;
}

#offlajn-ajax-search .offlajn-ajax-search-inner{
  width:100%;
}

#search-form{
  margin:0;
  padding:0;
  position: relative;
}

#search-form input{
  font-size:12px;
}

#search-form input:focus{
  background-color: #FFFFFF;
}

.dj_ie7 #search-form{
  padding-bottom:0px;
}

#search-area{
  display: block;
  height: 27px;
  padding: 0 60px 0 5px;
  width: 100%;
  box-sizing: border-box; /* css3 rec */
  -moz-box-sizing: border-box; /* ff2 */
  -ms-box-sizing: border-box; /* ie8 */
  -webkit-box-sizing: border-box; /* safari3 */
  -khtml-box-sizing: border-box; /* konqueror */
  
  border: 1px #b2c4d4 solid;
  border-right: none;
  line-height: 27px;

  <?php if($this->params->get('rounded')):?>  
  border-radius: 0px;
  -moz-border-radius: 0px;
  -moz-border-radius-topleft: 3px;
  -moz-border-radius-bottomleft: 3px;
  border-top-left-radius: 3px;
  border-bottom-left-radius: 3px;
  <?php endif; ?>

  -webkit-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);
  -moz-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);
  box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);   

  float: left;
  margin: 0;
}

.dj_ie7 #search-area{
  padding-left: 0;
  padding-right: 0;
  height: 25px;
  line-height: 25px;
  float: none;
}

.search-caption-on{
  color: #aaa;
}

#search-form #search-area-close.search-area-loading{
  background: url(<?php print $c['url'].'images/loaders/'.$this->params->get('ajaxloaderimage');?>) no-repeat center center;
}

#search-form #search-area-close{
  <?php if($this->params->get('closeimage') != -1 && file_exists(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'))): ?>
  background: url(<?php print $c['url'].'images/close/'.$this->params->get('closeimage');?>) no-repeat center center;
  background-image: url('<?php echo $this->themeCacheUrl.$c['helper']->ColorizeImage(dirname(__FILE__).'/images/close/'.$this->params->get('closeimage'), $this->params->get('closeimagecolor')); ?>');
  <?php endif; ?>
  height: 16px;
  width: 22px;
  top:50%;
  margin-top:-8px;
  right: 40px;
  position: absolute;
  cursor: pointer;
  visibility: hidden;
}

#ajax-search-button{
<?php
  $gradient = explode('-', $this->params->get('searchbuttongradient'));
  ob_start();
  include('images'.DS.'bgbutton.svg.php');
  $operagradient = ob_get_contents();
  ob_end_clean();  
?>
  height: 27px;
  width: 35px;

  -webkit-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);
  -moz-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);
  box-shadow: inset 0px 2px 4px rgba(0,0,0,0.28);   

  <?php if($this->params->get('rounded')):?>  
  -moz-border-radius-topright: 3px;
  -moz-border-radius-bottomright: 3px;
  border-top-right-radius: 3px;
  border-bottom-right-radius: 3px;
  <?php endif; ?>
  
  /* Firefox */
  background: -moz-linear-gradient( top, #<?php print $gradient[0]; ?>, #<?php print $gradient[1]; ?> );
  
  /* Chrome & Safari */
  background: -webkit-gradient( linear, left top, left bottom, color-stop( 0, #<?php print $gradient[0]; ?> ), color-stop( 1, #<?php print $gradient[1]; ?> ) );
  
  /* IE5.5 - IE7 */
  filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>);
  
  /* IE8 */
  -ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>)";
  float: left;
  cursor: pointer;
  position: absolute;
  top: 0px;
  right: 0px;
}

.dj_ie7 #ajax-search-button{
  top: 0+1; ?>px;
  right: 0-1; ?>px;
}

.dj_opera #ajax-search-button{
  background: transparent url(data:image/svg+xml;base64,<?php echo base64_encode($operagradient); ?>);
  border-radius: 0;
}

#ajax-search-button .magnifier{
  <?php if($this->params->get('searchbuttonimage') != -1 && file_exists(dirname(__FILE__).'/images/search_button/'.$this->params->get('searchbuttonimage'))): ?>
  background: url(<?php print $c['url'].'images/search_button/'.$this->params->get('searchbuttonimage');?>) no-repeat center center;
  <?php endif; ?>
  height: 27px;
  width: 35px;
  padding:0;
  margin:0;
}

#ajax-search-button:hover{
  -webkit-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.8);
  -moz-box-shadow: inset 0px 2px 4px rgba(0,0,0,0.8);
  box-shadow: inset 0px 2px 4px rgba(0,0,0,0.8);   
}

#search-results{
  position: absolute;
  top:0px;
  left:0px;
  margin-top: 2px;
  visibility: hidden;
  text-decoration: none;
  z-index:1000;
  font-size:12px;
}

#search-results-moovable{
  position: relative;
  overflow: hidden;
  height: 0px;
  width: <?php print $c['searchresultwidth'];?>px;
  background-color: #<?php print $this->params->get('resultcolor');?>;
  border: 1px #<?php print $this->params->get('resultbordertopcolor');?> solid;
  <?php if($this->params->get('rounded')):?>  
  -webkit-border-radius: 10px;
  -moz-border-radius: 10px;
  border-radius: 10px;
  <?php endif; ?>
  
<?php if($this->params->get('boxshadow')):?>    
  -webkit-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.6);
  -moz-box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.6);
  box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.6); 
<?php endif; ?>
}


#search-results-inner{
  position: relative;
  width: <?php print $c['searchresultwidth'];?>px; /**/
  overflow: hidden;
  padding-bottom: 10px;
}

.dj_ie #search-results-inner{
  padding-bottom: 0px;
}

#search-results .plugin-title{
<?php
  $gradient = explode('-', $this->params->get('plugintitlegradient'));
  ob_start();
  include('images'.DS.'bgtitle.svg.php');
  $operagradient = ob_get_contents();
  ob_end_clean(); 
?>
  -webkit-box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);
  -moz-box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);
  box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);

  line-height: 26px;
  font-size: 14px;
  /* Firefox */
  background: -moz-linear-gradient( top, #<?php print $gradient[0]; ?>, #<?php print $gradient[1]; ?> );
  
  /* Chrome & Safari */
  background: -webkit-gradient( linear, left top, left bottom, color-stop( 0, #<?php print $gradient[0]; ?> ), color-stop( 1, #<?php print $gradient[1]; ?> ) );
  
  /* IE5.5 - IE7 */
  filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>);
  
  /* IE8 */
  -ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>)";
  
  color: #<?php print $this->params->get('plugintitlefontcolor');?>;
  text-align: left;
  border-top: 1px solid #<?php print $this->params->get('resultbordertopcolor');?>;
  border-bottom: 1px solid #<?php print $this->params->get('resultborderbottomcolor');?>;
  font-weight: bold;
  height: 100%;
  margin:0;
  padding:0;
}

.dj_opera #search-results .plugin-title{
  background: #<?php print $gradient[0]; ?> url(data:image/svg+xml;base64,<?php echo base64_encode($operagradient); ?>);
/*  border-radius: 0;*/
}

#search-results .plugin-title.first{
<?php
  $gradient = explode('-', $this->params->get('plugintitlegradient'));
  ob_start();
  include('images'.DS.'bgtitletop.svg.php');
  $operagradient = ob_get_contents();
  ob_end_clean(); 
?>
  -webkit-box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);
  -moz-box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);
  box-shadow: inset 0px 0px 2px rgba(255, 255, 255, 0.4);

  <?php if($this->params->get('rounded')):?>  
  -moz-border-radius-topleft: 10px;
  -moz-border-radius-topright: 10px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  <?php endif; ?>
  margin-top: -1px;
}

.dj_opera #search-results .plugin-title.first{
  background: #<?php print $gradient[0]; ?> url(data:image/svg+xml;base64,<?php echo base64_encode($operagradient); ?>);
/*  border-radius: 0;*/
}

.dj_ie #search-results .plugin-title.first{
  margin-top: 0;
} 

#search-results .ie-fix-plugin-title{
  border-top: 1px solid #B2BCC1;
  border-bottom: 1px solid #000000;
}


#search-results .plugin-title-inner{
/* -moz-box-shadow:0 1px 2px #B2BCC1 inset;*/
  -moz-user-select:none;
  padding-left:10px;
  padding-right:5px;
  float: left;
  cursor: default;
}

#search-results .pagination{
  margin: 8px;
  margin-left: 0px;
  float: right;
  width: auto;
}


#search-results .pager{
  width: 10px;
  height: 10px;
  margin-left: 5px;
  <?php if($this->params->get('inactivepaginatorimage') != -1 && file_exists(dirname(__FILE__).'/images/paginators/'.$this->params->get('inactivepaginatorimage'))): ?>
  background: url(<?php print $c['url'].'images/paginators/'.$this->params->get('inactivepaginatorimage');?>) no-repeat;
  <?php endif; ?>
  float: left;
  padding:0;
}

#search-results .pager:hover{
  <?php if($this->params->get('hoverpaginatorimage') != -1 && file_exists(dirname(__FILE__).'/images/paginators/'.$this->params->get('hoverpaginatorimage'))): ?>
  background: url(<?php print $c['url'].'images/paginators/'.$this->params->get('hoverpaginatorimage');?>) no-repeat;
  <?php endif; ?>
  cursor: pointer;
}


#search-results .pager.active,
#search-results .pager.active:hover{
  <?php if($this->params->get('actualpaginatorimage') != -1 && file_exists(dirname(__FILE__).'/images/paginators/'.$this->params->get('actualpaginatorimage'))): ?>
  background: url(<?php print $c['url'].'images/paginators/'.$this->params->get('actualpaginatorimage');?>) no-repeat;
  <?php endif; ?>
  cursor: default;
}


#search-results .page-container{
  position: relative;
  overflow: hidden;
  height: <?php print (intval($this->params->get('imageh', 60))+6)*$c['productsperplugin'];?>px; /* 66x num of elements */
  width: <?php print $c['searchresultwidth'];?>px; /**/
}

#search-results .page-band{
  position: absolute;
  left: 0;
  width: 10000px;
}

#search-results .page-element{
  float: left;
  left: 0;
  cursor: hand;
}

#search-results #search-results-inner .result-element:hover,
#search-results #search-results-inner .selected-element{
<?php
  $gradient = explode('-', $this->params->get('activeresultgradient'));
  ob_start();
  include('images'.DS.'bgactive.svg.php');
  $operagradient = ob_get_contents();
  ob_end_clean();  
?>
  text-decoration: none;
  color: #<?php print $this->params->get('activeresultfontcolor');?>;
  /* Opera */
/*  background: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxsaW5lYXJHcmFkaWVudCBpZD0iZ3JhZGllbnQiIHgxPSIwJSIgeTE9IjAlIiB4Mj0iMCUiIHkyPSIxMDAlIj48c3RvcCBvZmZzZXQ9IjAlIiBzdHlsZT0ic3RvcC1jb2xvcjpyZ2JhKDI0LDE0MSwyMTcsMSk7IiAvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3R5bGU9InN0b3AtY29sb3I6cmdiYSgyNCw4MSwxMjUsMSk7IiAvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IGZpbGw9InVybCgjZ3JhZGllbnQpIiBoZWlnaHQ9IjEwMCUiIHdpZHRoPSIxMDAlIiAvPjwvc3ZnPg==);*/
  
  background-color: #<?php print $gradient[0]; ?>;
  /* Firefox */
  background: -moz-linear-gradient( top, #<?php print $gradient[0]; ?>, #<?php print $gradient[1]; ?> );
  
  /* Chrome & Safari */
  background: -webkit-gradient( linear, left top, left bottom, color-stop( 0, #<?php print $gradient[0]; ?> ), color-stop( 1, #<?php print $gradient[1]; ?> ) );
  
  /* IE5.5 - IE7 */
  filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>);
  
  /* IE8 */
  -ms-filter: "progid:DXImageTransform.Microsoft.Gradient(GradientType=0,StartColorStr=#<?php print $gradient[0]; ?>,EndColorStr=#<?php print $gradient[1]; ?>)";


/*  border-top: 1px solid #188dd9;*/
  border-top: none;
  padding-top: 1px;
  -webkit-box-shadow: inset 0px 2px 3px rgba(0,0,0,0.7);
  -moz-box-shadow: inset 0px 2px 3px rgba(0,0,0,0.7);
  box-shadow: inset 0px 2px 3px rgba(0,0,0,0.7);
}

.dj_opera #search-results #search-results-inner .result-element:hover,
.dj_opera #search-results #search-results-inner .selected-element{
  background: transparent url(data:image/svg+xml;base64,<?php echo base64_encode($operagradient); ?>);
  border-radius: 0;
}


#search-results .result-element{
  display: block;
  width: <?php print $c['searchresultwidth'];?>px; /**/
  height: <?php echo intval($this->params->get('imageh', 60))+4; ?>px; /*height*/
  color: #<?php print $this->params->get('resultfontcolor');?>;
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordertopcolor');?>;
  border-bottom: 1px solid #<?php print $this->params->get('resultborderbottomcolor');?>;
  overflow: hidden;
}

#search-results .result-element img{
  display: block;
  float: left;
  padding: 2px;
  padding-right:10px;
  border: 0;
}

.ajax-clear{
  clear: both;
}

#search-results .result-element span{
  display: block;
  float: left;
  width: <?php print $c['searchresultwidth']-17;?>px;   /*  margin:5+12 */
  margin-left:5px;
  margin-right:12px;
  line-height: 14px;
  text-align: left;
  cursor: pointer;
  margin-top: 5px;
}

#search-results .result-element span.small-desc{
  margin-top : 2px;
  font-weight: normal;
  line-height: 13px;
  color: #<?php print $this->params->get('resultintrotextcolor');?>;
}

#search-results .result-element:hover span.small-desc,
#search-results .selected-element span.small-desc{
  color: #DDDDDD;
}

#search-results .result-products span{
/*  text-align: center;*/
  width: <?php print $c['searchresultwidth']-12-intval($this->params->get('imagew', 60))-17;?>px;   /* padding and pictures: 10+2+60, margin:5+12  */
  margin-top: 5px;
}

#search-results .no-result{
  display: block;
  width: <?php print $c['searchresultwidth'];?>px; /**/
  height: 30px; /*height*/
  color: #<?php print $this->params->get('resultfontcolor');?>;
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordertopcolor');?>;
  border-bottom: 1px solid #<?php print $this->params->get('resultborderbottomcolor');?>;
  overflow: hidden;
  text-align: center;
  padding-top:10px;
}

#search-results .no-result-suggest {
  display: block;
  width: <?php print $c['searchresultwidth'];?>px; /**/
  color: #<?php print $this->params->get('resultfontcolor');?>;
  font-weight: bold;
  border-top: 1px solid #<?php print $this->params->get('resultbordertopcolor');?>;
  border-bottom: 1px solid #<?php print $this->params->get('resultborderbottomcolor');?>;
  overflow: hidden;
  text-align: center;
  padding-top:10px;
  padding-bottom: 6px;
  padding-left: 5px;
  padding-right: 5px;
}

#search-results .sugg-keyword {
  cursor: pointer;
  color: #<?php print $this->params->get('resultfontcolor');?>;
  font-weight: bold;
  text-decoration: none;
  padding-left: 4px;
  background-color: transparent;
  white-space:nowrap;
}

#search-results .sugg-keyword:hover {
  text-decoration: underline;  
  background-color: transparent;
  white-space:nowrap;
}

#search-results .no-result span{
  width: <?php print $c['searchresultwidth']-17;?>px;   /*  margin:5+12 */
  line-height: 20px;
  text-align: left;
  cursor: default;
  -moz-user-select:none;
}