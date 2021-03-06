<?php
/**
 * @version $Id$
 * Kunena Translate Component
 * 
 * @package	Kunena Translate
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class TableLabel extends JTable
{
	/** Primary Key
	 * @var int 
	 */
	var $id = null;
	/** unique key label
	 * @var label
	 */
	var $label = null;
	/** key client
	 * @var client
	 */
	var $client = null;
	/** key extension
	 * @var extension
	 */
	var $extension = null;
	
	function __construct(& $db){
		parent::__construct('#__kunenatranslate_label', 'id', $db);
	}
	
	function loadLabels($id=null, $client=null, $ext=null){
		$db = $this->getDBO();
		$where = null;
		if(!empty($id) && is_array($id)){
			$n = count($id);
			$where = ' WHERE ';
			foreach ($id as $k=>$v){
				$where .= 'id='.$v;
				if($n>1 && $n-1>$k) $where .= ' OR ';
			}
		}elseif (!empty($id) && is_int($id)){
			$where = ' WHERE id='.$id;
		}else{
			if(empty($client)){
				$client = $this->client;
			}
			if (!empty($client) && is_string($client)){
				if(empty($where)) $where = " WHERE ";
				else $where .= " AND ";
				$where .= " client='{$client}'";
			}
			if(empty($ext)){
				$ext = $this->extension;
			}
			if (!empty($ext) && is_int($ext)){
				if(empty($where)) $where = " WHERE ";
				else $where .= " AND ";
				$where .= " extension='{$ext}'";
			}
		}
		$query = 'SELECT * 
				FROM '. $this->_tbl
				.$where;
		$db->setQuery($query);
		
		$result = $db->loadObjectlist();
		if ($result) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function loadLabelsTrans(){
		$db = $this->getDBO();
		
		$query = 'SELECT l.id, l.label, l.client , t.lang
					FROM '.$this->_tbl.' as l
					LEFT JOIN #__kunenatranslate_translation as t
					ON l.id=t.labelid
					GROUP BY l.label';
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if ($result) {
			return $result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}
	
	function store($data, $client, $extension){
		$db = $this->getDBO();
		$cdata = count($data);
		$values = '';
		if(is_array($data)){
			foreach ($data as $k=>$value) {
				$values .= "('', '{$value}', '{$client}', '{$extension}')";
				if ($cdata != $k+1) $values .= ",";
			}
		}
		$query = "INSERT INTO {$this->_tbl} ( id, label, client, extension )
				VALUES {$values}";
		$db->setQuery( $query );
		if(!$db->query()){
			$this->setError($db->getErrorMsg());
			return false;
		}else{
			return true;
		} 
	}
	
	function delete($id=array()){
		$db = $this->getDBO();
		foreach ($id as $v) {
			//TODO Find better way!
			$query = "DELETE {$this->_tbl} , #__kunenatranslate_translation 
					FROM {$this->_tbl}, #__kunenatranslate_translation
					WHERE {$this->_tbl}.{$this->_tbl_key}=#__kunenatranslate_translation.labelid
					AND {$this->_tbl}.{$this->_tbl_key}={$v}";
			$db->setQuery($query);
			if(!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
			$query = "DELETE FROM {$this->_tbl}
					WHERE {$this->_tbl}.{$this->_tbl_key}={$v}";
			$db->setQuery($query);
			if(!$db->query()){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return true;
	}
}