<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.error');
kimport ('kunena.forum.message.attachment');

/**
 * Kunena Forum Message Attachment Helper Class
 */
class KunenaForumMessageAttachmentHelper {
	// Global for every instance
	protected static $_instances = array();
	protected static $_messages = array();

	private function __construct() {}

	/**
	 * Returns KunenaForumMessageAttachment object
	 *
	 * @access	public
	 * @param	identifier		The attachment to load - Can be only an integer.
	 * @return	KunenaForumMessageAttachment		The attachment object.
	 * @since	1.7
	 */
	static public function get($identifier = null, $reload = false) {
		if ($identifier instanceof KunenaForumMessageAttachment) {
			return $identifier;
		}
		$id = intval ( $identifier );
		if ($id < 1)
			return new KunenaForumMessageAttachment ();

		if ($reload || empty ( self::$_instances [$id] )) {
			self::$_instances [$id] = new KunenaForumMessageAttachment ( $id );
			self::$_messages [self::$_instances [$id]->mesid][$id] = self::$_instances [$id];
		}

		return self::$_instances [$id];
	}

	static public function getById($ids = false, $authorise='read') {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids = array_unique($ids);
		} else {
			$ids = array($ids);
		}
		self::loadById($ids);

		$list = array ();
		foreach ( $ids as $id ) {
			if (!empty(self::$_instances [$id]) && self::$_instances [$id]->authorise($authorise, null, true)) {
				$list [$id] = self::$_instances [$id];
			}
		}

		return $list;
	}

	static public function getByMessage($ids = false, $authorise='read') {
		if ($ids === false) {
			return self::$_instances;
		} elseif (is_array ($ids) ) {
			$ids2 = array();
			foreach ($ids as $id) {
				if ($id instanceof KunenaForumMessage) $id = $id->id;
				$ids2[(int)$id] = (int)$id;
			}
			$ids = $ids2;
		} else {
			$ids = array($ids);
		}
		self::loadByMessage($ids);

		$list = array ();
		foreach ( $ids as $id ) {

			if (!empty(self::$_messages [$id])) {
				foreach (self::$_messages [$id] as $instance) {
					if ($instance->authorise($authorise, null, true)) {
						$list [$instance->id] = $instance;
					}
				}
			}
		}
		return $list;
	}

	static public function getExtensions($category, $user = null) {
		$imagetypes = self::getImageExtensions($category, $user);
		$filetypes = self::getFileExtensions($category, $user);
		if ($imagetypes === false && $filetypes === false) return false;
		return array_merge((array)$imagetypes, (array)$filetypes);
	}

	static public function getImageExtensions($category, $user = null) {
		$category = KunenaForumCategoryHelper::get($category);
		$user = KunenaUserHelper::get($user);
		$config = KunenaFactory::getConfig();
		$types = explode(',', $config->imagetypes);
		foreach ($types as &$type) {
			$type = trim($type);
			if (empty($type)) unset ($type);
		}

		// Check if attachments are allowed at all
		if (!$config->image_upload) return false;
		if ($config->image_upload == 'all') return $types;

		// For now on we only allow registered users
		if (!$user->exists()) return false;
		if ($config->image_upload == 'user') return $types;

		// For now on we only allow moderators
		if (!$user->isModerator($category->id)) return false;
		if ($config->image_upload == 'moderator') return $types;

		// For now on we only allow administrators
		if (!$user->isAdmin($category->id)) return false;
		if ( $config->image_upload == 'admin') return $types;

		return false;
	}

	static public function getFileExtensions($category, $user = null) {
		$category = KunenaForumCategoryHelper::get($category);
		$user = KunenaUserHelper::get($user);
		$config = KunenaFactory::getConfig();
		$types = explode(',', $config->filetypes);
		foreach ($types as &$type) {
			$type = trim($type);
			if (empty($type)) unset ($type);
		}

		// Check if attachments are allowed at all
		if (!$config->file_upload) return false;
		if ($config->file_upload == 'all') return $types;

		// For now on we only allow registered users
		if (!$user->exists()) return false;
		if ($config->file_upload == 'user') return $types;

		// For now on we only allow moderators
		if (!$user->isModerator($category->id)) return false;
		if ($config->file_upload == 'moderator') return $types;

		// For now on we only allow administrators
		if (!$user->isAdmin($category->id)) return false;
		if ( $config->file_upload == 'admin') return $types;

		return false;
	}

	static public function cleanup() {
		$db = JFactory::getDBO ();
		// Find up to 50 orphan attachments and delete them
		$query = "SELECT a.* FROM #__kunena_attachments AS a LEFT JOIN #__kunena_messages AS m ON a.mesid=m.id WHERE m.id IS NULL";
		$db->setQuery ( $query, 0, 50 );
		$results = (array) $db->loadAssocList ('id');
		if (KunenaError::checkDatabaseError ()) return false;
		if (empty($results)) return;
		foreach ($results as $result) {
			$instance = new KunenaForumMessageAttachment ();
			$instance->bind ( $result );
			$instance->exists(false);
			unset ($instance);
		}
		$ids = implode(',', array_keys($results));
		unset ($results);
		$query = "DELETE FROM #__kunena_attachments WHERE id IN ($ids)";
		$db->setQuery ( $query );
		$db->query ();
		return KunenaError::checkDatabaseError ();
	}

	/**
	 * This function shortens long filenames for display purposes.
	 * The first 8 characters of the filename, followed by three dots
	 * and the last 5 character of the filename.
	 *
	 * @param char $filename 	Filename to be shortened if too long
	 */
	public static function shortenFilename($filename, $front=10, $back=8, $filler='...') {
		$len = strlen($filename);
		if ($len>($front+strlen($filler)+$back)){
			$output=substr($filename,0,$front).$filler.substr($filename,$len-$back,$back);
		}else{
			$output=$filename;
		}
		return $output;
	}

	// Internal functions

	static protected function loadById($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_instances [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_attachments WHERE id IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $id ) {
			if (isset($results[$id])) {
				$instance = new KunenaForumMessageAttachment ();
				$instance->bind ( $results[$id] );
				$instance->exists(true);
				self::$_instances [$id] = $instance;
				self::$_messages [$instance->mesid][$id] = $instance;
			} else {
				self::$_instances [$id] = null;
			}
		}
		unset ($results);
	}

	static protected function loadByMessage($ids) {
		foreach ($ids as $i=>$id) {
			if (isset(self::$_messages [$id]))
				unset($ids[$i]);
		}
		if (empty($ids))
			return;

		$idlist = implode(',', $ids);
		$db = JFactory::getDBO ();
		$query = "SELECT * FROM #__kunena_attachments WHERE mesid IN ({$idlist})";
		$db->setQuery ( $query );
		$results = (array) $db->loadAssocList ('id');
		KunenaError::checkDatabaseError ();

		foreach ( $ids as $mesid ) {
			if (!isset(self::$_messages [$mesid])) {
				self::$_messages [$mesid] = array();
			}
		}
		foreach ( $results as $id=>$result ) {
			$instance = new KunenaForumMessageAttachment ();
			$instance->bind ( $result );
			$instance->exists(true);
			self::$_instances [$id] = $instance;
			self::$_messages [$instance->mesid][$id] = $instance;
		}
		unset ($results);
	}
}