<?php
/**
 * @version $Id: kunenacategories.php 4220 2011-01-18 09:13:04Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2011 Kunena Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();

kimport ('kunena.databasequery');
kimport ('kunena.error');
kimport ('kunena.user');
kimport ('kunena.user.helper');
kimport ('kunena.forum.topic.poll.helper');

/**
 * Kunena Forum Topic Poll Class
 */
class KunenaForumTopicPoll extends JObject {
	protected $_exists = false;
	protected $_db = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct($identifier = 0) {
		// Always load the topic -- if poll does not exist: fill empty data
		$this->_db = JFactory::getDBO ();
		$this->load ( $identifier );
	}

	/**
	 * Returns KunenaForumTopicPoll object
	 *
	 * @access	public
	 * @param	identifier		The poll to load - Can be only an integer.
	 * @return	KunenaForumTopicPoll		The poll object.
	 * @since	2.0
	 */
	static public function getInstance($identifier = null, $reset = false) {
		return KunenaForumTopicPollHelper::get($identifier, $reset);
	}

	function exists($exists = null) {
		$return = $this->_exists;
		if ($exists !== null) {
			$this->_exists = $exists;
		}
		return $return;
	}

	/**
	 * Method to get the polls table object
	 *
	 * This function uses a static variable to store the table name of the user table to
	 * it instantiates. You can call this function statically to set the table name if
	 * needed.
	 *
	 * @access	public
	 * @param	string	The polls table name to be used
	 * @param	string	The polls table prefix to be used
	 * @return	object	The polls table object
	 * @since	2.0
	 */
	public function getTable($type = 'KunenaPolls', $prefix = 'Table') {
		static $tabletype = null;

		//Set a custom table type is defined
		if ($tabletype === null || $type != $tabletype ['name'] || $prefix != $tabletype ['prefix']) {
			$tabletype ['name'] = $type;
			$tabletype ['prefix'] = $prefix;
		}

		// Create the user table object
		return JTable::getInstance ( $tabletype ['name'], $tabletype ['prefix'] );
	}

	public function newPoll($fields=array()) {
		$poll = new KunenaForumTopicPoll();
		$poll = $this->bind($fields, array ('title', 'threadid', 'polltimetolive'));

		return $poll;
	}

	public function bind($data, $allow = array()) {
		if (!empty($allow)) $data = array_intersect_key($data, array_flip($allow));
		$this->setProperties ( $data );
	}

	/**
	 * Method to load a KunenaForumTopicPoll object by id
	 *
	 * @access	public
	 * @param	mixed	$id The poll id to be loaded
	 * @return	boolean			True on success
	 * @since 2.0
	 */
	public function load($id) {
		// Create the table object
		$table = $this->getTable ();

		// Load the KunenaTable object based on id
		$this->_exists = $table->load ( $id );

		// Assuming all is well at this point lets bind the data
		$this->setProperties ( $table->getProperties () );

		return $this->_exists;
	}

	/**
	 * Method to delete the KunenaForumTopicPoll object from the database
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since 2.0
	 */
	public function delete() {
		if (!$this->exists()) {
			return true;
		}

		// Create the table object
		$table = $this->getTable ();

		$result = $table->delete ( $this->id );
		if (! $result) {
			$this->setError ( $table->getError () );
		}
		$this->_exists = false;

		$db = JFactory::getDBO ();
		$query = "DELETE FROM #__kunena_polls_options WHERE pollid={$db->Quote($this->id)}";
		$db->setQuery($query);
		$db->query();
		if (KunenaError::checkDatabaseError()) return;

		$query = "DELETE FROM #__kunena_polls_users WHERE pollid={$db->Quote($this->id)}";
		$db->setQuery($query);
		$db->query();
		if (KunenaError::checkDatabaseError()) return;

		return $result;
	}

	/**
	 * Method to save the KunenaForumTopicPoll object to the database
	 *
	 * @access	public
	 * @param	boolean $updateOnly Save the object only if not a new poll
	 * @return	boolean True on success
	 * @since 2.0
	 */
	public function save($updateOnly = false) {
		//are we creating a new poll
		$isnew = ! $this->_exists;

		// Create the topics table object
		$table = $this->getTable ();
		$table->bind ( $this->getProperties () );
		$table->exists ( $this->_exists );

		//Store the topic data in the database
		if (! $table->store ()) {
			$this->setError ( $table->getError () );
			return false;
		}

		// Set the id for the KunenaForumTopic object in case we created a new topic.
		if ($isnew) {
			$this->load ( $table->id );
		}

		return true;
	}
}