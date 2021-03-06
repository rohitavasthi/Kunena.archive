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

require_once (dirname ( __FILE__ ) . DS . 'kunena.php');
kimport ('kunena.user.helper');
kimport ('kunena.forum.message.helper');

/**
 * Kunena Attachments Table
 * Provides access to the #__kunena_attachments table
 */
class TableKunenaAttachments extends KunenaTable
{
	var $id = null;
	var $userid = null;
	var $mesid = null;
	var $hash = null;
	var $size = null;
	var $folder = null;
	var $filetype = null;
	var $filename = null;

	function __construct($db) {
		parent::__construct ( '#__kunena_attachments', 'id', $db );
	}

	function check() {
		$user = KunenaUserHelper::get($this->userid);
		$message = KunenaForumMessageHelper::get($this->mesid);
		if (!$user->exists()) {
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_USER'));
		}
		if (!$message->exists()) {
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_MESSAGE'));
		}
		if (!$this->folder) {
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FOLDER'));
		}
		if (!$this->filename) {
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_NO_FILENAME'));
		}
		$file = JPATH_ROOT . "/{$this->folder}/{$this->filename}";
		if (!file_exists($file)) {
			$this->setError(JText::_('COM_KUNENA_LIB_TABLE_ATTACHMENTS_ERROR_FILE_MISSING'));
		} else {
			if (!$this->hash) $this->hash = md5_file ( $file );
			if (!$this->size) $this->size = filesize ( $file );
		}
		return ($this->getError () == '');
	}
}