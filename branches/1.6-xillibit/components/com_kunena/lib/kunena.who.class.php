<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();

class CKunenaWhoIsOnline {
	public $db = null;
	public $my = null;
	public $app = null;
	public $config = null;
	protected $myip = null;
	protected $name = null;
	protected $datenow = null;

	protected function __construct($db, $config, $app) {
		$this->db = $db;
		$this->my = &JFactory::getUser ();
		$this->config = $config;
		$this->app = $app;
		$this->name = $this->config->username ? "username" : "name";
		$this->datenow = $this->_getDateNow();
	}

	public function &getInstance() {
		static $instance = NULL;
		if (! $instance) {
			$kunena_db = & JFactory::getDBO ();
			$kunena_config = & CKunenaConfig::getInstance ();
			$kunena_app = & JFactory::getApplication ();

			$instance = new CKunenaWhoIsOnline ( $kunena_db, $kunena_config, $kunena_app );
		}
		return $instance;
	}

	protected function _getDateNow ( ) {
		return CKunenaTimeformat::internalTime();
	}

	public function getActiveUsersList() {
		$query
        = "SELECT w.userip, w.time, w.what, u.{$this->name} AS username, u.id, k.moderator, k.showOnline "
        . " FROM #__fb_whoisonline AS w"
        . " LEFT JOIN #__users AS u ON u.id=w.userid "
        . " LEFT JOIN #__fb_users AS k ON k.userid=w.userid "
		# filter real public session logouts
        . " INNER JOIN #__session AS s ON s.guest='0' AND s.userid=w.userid "
        . " WHERE w.userid!='0' "
        . " GROUP BY u.id "
        . " ORDER BY username ASC";
    	$this->db->setQuery($query);
    	$users = $this->db->loadObjectList();
    	check_dberror ( "Unable to load online users." );

    	return $users;
	}

	public function getTotalRegistredUsers () {
		$users =$this->getActiveUsersList();
		return $totaluser = count($users);
	}

	public function getTotalGuestUsers () {
		$query = "SELECT COUNT(*) FROM #__fb_whoisonline WHERE user='0'";
    	$this->db->setQuery($query);
    	$totalguests = $this->db->loadResult();
    	check_dberror ( "Unable to load who is online." );

    	return $totalguests;
	}

	public function getTitleWho ($totaluser,$totalguests) {
		$who_name = '<strong>'.$totaluser.' </strong>';
       	if($totaluser==1) {
        	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBER').'&nbsp;';
        } else {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_MEMBERS').'&nbsp;';
        }
        $who_name .= JText::_('COM_KUNENA_WHO_AND');
        $who_name .= '<strong> '. $totalguests.' </strong>';
        if($totalguests==1) {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUEST').'&nbsp;';
        } else {
           	$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_GUESTS').'&nbsp;';
        }
		$who_name .= JText::_('COM_KUNENA_WHO_ONLINE_NOW');

		return $who_name;
	}

	public function getUsersList () {
		$query = "SELECT w.*, u.id, u.{$this->name}, f.showOnline FROM #__fb_whoisonline AS w
        LEFT JOIN #__users AS u ON u.id=w.userid
        LEFT JOIN #__fb_users AS f ON u.id=f.userid
        ORDER BY w.time DESC";
        $this->db->setQuery($query);
        $users = $this->db->loadObjectList();
        check_dberror ( "Unable to load online users." );

        return $users;
	}

	protected function _deleteUsersOnline () {
		$past = $this->datenow - $this->config->fbsessiontimeout;
		$this->db->setQuery("DELETE FROM #__fb_whoisonline WHERE time < '{$past}'");
		$this->db->query();
		check_dberror ( "Unable to delete users from whoisonline." );
	}

	protected function _getOnlineUsers () {
		$this->db->setQuery("SELECT COUNT(*) FROM #__fb_whoisonline WHERE userip='{$this->myip}' AND userid='{$this->my->id}'");
		$online = $this->db->loadResult();
		check_dberror ( "Unable to load online count." );

		return $online;
	}

	protected function _IsUser() {
		if ($this->my->id > 0) {
    		$isuser = 1;
    	} else {
    		$isuser = 0;
    	}

    	return $isuser;
	}

	public function insertOnlineDatas () {
		$id = JRequest::getInt('id');
		$catid = JRequest::getInt('catid');
		$func = JString::strtolower ( JRequest::getCmd ( 'func', 'listcat' ) );
		$task = JRequest::getCmd('task');
		$do = JRequest::getCmd('do');

		$isuser = $this->_IsUser();
		$this->myip = getenv('REMOTE_ADDR');

		$online = $this->_getOnlineUsers();

		if ( $func == 'showcat') {
    		$this->db->setQuery("SELECT name FROM #__fb_categories WHERE id='{$catid}'");
    		$what = JText::_('COM_KUNENA_WHO_VIEW_SHOWCAT').' '.$this->db->loadResult();
    		check_dberror ( "Unable to load category name." );
		} else if ($func == 'listcat') {
    		$what = JText::_('COM_KUNENA_WHO_VIEW_LISCAT');
		} else if ($func == 'latest') {
    		$what = JText::_('COM_KUNENA_WHO_ALL_DISCUSSIONS');
    	} else if ($func == 'mylatest') {
    		$what = JText::_('COM_KUNENA_WHO_MY_DISCUSSIONS');
    	} else if ($func == 'view') {
    		$this->db->setQuery("SELECT subject FROM #__fb_messages WHERE id='{$id}'");
    		$what = JText::_('COM_KUNENA_WHO_VIEW_TOPIC').' '.$this->db->loadResult();
    		check_dberror ( "Unable to load subject of message." );
    	} else if ($func == 'post' && $do == 'reply') {
    		$this->db->setQuery("SELECT subject FROM #__fb_messages WHERE id='{$id}'");
    		$what = JText::_('COM_KUNENA_WHO_REPLY_TOPIC').' '.$this->db->loadResult();
    		check_dberror ( "Unable to load subject of message." );
    	} else if ($func == 'post' && $do == 'edit') {
    		$this->db->setQuery("SELECT name FROM #__fb_messages WHERE id='{$id}'");
    		$what = JText::_('COM_KUNENA_WHO_POST_EDIT').' '.$this->db->loadResult();
    		check_dberror ( "Unable to load name of author of the message." );
    	} else if ($func == 'who') {
    		$what = JText::_('COM_KUNENA_WHO_VIEW_WHO');
    	} else if ($func== 'search') {
  			$what = JText::_('COM_KUNENA_WHO_SEARCH');
		} else if ($func== 'profile') {
  			$what = JText::_('COM_KUNENA_WHO_PROFILE');
		} else if ($func== 'stats') {
  			$what = JText::_('COM_KUNENA_WHO_STATS');
		} else if ($func== 'userlist') {
  			$what = JText::_('COM_KUNENA_WHO_USERLIST');
		} else {
  			$what = JText::_('COM_KUNENA_WHO_MAINPAGE');
		}

		$link = addslashes(JURI::current());

		if ($online == 1) {
    		$sql = "UPDATE #__fb_whoisonline SET ".
    		" time=".$this->db->quote($this->datenow).", ".
    		" what=".$this->db->quote($what).", ".
    		" do=".$this->db->quote($do).", ".
    		" task=".$this->db->quote($task).", ".
    		" link=".$this->db->quote($link).", ".
    		" func=".$this->db->quote($func).
            " WHERE userid=".$this->db->quote($this->my->id).
            " AND userip=".$this->db->quote($this->myip);
    		$this->db->setQuery($sql);
    	} else {
    		$this->_deleteUsersOnline();
    		$sql = "INSERT INTO #__fb_whoisonline (`userid` , `time`, `what`, `task`, `do`, `func`,`link`, `userip`, `user`) "
            . " VALUES (".
            $this->db->quote($this->my->id).",".
            $this->db->quote($this->datenow).",".
            $this->db->quote($what).",".
            $this->db->quote($task).",".
            $this->db->quote($do).",".
            $this->db->quote($func).",".
            $this->db->quote($link).",".
            $this->db->quote($this->myip).",".
            $this->db->quote($isuser).")";

    		$this->db->setQuery($sql);
    	}

		$this->db->query();
		check_dberror ( "Unable to insert user into whoisonline." );
	}

	public function displayWho () {
		CKunenaTools::loadTemplate('/plugin/who/who.php');
	}

	public function displayWhoIsOnline () {
		CKunenaTools::loadTemplate('/plugin/who/whoisonline.php');
	}
}
?>