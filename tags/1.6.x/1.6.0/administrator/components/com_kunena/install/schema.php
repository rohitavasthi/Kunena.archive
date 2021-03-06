<?php
/**
 * @version		$Id: install.php 1244 2009-12-02 04:10:31Z mahagr$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

//
// Dont allow direct linking
defined( '_JEXEC' ) or die('Restricted access');

DEFINE('KUNENA_SCHEMA_FILE', KPATH_ADMIN.'/install/install.xml');
DEFINE('KUNENA_UPGRADE_SCHEMA_FILE', KPATH_ADMIN.'/install/upgrade/upgrade.xml');
DEFINE('KUNENA_INSTALL_SCHEMA_EMPTY', '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE schema><schema></schema>');

jimport('joomla.application.component.model');

/**
 * Install Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaModelSchema extends JModel
{
	/**
	 * Flag to indicate model state initialization.
	 *
	 * @var		boolean
	 * @since	1.6
	 */
	protected $__state_set = false;

	protected $schema = null;
	protected $xmlschema = null;
	protected $upgradeschema = null;
	protected $diffschema = null;
	protected $db = null;
	protected $sql = null;
	protected $version = null;

	public function __construct()
	{
		parent::__construct();
		$this->db = JFactory::getDBO();
	}

	/**
	 * Installer object destructor
	 *
	 * @access public
	 * @since 1.6
	 */
	public function __destruct() {
	}

	/**
	 * Overridden method to get model state variables.
	 *
	 * @param	string	Optional parameter name.
	 * @param	mixed	The default value to use if no state property exists by name.
	 * @return	object	The property where specified, the state object where omitted.
	 * @since	1.6
	 */
	public function getState($property = null, $default = null)
	{
		// if the model state is uninitialized lets set some values we will need from the request.
		if ($this->__state_set === false)
		{
			$this->__state_set = true;
		}
		$value = parent::getState($property);
		return (is_null($value) ? $default : $value);
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function getSchema()
	{
		if ($this->schema == null) $this->schema = $this->getSchemaFromDatabase();
		return $this->schema;
	}

	public function getXmlSchema($input=KUNENA_SCHEMA_FILE)
	{
		if ($this->xmlschema == null) $this->xmlschema = $this->getSchemaFromFile($input);
		return $this->xmlschema;
	}

	public function getUpgradeSchema($input=KUNENA_UPGRADE_SCHEMA_FILE)
	{
		if ($this->upgradeschema == null) $this->upgradeschema = $this->getSchemaFromFile($input);
		return $this->upgradeschema;
	}

	public function getDiffSchema($from=null, $to=null, $using=null)
	{
		if ($this->diffschema == null)
		{
			if (!$from) $from = $this->getSchema();
			if (!$to) $to = $this->getXmlSchema();
			if (!$using) $using = $this->getUpgradeSchema();
			$this->fromschema = $from;
			$this->toschema = $to;
			$this->usingschema = $using;
			$this->upgradeSchema($from, $using);
			$this->diffschema = $this->getSchemaDiff($from, $to);
			//echo "<pre>",htmlentities($this->fromschema->saveXML()),"</pre>";
			//echo "<pre>",htmlentities($this->toschema->saveXML()),"</pre>";
			$this->sql = null;
		}
		return $this->diffschema;
	}

	protected function getSQL()
	{
		if ($this->sql == null) {
			$diff = $this->getDiffSchema();
			$this->sql = $this->getSchemaSQL($diff);
		}
		return $this->sql;
	}

	public function getCreateSQL()
	{
		if ($this->sql == null) {
			$from = $this->createSchema();
			$diff = $this->getDiffSchema($from);
			$this->sql = $this->getSchemaSQL($diff);
		}
		return $this->sql;
	}

	// helper function to update table schema
	public function updateSchemaTable($table)
	{
		$sql = $this->getSQL();
		if (!isset($sql[$table])) return;
		$this->db->setQuery($sql[$table]['sql']);
		$this->db->query();
		if ($this->db->getErrorNum()) throw new KunenaSchemaException($this->db->getErrorMsg(), $this->db->getErrorNum());
		$result = $sql[$table];
		$result['success'] = true;
		return $result;
	}

	// helper function to update schema
	public function updateSchema()
	{
		$sqls = $this->getSQL();
		$results = array();
		foreach ($sqls as $sql)
		{
			if (!isset($sql['sql'])) continue;
			$this->db->setQuery($sql['sql']);
			$this->db->query();
			if ($this->db->getErrorNum()) throw new KunenaSchemaException($this->db->getErrorMsg(), $this->db->getErrorNum());
			$results[] = $sql;
		}
		return $results;
	}

	protected function listTables($prefix, $reload = false)
	{
		if (isset($this->tables[$prefix]) && !$reload) {
			return $this->tables[$prefix];
		}
		$this->db->setQuery("SHOW TABLES LIKE ".$this->db->quote($this->db->getPrefix().$prefix.'%'));
		$list = $this->db->loadResultArray();
		if ($this->db->getErrorNum()) throw new KunenaSchemaException($this->db->getErrorMsg(), $this->db->getErrorNum());
		$this->tables[$prefix] = array();
		foreach ($list as $table) {
			$table = preg_replace('/^'.$this->db->getPrefix().'/', '', $table);
			$this->tables[$prefix][] = $table;
		}
		return $this->tables[$prefix];
	}

	public function createSchema()
	{
		$schema = new DOMDocument('1.0', 'utf-8');
		$schema->formatOutput = true;
		$schema->preserveWhiteSpace = false;
		$schema->loadXML(KUNENA_INSTALL_SCHEMA_EMPTY);
		return $schema;
	}

	public function getSchemaFromFile($filename, $reload = false)
	{
		static $schema = array();
		if (isset($schema[$filename]) && !$reload) {
			return $schema[$filename];
		}
		$schema[$filename] = new DOMDocument('1.0', 'utf-8');
		$schema[$filename]->formatOutput = true;
		$schema[$filename]->preserveWhiteSpace = false;
		$dom->validateOnParse = false;
		$schema[$filename]->load($filename);
		return $schema[$filename];
	}

	public function getSchemaFromDatabase($reload = false)
	{
		static $schema = false;
		if ($schema !== false && !$reload) {
			return $schema;
		}

		$tables = $this->listTables('kunena_');

		$schema = $this->createSchema();
		$schemaNode = $schema->documentElement;
		foreach ($tables as $table) {
			if (preg_match('/_(bak|backup)$/', $table)) continue;

			$tableNode = $schema->createElement("table");
			$schemaNode->appendChild($tableNode);

			$tableNode->setAttribute("name", $table);

			$this->db->setQuery( "SHOW COLUMNS FROM ".$this->db->nameQuote($this->db->getPrefix().$table));
			$fields = $this->db->loadObjectList();
			if ($this->db->getErrorNum()) throw new KunenaSchemaException($this->db->getErrorMsg(), $this->db->getErrorNum());
			foreach ($fields as $row) {
				$fieldNode = $schema->createElement("field");
				$tableNode->appendChild($fieldNode);

				if ($row->Key == "PRI") $fieldNode->setAttribute("primary_key", "yes");
				$fieldNode->setAttribute("name", $row->Field);
				$fieldNode->setAttribute("type", $row->Type);
				$fieldNode->setAttribute("null", (strtolower($row->Null)=='yes') ? '1' : '0');
				if ($row->Default !== null) $fieldNode->setAttribute("default", $row->Default);
				if ($row->Extra != '') $fieldNode->setAttribute("extra", $row->Extra);
			}

			$this->db->setQuery( "SHOW KEYS FROM ".$this->db->nameQuote($this->db->getPrefix().$table));
			$keys = $this->db->loadObjectList();
			if ($this->db->getErrorNum()) throw new KunenaSchemaException($this->db->getErrorMsg(), $this->db->getErrorNum());

			$keyNode = null;
			foreach ($keys as $row) {
				if (!isset($keyNode) || $keyNode->getAttribute('name') != $row->Key_name) {
					$keyNode = $schema->createElement("key");
					$tableNode->appendChild($keyNode);

					$keyNode->setAttribute("name", $row->Key_name);
					if (!$row->Non_unique) $keyNode->setAttribute("unique", (bool)!$row->Non_unique);
					//if ($row->Comment != '') $keyNode->setAttribute("comment", $row->Comment);
				}

				$columns = $keyNode->getAttribute('columns');
				if (!empty($columns)) $columns .= ',';
				$columns .= $row->Column_name;
				$columns .= ($row->Sub_part) ? '('.$row->Sub_part.')' : '';
				$keyNode->setAttribute('columns', $columns);
			}
		}
		return $schema;
	}

	public function getSchemaDiff($old, $new)
	{
		$old = $this->getDOMDocument($old);
		$new = $this->getDOMDocument($new);
		if (!$old || !$new) return;

		//$old->validate();
		//$new->validate();
		$schema = $this->createSchema();
		$schemaNode = $schema->documentElement;
		$schemaNode->setAttribute('type', 'diff');

		$nodes = $this->listAllNodes(array('new'=>$new->documentElement->childNodes, 'old'=>$old->documentElement->childNodes));
		foreach ($nodes as $nodeTag => $nodeList)
		{
			foreach ($nodeList as $nodeName => $nodeLoc)
			{
				$newNode = $this->getSchemaNodeDiff($schema, $nodeTag, $nodeName, $nodeLoc);
				if ($newNode) $schemaNode->appendChild($newNode);
			}
		}
		return $schema;
	}

	protected function listAllNodes($nodeLists)
	{
		$list = array();
		foreach ($nodeLists as $k=>$nl) foreach ($nl as $n)
		{
			if (is_a($n, 'DOMAttr')) $list[$n->name][$k] = $n;
			else if (is_a($n, 'DOMElement')) $list[$n->tagName][$n->getAttribute('name')][$k] = $n;
		}
		return $list;
	}

	public function getSchemaNodeDiff($schema, $tag, $name, $loc)
	{
		$node = null;
		// Add
		if (!isset($loc['old']))
		{
			$node = $schema->importNode($loc['new'], true);
			$action = $loc['new']->getAttribute('action');
			if (!$action) $node->setAttribute('action', 'create');

			$prev = $loc['new']->previousSibling;
			while ($prev && !is_a($prev, 'DOMElement')) {
				$prev = $prev->previousSibling;
			}
			if ($prev && $tag == 'field' && $prev->tagName == 'field') $node->setAttribute('after', $prev->getAttribute('name'));
			return $node;
		}
		// Delete
		if (!isset($loc['new']))
		{
			if($loc['old']->getAttribute('extra') == 'auto_increment')
			{
				// Only one field can have auto_increment, so give enough info to fix it!
				$node = $schema->importNode($loc['old'], false);
			}
			else
			{
				$node = $schema->createElement($tag);
				$node->setAttribute('name', $name);
			}

			$action = $loc['old']->getAttribute('action');
			if (!$action) $action = 'unknown';
			$node->setAttribute('action', $action);
			return $node;
		}

		$action = $loc['old']->getAttribute('action');
		$childNodes = array();
		$childAll = $this->listAllNodes(array('new'=>$loc['new']->childNodes, 'old'=>$loc['old']->childNodes));
		foreach ($childAll as $childTag => $childList)
		{
			foreach ($childList as $childName => $childLoc)
			{
				$childNode = $this->getSchemaNodeDiff($schema, $childTag, $childName, $childLoc);
				if ($childNode) $childNodes[] = $childNode;
			}
			if (!$action && count($childNodes)) $action = 'alter';
		}

		// Primary key is always unique
		if ($loc['new']->tagName == 'key' && $loc['new']->getAttribute('name') == 'PRIMARY') $loc['new']->setAttribute('unique','1');
		// Remove default='' from a field
		if ($loc['new']->tagName == 'field' && $loc['new']->getAttribute('default') === null) $loc['new']->removeAttribute('default');

		$attributes = array();
		$attrAll = $this->listAllNodes(array('new'=>$loc['new']->attributes, 'old'=>$loc['old']->attributes));
		if (!$action) foreach ($attrAll as $attrName => $attrLoc)
		{
			if ($attrName == 'primary_key') continue;
			if ($attrName == 'action') continue;
			if (!isset($attrLoc['old']->value) || !isset($attrLoc['new']->value) || str_replace(' ', '', $attrLoc['old']->value) != str_replace(' ', '', $attrLoc['new']->value))
				$action = 'alter';
		}

		if (count($childNodes) || $action)
		{
			$node = $schema->importNode($loc['new'], false);
			foreach ($loc['new']->attributes as $attribute) $node->setAttribute($attribute->name, $attribute->value);
			if ($loc['old']->hasAttribute('from')) $node->setAttribute('from', $loc['old']->getAttribute('from'));
			$node->setAttribute('action', $action);

			$prev = $loc['new']->previousSibling;
			while ($prev && !is_a($prev, 'DOMElement')) {
				$prev = $prev->previousSibling;
			}
			if ($prev && $tag == 'field' && $prev->tagName == 'field') $node->setAttribute('after', $prev->getAttribute('name'));

			foreach ($childNodes as $newNode) {
				$node->appendChild($newNode);
			}
		}
		return $node;
	}

	protected function getDOMDocument($input)
	{
		if (is_a($input, 'DOMNode')) $schema = $input;
		else if ($input === KUNENA_INPUT_DATABASE) $schema = $this->getSchemaFromDatabase();
		else if (is_string($input) && file_exists($input)) $schema = $this->getSchemaFromFile($input);
		else if (is_string($input)) { $schema = new DOMDocument('1.0', 'utf-8'); $schema->loadXML($input); }
		if (!isset($schema)  || $schema == false) return;
		$schema->formatOutput = true;
		$schema->preserveWhiteSpace = false;

		return $schema;
	}

	public function getSchemaSQL($schema, $drop=false)
	{
		$tables = array();
		foreach ($schema->getElementsByTagName('table') as $table)
		{
			$str = '';
			$tablename = $this->db->getPrefix() . $table->getAttribute('name');
			$fields = array();
			switch ($action = $table->getAttribute('action'))
			{
				case 'unknown':
					if (!$drop) break;
				case 'drop':
					$str .= 'DROP TABLE '.$this->db->nameQuote($tablename).';';
					break;
//				case 'rename':
				case 'alter':
					if ($action == 'alter') $str .= 'ALTER TABLE '.$this->db->nameQuote($tablename).' '."\n";
//					else $str .= 'ALTER TABLE '.$this->db->nameQuote($field->getAttribute('from')).' RENAME '.$this->db->nameQuote($tablename).' '."\n";
					foreach ($table->childNodes as $field)
					{
						if ($field->hasAttribute('after')) $after = ' AFTER '.$this->db->nameQuote($field->getAttribute('after'));
						else $after = ' FIRST';

						switch ($action2 = $field->getAttribute('action'))
						{
							case 'unknown':
							case 'drop':
								if ($action2 == 'unknown' && !$drop)
								{
									if($field->getAttribute('extra') == 'auto_increment')
									{
										// Only one field can have auto_increment, so fix the old field!
										$field->removeAttribute('extra');
										$field->setAttribute('action', 'alter');
									}
									else break;
								}
								else
								{
									$fields[] = '	DROP '.$this->getSchemaSQLField($field);
									break;
								}
							case 'rename':
								if ($field->tagName == 'key') {
									$fields[] = '	DROP KEY '.$this->db->nameQuote($field->getAttribute('from'));
									$fields[] = '	ADD '.$this->getSchemaSQLField($field);
								} else {
									$fields[] = '	CHANGE '.$this->db->nameQuote($field->getAttribute('from')).' '.$this->getSchemaSQLField($field, $after);
								}
								break;
							case 'alter':
								if ($field->tagName == 'key') {
									$fields[] = '	DROP KEY '.$this->db->nameQuote($field->getAttribute('name'));
									$fields[] = '	ADD '.$this->getSchemaSQLField($field);
								} else {
									$fields[] = '	MODIFY '.$this->getSchemaSQLField($field, $after);
								}
								break;
							case 'create':
								$fields[] = '	ADD '.$this->getSchemaSQLField($field, $after);
							case '':
								break;
							default:
								echo("Kunena Installer: Unknown action $tablename.$action2 on xml file<br />");
						}
					}
					if (count($fields)) $str .= implode(",\n", $fields) . ';';
					else $str = '';
					break;
				case 'create':
				case '':
					$action = 'create';
					$str .= 'CREATE TABLE '.$this->db->nameQuote($tablename).' ('."\n";
					foreach ($table->childNodes as $field)
					{
						$sqlpart = $this->getSchemaSQLField($field);
						if (!empty($sqlpart)) $fields[] = '	'.$sqlpart;
					}
					$collation = $this->db->getCollation ();
					if (!strstr($collation, 'utf8')) $collation = 'utf8_general_ci';
					$str .= implode(",\n", $fields) . " ) DEFAULT CHARACTER SET utf8 COLLATE {$collation};";
					break;
				default:
					echo("Kunena Installer: Unknown action $tablename.$action on xml file<br />");
			}
			if (!empty($str))
				$tables[$table->getAttribute('name')] = array('name'=>$table->getAttribute('name'), 'action'=>$action, 'sql'=>$str);
		}
		return $tables;
	}

	protected function getSchemaSQLField($field, $after='')
	{
		if (!is_a($field, 'DOMElement')) return '';

		$str = '';
		if ($field->tagName == 'field')
		{
			$str .= $this->db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ' '.$field->getAttribute('type');
				$str .= ($field->getAttribute('null') == 1) ? ' NULL' : ' NOT NULL';
				$str .= ($field->hasAttribute('default')) ? ' default '.$this->db->quote($field->getAttribute('default')) : '';
				$str .= ($field->hasAttribute('extra')) ? ' '.$field->getAttribute('extra') : '';
				$str .= $after;
			}
		}
		else if ($field->tagName == 'key')
		{
			if ($field->getAttribute('name') == 'PRIMARY') $str .= 'PRIMARY KEY';
			else if ($field->getAttribute('unique') == 1) $str .= 'UNIQUE KEY '.$this->db->nameQuote($field->getAttribute('name'));
			else $str .= 'KEY '.$this->db->nameQuote($field->getAttribute('name'));
			if ($field->getAttribute('action') != 'drop')
			{
				$str .= ($field->hasAttribute('type')) ? ' USING '.$field->getAttribute('type') : '';
				$str .= ' ('.$field->getAttribute('columns').')';
			}
		}
		return $str;
	}

	public function upgradeSchema($dbschema, $upgrade)
	{
		$dbschema = $this->getDOMDocument($dbschema);
		$upgrade = $this->getDOMDocument($upgrade);
		if (!$dbschema || !$upgrade) return;

		//$dbschema->validate();
		//$upgrade->validate();

		$this->upgradeNewAction($dbschema, $upgrade->documentElement);
	}

	protected function upgradeNewAction($dbschema, $node, $table='')
	{
		foreach ($node->childNodes as $action)
		{
			if (!is_a($action, 'DOMElement')) continue;
			switch ($action->tagName) {
				case 'table':
					$this->upgradeNewAction($dbschema, $action, $action->getAttribute('name'));
					break;
				case 'version':
					if (!$this->version) break;
					$version = $action->getAttribute('version');
					$build = $action->getAttribute('build');
					$date = $action->getAttribute('date');
					$this->upgradeNewAction($dbschema, $action, $table);
				case 'if':
					$table = $action->getAttribute('table');
					$field = $action->getAttribute('field');
					$key = $action->getAttribute('key');
					if (!$field && !$key && !$this->findNode($dbschema, 'table', $table)) break;
					if ($field && !$this->findNode($dbschema, 'field', $table, $field)) break;
					if ($key && !$this->findNode($dbschema, 'key', $table, $key)) break;
					$this->upgradeNewAction($dbschema, $action, $table);
					break;
				default:
					$this->upgradeAction($dbschema, $action, $table);
			}
		}
	}

	protected function findNode($schema, $type, $table, $field='')
	{
		$rootNode = $schema->documentElement;
		foreach ($rootNode->childNodes as $tableNode)
		{
			if (!is_a($tableNode, 'DOMElement')) continue;
			if ($tableNode->tagName == 'table' && $table == $tableNode->getAttribute('name'))
			{
				if ($type == 'table') return $tableNode;
				foreach ($tableNode->childNodes as $fieldNode)
				{
					if (!is_a($fieldNode, 'DOMElement')) continue;
					if ($fieldNode->tagName == $type && $field == $fieldNode->getAttribute('name'))
					{
						return $fieldNode;
					}
				}
			}
		}
		return null;
	}

	protected function upgradeAction($dbschema, $node, $table='')
	{
		if (!$table) $table = $node->getAttribute('table');
		if (!$table) return;
		$tag = $node->tagName;

		// Allow both formats: <drop key="id"/> and <key name="id" action="drop"/>
		if ($tag != 'table' && $tag != 'field' && $tag != 'key')
		{
			$action = $tag;
			$attributes = array('field', 'key', 'table');
			foreach ($attributes as $attribute)
			{
				if ($node->hasAttribute($attribute))
				{
					$tag = $attribute;
					$name = $node->getAttribute($attribute);
					break;
				}
			}
			if (!isset($name)) return;
		}
		else
		{
			$action = $node->getAttribute('action');
			$name = $node->getAttribute('name');
		}
		$to = $node->getAttribute('to');

		$dbnode = $this->findNode($dbschema, $tag, $table, $name);
		if (!$dbnode) return;

		if ($action) $dbnode->setAttribute('action', $action);
		if ($to) {
			if (!$dbnode->hasAttribute('from')) $dbnode->setAttribute('from', $dbnode->getAttribute('name'));
			$dbnode->setAttribute('name', $to);
		}
	}

}
class KunenaSchemaException extends Exception {}