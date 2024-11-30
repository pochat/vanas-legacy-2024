<?php
/**
 * @package JDBautoBackup 
 * @author Robert Gastaud - Marc Studer
 * @link 
 * &licence GNU/GPL 
 * Fork of LazyBackup from Stefan Granholm
 * Portage Joomla 2.5 & internationalization
 *
 */
defined('_JEXEC') or die('Restricted access');

// detect if plugin settings are applied
$pJform = JRequest::getVar('jform');
if(isset($pJform['params']['backupfreq'])){
	//$fname=array_pop(JFolder::files(JPATH_SITE.'/media','lazydbbackup_checkfile.*'));
	$fnames=(JFolder::files(JPATH_SITE.'/media','lazydbbackup_checkfile.*'));
  	$fname=array_pop($fnames);
	if($fname)unlink(JPATH_SITE.'/media/'.$fname);
	$dayssecs=$pJform['params']['backuptime'];
	$dayssecs=strtotime(date('Y-m-d').' '.$dayssecs);
	if(!$dayssecs)$dayssecs=0;else $dayssecs-=strtotime(date('Y-m-d'));
	$time=time();
	$round=strtotime(date('Y-m-d',$time));
	$backuptime=$round+$dayssecs;
	$xdays=(int)$pJform['params']['xdays'];
	if($xdays==0)$xdays=1;
	if($xdays==1){
		$interval=(int)$pJform['params']['backupfreq'];
		if($interval==0)$interval=86400;else $interval=(int)(86400/$interval);
		while($backuptime<$time){
			$backuptime+=$interval;
		}
	}else{
		$interval=$xdays*86400;
		if($backuptime<$time)$backuptime+=86400;
	}
	$fname=JPATH_SITE.'/media/lazydbbackup_checkfile.'.$backuptime;
	if(!touch($fname))return;
	$f=fopen($fname,'w');fputs($f,'w'.$interval);fclose($f);
}


function teste($s){
	echo print_r($s,true).' - ';
//	echo $s.' - ';
//	echo '<pre><span style="background-color:white;color:black">'.$s.'</span></pre>';	
}

/* Import library dependencies */
jimport('joomla.event.plugin');
jimport( 'joomla.registry.registry' );

class plgSystemLazyDbBackup extends JPlugin {
	
	function onAfterInitialise() {
		jimport( 'joomla.filesystem.folder' ); // ???
		//$plugin =& JPluginHelper::getPlugin( 'system', 'lazydbbackup' );
		$plugin = JPluginHelper::getPlugin( 'system', 'lazydbbackup' );
//		$pluginParams = new JParameter( $plugin->params );
$pluginParams = new JRegistry( $plugin->params );
		$create=false;
		$fnames=JFolder::files(JPATH_SITE.'/media','lazydbbackup_checkfile.*');
		$fname=array_pop($fnames);
		//$fname=array_pop(JFolder::files(JPATH_SITE.'/media','lazydbbackup_checkfile.*'));
		if(!$fname)return;
		$backuptime=substr($fname,-10,10);
		$interval=file_get_contents(JPATH_SITE.'/media/'.$fname);
		//test("$interval");
		if($interval[0]=='w'){
			$interval=(int)substr($interval,1);
			$create=true;
		}
		
//test(date('Y-m-d H:i:s',$time));		
//test(date('Y-m-d H:i:s',$backuptime));		

//if((strpos(strtolower($_SERVER['REQUEST_URI']),'administrator')!==false)&&(strpos(strtolower($_SERVER['REQUEST_URI']),'option=com_plugins')!==false)){ // RRG 19/01/2012 backup not only for plugins
		if(strpos(strtolower($_SERVER['REQUEST_URI']),'administrator')!==false){
			//if($pluginParams->def('test',0)==1){
			$testsave = $pluginParams->def('test',0);
			if (($testsave)==1){
				$create=true;
			}
		}
		$time=time();
		if (($time>$backuptime)||$create) {
			unlink(JPATH_SITE.'/media/'.$fname);
			while($backuptime<$time)$backuptime+=$interval;
			$fname=JPATH_SITE.'/media/lazydbbackup_checkfile.'.$backuptime;
			if(!touch($fname))return;
			$f=fopen($fname,'w');fputs($f,$interval);fclose($f);

			$db = JFactory::getDBO();
			$config = JFactory::getConfig();
			$lb_abspath    = JPATH_SITE;
			$lb_host       = $config->getValue('config.host');
			$lb_user       = $config->getValue('config.user');
			$lb_password   = $config->getValue('config.password');
			$lb_db         = $config->getValue('config.db');;
			$lb_mailfrom   = $config->getValue('config.mailfrom');
			$lb_fromname   = $config->getValue('config.fromname');;
			$lb_livesite   = JURI::root();
			$mediaPath=$lb_abspath.'/media';
			$checkfileName='lazydbbackup_checkfile';
			$today = date("Y-m-d");

			// create file name
			if(!$pluginParams->def( 'name_format', 0 )){
				$filename=$config->getValue('config.sitename').'.'.$today;
			}else{
				$filename=$today.'.'.$config->getValue('config.sitename');
			}
			$filename.='.'.str_pad(rand(0,999),3,'0',STR_PAD_LEFT);
			$filename.=($pluginParams->def('compress',0)?'.sql.gz':'.sql');
			//test("$filename");
			/* No need to do the require beforehand if not ok to continue, so we'll do it here to save an eeny weeny amount of time */
			require_once($lb_abspath.'/plugins/system/lazydbbackup/lazydbbackup/mysql_db_backup.class.php');
			/* Alternative location for Bot query  */
			$deletefile      = $pluginParams->def( 'deletefile', 1 );
			$compress      = $pluginParams->def( 'compress', 0 );
			$backuppath      = $pluginParams->def( 'backuppath', 0 );
			$sendmail		= $pluginParams->def( 'sendmail', 1 );

			/* Now we need to create the backup */
			$backup_obj = new LazyDbBackup_MySQL_DB_Backup();
			$result=$this->LazyDbBackupBackup($backup_obj,$lb_host,$lb_user,$lb_password,$lb_db,$pluginParams,$mediaPath,$lb_fromname,$compress,$backuppath,$filename);
			$backupFile=$backup_obj->lazydbbackup_file_name;
//test("$backupFile");
			if($pluginParams->def('encrypt',0)){
				$password=$pluginParams->def('password',0);
				if(!empty($password)){
					if(strtoupper(substr(PHP_OS,0,3))==='WIN'){
						$zipcmd=$lb_abspath.'/plugins/system/lazydbbackup/lazydbbackup/zip.exe';		
						exec("$zipcmd -j -P $password \"$backupFile.zip\" \"$backupFile\"");
					}else{
						$zipcmd='zip';		
						exec("$zipcmd -j -P $password \"$backupFile.zip\" \"$backupFile\"");
					}
					unlink($backupFile);
					$backupFile.='.zip';
				}
			}

			if ($sendmail) {
				/* and email it to wherever */
				$EmailResult=$this->LazyDbBackupEmail($pluginParams,$lb_mailfrom,$lb_fromname,$backupFile,$result['output'],$lb_livesite);
				if($deletefile=="1"&&!empty($backupFile)){
					unlink($backupFile);
				}
			}
			/* Job done */			
			return true;
		}
	}
	 
	function LazyDbBackupEmail($pluginParams,$lb_mailfrom,$lb_fromname,$Attachment,$Body,$lb_livesite) {
		$mail = JFactory::getMailer();
		$ToEmail       = $pluginParams->def( 'recipient', '' );
		$Subject       = $pluginParams->def( 'subject', 'Mysql backup' );
		$FromName       = $pluginParams->def( 'fromname', $lb_fromname );
		if (empty($ToEmail) ) $ToEmail=$lb_mailfrom;
		// Thanks to Gerald Berger for correction on multiple email addresses
		if (strpos($ToEmail,"," )) {
        	$ToEmail2 = split(",",$ToEmail );
        }else {
            $ToEmail2 = $ToEmail;
        }

		$mail->addAttachment($Attachment);
		//$mail->addRecipient($ToEmail);
		// Thanks to Gerald Berger for correction on multiple email addresses
		$mail->addRecipient($ToEmail2);
		$mail->setSubject($Subject.' '.$lb_livesite);
		$mail->setBody($Body);
		$mail->Send();
	}
	
	function LazyDbBackupBackup(&$backup_obj,$lb_host,$lb_user,$lb_password,$lb_db,$pluginParams,$mediaPath,$lb_fromname,$compress,$backuppath,$filename='')
		 {
		 $Body             = $pluginParams->def( 'body', 'Mysql backup from '.$lb_fromname );
		 $drop_tables       = $pluginParams->def( 'drop_tables', 1 );
		 $create_tables       = $pluginParams->def( 'create_tables', 1 );
		 $struct_only       = $pluginParams->def( 'struct_only', 1 );
		 $site_only       = $pluginParams->def( 'site_only', 1 );
		 $foreign_key       = $pluginParams->def( 'foreign_key', 1 );
		 $locks             = $pluginParams->def( 'locks', 1 );
		 $comments          = $pluginParams->def( 'comments', 1 );
		 if (!empty($backuppath) && is_dir($backuppath) && @is_writable($backuppath)  )
			$backup_dir       = $backuppath;
		 else
			$backup_dir       = $mediaPath;
	
		 /* START - REQUIRED SETUP VARIABLES */
		 $backup_obj->server    = $lb_host;
		 $backup_obj->port       = 3306;
		 $backup_obj->username    = $lb_user;
		 $backup_obj->password    = $lb_password;
		 $backup_obj->database    = $lb_db;
		 /* Tables you wish to backup. All tables in the database will be backed up if this array is null. */
		 $backup_obj->tables = array();
		 /* END - REQUIRED SETUP VARIABLES */
		 
		 /* START - OPTIONAL PREFERENCE VARIABLES */
		 /* Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file. */
		 $backup_obj->drop_tables = $drop_tables;
		 /* No table structure will be backed up if false */
		 $backup_obj->create_tables = $create_tables;
		 /* Only site's tables will be backed up if true. */
		 $backup_obj->site_only = $site_only;
		 /* disable foreign key checks if true. */
		 $backup_obj->foreign_key = $foreign_key;
		 /* Add LOCK TABLES before data backup and UNLOCK TABLES after */
		 $backup_obj->struct_only = $struct_only;
		 /* Add LOCK TABLES before data backup and UNLOCK TABLES after */
		 $backup_obj->locks = $locks;
		 /* Include comments in backup file if true. */
		 $backup_obj->comments = $comments;
		 /* Directory on the server where the backup file will be placed. Used only if task parameter equals MSX_SAVE. */
		 $backup_obj->backup_dir = $backup_dir.'/';
		 /* Default file name format. */
		 $backup_obj->fname_format = 'd_m_Y';
		 /* Values you want to be intrerpreted as NULL */
		 $backup_obj->null_values = array( );
	
		 $savetask = MSX_SAVE;
		 /* Optional name of backup file if using 'MSX_APPEND', 'MSX_SAVE' or 'MSX_DOWNLOAD'. If nothing is passed, the default file name format will be used. */
//		 $filename = '';
		 /* END - REQUIRED EXECUTE VARIABLES */
		 $result_bk = $backup_obj->Execute($savetask, $filename, $compress);
		 if (!$result_bk)
			{
			$output = $backup_obj->error;
			}
		 else
			{
			$output = $Body.': ' . strftime('%A  %d  %B  %Y    - %T  ') . ' ';
			}
		 return array('result'=>$result_bk,'output'=>$output);
		 }
}
?>