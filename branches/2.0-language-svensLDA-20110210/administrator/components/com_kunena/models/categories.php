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

jimport ( 'joomla.application.component.model' );
kimport('kunena.forum.category.helper');
kimport('kunena.model');
kimport('kunena.user.helper');

/**
 * Categories Model for Kunena
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaAdminModelCategories extends KunenaModel {
	protected $__state_set = false;
	protected $_admincategories = false;
	protected $_admincategory = false;

	/**
	 * Method to auto-populate the model state.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() {
		$app = JFactory::getApplication ();

		// List state information
		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.limit", 'limit', $app->getCfg ( 'list_limit' ), 'int' );
		$this->setState ( 'list.limit', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.ordering', 'filter_order', 'ordering', 'cmd' );
		$this->setState ( 'list.ordering', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.start", 'limitstart', 0, 'int' );
		$this->setState ( 'list.start', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.direction', 'filter_order_Dir', 'asc', 'word' );
		if ($value != 'asc')
			$value = 'desc';
		$this->setState ( 'list.direction', $value );

		$value = $this->getUserStateFromRequest ( 'com_kunena.admin.categories.list.search', 'search', '', 'string' );
		$this->setState ( 'list.search', $value );

		$value = $this->getUserStateFromRequest ( "com_kunena.admin.categories.list.levels", 'levellimit', 10, 'int' );
		$this->setState ( 'list.levels', $value );

		$catid = $this->getInt ( 'catid', 0 );
		$layout = $this->getWord ( 'layout', 'edit' );
		$parent_id = 0;
		if ($layout == 'create') {
			$parent_id = $catid;
			$catid = 0;
		}
		$this->setState ( 'item.id', $catid );
		$this->setState ( 'item.parent_id', $parent_id );
	}

	public function getAdminCategories() {
		if ( $this->_admincategories === false ) {
			$me = KunenaFactory::getUser();
			$params = array (
				'ordering'=>$this->getState ( 'list.ordering' ),
				'direction'=>$this->getState ( 'list.direction' ) == 'asc' ? 1 : -1,
				'search'=>$this->getState ( 'list.search' ),
				'unpublished'=>1,
				'action'=>'admin');
			$catid = $this->getState ( 'item.id', 0 );
			$categories = array();
			if ($catid) {
				$categories = KunenaForumCategoryHelper::getParents($catid, $this->getState ( 'list.levels' ), array('unpublished'=>1, 'action'=>'none'));
				$categories[] = KunenaForumCategoryHelper::get($catid);
			}
			$categories = array_merge($categories, KunenaForumCategoryHelper::getChildren($catid, $this->getState ( 'list.levels' ), $params));
			$this->setState ( 'list.total', count($categories) );
			$this->_admincategories = array_slice ( $categories, $this->getState ( 'list.start' ), $this->getState ( 'list.limit' ) );
			$acl = JFactory::getACL ();
			$admin = 0;
			foreach ($this->_admincategories as $category) {
				$siblings = array_keys(KunenaForumCategoryHelper::getCategoryTree($category->parent_id));
				if (empty($siblings)) {
					// FIXME: deal with orphaned categories
					$orphans = true;
					$category->parent_id = 0;
					$category->name = JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN' ) . ' : ' . $category->name;
				}
				$category->up = $me->isAdmin($category->parent_id) && reset($siblings) != $category->id;
				$category->down = $me->isAdmin($category->parent_id) && end($siblings) != $category->id;
				$category->reorder = $me->isAdmin($category->parent_id);
				if ($category->accesstype != 'none') {
					$category->pub_group = JText::_('COM_KUNENA_INTEGRATION_'.strtoupper($category->accesstype));
				} else if ($category->pub_access == 0) {
					$category->pub_group = JText::_ ( 'COM_KUNENA_EVERYBODY' );
				} else if ($category->pub_access == - 1) {
					$category->pub_group = JText::_ ( 'COM_KUNENA_ALLREGISTERED' );
				} else if ($category->pub_access == 1) {
					$category->pub_group = JText::_ ( 'COM_KUNENA_NOBODY' );
				} else {
					// FIXME: Add Joomla 1.6 support
					$category->pub_group = JText::_ ( $acl->get_group_name($category->pub_access));
				}
				if ($category->accesstype != 'none') {
					$category->admin_group = '';
				} else {
					$category->admin_group = JText::_ ( $acl->get_group_name($category->admin_access ));

				}
				if ($me->isAdmin($category->id) && $category->isCheckedOut(0)) {
					$category->editor = KunenaFactory::getUser($category->checked_out)->getName();
				} else {
					$category->checked_out = 0;
					$category->editor = '';
				}
				$admin += $me->isAdmin($category->id);
			}
			$this->setState ( 'list.count.admin', $admin );
		}
		if (isset($orphans)) {
			$app = JFactory::getApplication ();
			$app->enqueueMessage ( JText::_ ( 'COM_KUNENA_CATEGORY_ORPHAN_DESC' ), 'notice' );
		}
		return $this->_admincategories;
	}

	public function getAdminNavigation() {
		kimport ( 'kunena.html.pagination' );
		$navigation = new KunenaHtmlPagination ($this->getState ( 'list.total'), $this->getState ( 'list.start'), $this->getState ( 'list.limit') );
		return $navigation;
	}

	public function getAdminCategory() {
		$parent_id = $this->getState ( 'item.parent_id' );
		$catid = $this->getState ( 'item.id' );
		$me = KunenaFactory::getUser();
		if (!$me->isAdmin(null) && !$me->isAdmin($catid)) {
			return false;
		}
		if ($this->_admincategory === false) {
			$app = JFactory::getApplication ();
			$category = KunenaForumCategoryHelper::get ( $catid );
			if ($category->exists ()) {
				if (!$category->isCheckedOut ( $me->userid ))
					$category->checkout ( $me->userid );
			} else {
				// New category is by default child of the first section -- this will help new users to do it right
				$db = JFactory::getDBO ();
				$db->setQuery ( "SELECT a.id, a.name FROM #__kunena_categories AS a WHERE parent_id='0' AND id!='$category->id' ORDER BY ordering" );
				$sections = $db->loadObjectList ();
				KunenaError::checkDatabaseError ();
				$category->parent_id = $parent_id;
				$category->published = 0;
				$category->ordering = 9999;
				$category->accesstype = 'none';
				$category->access = 0;
				$category->pub_recurse = 1;
				$category->admin_recurse = 1;
				$category->pub_access = 0;
				$category->moderated = 1;
			}
			$this->_admincategory = $category;
		}
		return $this->_admincategory;
	}

	public function getAdminOptions() {
		$category = $this->getAdminCategory();
		if (!$category) return false;

		$catList = array ();
		$catList [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_TOPLEVEL' ) );

		// make a standard yes/no list
		$published = array ();
		$published [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_PUBLISHED' ) );
		$published [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_UNPUBLISHED' ) );

		// make a standard yes/no list
		$yesno = array ();
		$yesno [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_ANN_NO' ) );
		$yesno [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_ANN_YES' ) );

		//create custom group levels to include into the public group selectList
		$pub_groups = array ();
		$adm_groups = array ();
		$pub_groups [] = JHTML::_ ( 'select.option', 1, JText::_ ( 'COM_KUNENA_NOBODY' ) );
		$pub_groups [] = JHTML::_ ( 'select.option', 0, JText::_ ( 'COM_KUNENA_EVERYBODY' ) );
		$pub_groups [] = JHTML::_ ( 'select.option', - 1, JText::_ ( 'COM_KUNENA_ALLREGISTERED' ) );

		jimport ( 'joomla.version' );
		$jversion = new JVersion ();
		if ($jversion->RELEASE == 1.5) {
			$acl = JFactory::getACL ();
			$pub_groups = array ();
			$adm_groups = array ();
			$pub_groups [] = JHTML::_ ( 'select.option', 1, JText::_('COM_KUNENA_NOBODY') );
			$pub_groups [] = JHTML::_ ( 'select.option', 0, JText::_('COM_KUNENA_EVERYBODY') );
			$pub_groups [] = JHTML::_ ( 'select.option', - 1, JText::_('COM_KUNENA_ALLREGISTERED') );
			$pub_groups = array_merge ( $pub_groups, $acl->get_group_children_tree ( null, 'Registered', true ) );
			$adm_groups = array_merge ( $adm_groups, $acl->get_group_children_tree ( null, 'Public Backend', true ) );
			// Create the access control lists for Joomla 1.5
			$accessLists ['pub_access'] = JHTML::_ ( 'select.genericlist', $pub_groups, 'pub_access', 'class="inputbox" size="4"', 'value', 'text', $category->pub_access );
			$accessLists ['admin_access'] = JHTML::_ ( 'select.genericlist', $adm_groups, 'admin_access', 'class="inputbox" size="4"', 'value', 'text', $category->admin_access );
		} else {
			// Create the access control lists for Joomla 1.6
			$accessLists ['pub_access'] = JHTML::_ ( 'access.usergroup', 'pub_access', $category->pub_access, 'class="inputbox" size="4"', false);
			$accessLists ['admin_access'] = JHTML::_ ( 'access.usergroup', 'pub_access', $category->admin_access, 'class="inputbox" size="4"', false);
		}

		// Anonymous posts default
		$post_anonymous = array ();
		$post_anonymous [] = JHTML::_ ( 'select.option', '0', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_REG' ) );
		$post_anonymous [] = JHTML::_ ( 'select.option', '1', JText::_ ( 'COM_KUNENA_CATEGORY_ANONYMOUS_X_ANO' ) );

		$cat_params = array ();
		$cat_params['ordering'] = 'ordering';
		$cat_params['toplevel'] = 1;
		$cat_params['sections'] = 1;
		$cat_params['direction'] = 1;
		$cat_params['unpublished'] = 1;
		$cat_params['catid'] = $category->id;
		$cat_params['action'] = 'admin';

		$lists = array ();
		$lists ['access'] = KunenaFactory::getAccessControl()->getAccessLevelsList($category);
		$lists ['pub_access'] = $accessLists ['pub_access'];
		$lists ['admin_access'] = $accessLists ['admin_access'];
		$lists ['categories'] = JHTML::_('kunenaforum.categorylist', 'parent_id', 0, null, $cat_params, 'class="inputbox"', 'value', 'text', $category->parent_id);
		$lists ['published'] = JHTML::_ ( 'select.genericlist', $published, 'published', 'class="inputbox"', 'value', 'text', $category->published );
		$lists ['pub_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'pub_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->pub_recurse );
		$lists ['admin_recurse'] = JHTML::_ ( 'select.genericlist', $yesno, 'admin_recurse', 'class="inputbox" size="1"', 'value', 'text', $category->admin_recurse );
		$lists ['forumLocked'] = JHTML::_ ( 'select.genericlist', $yesno, 'locked', 'class="inputbox" size="1"', 'value', 'text', $category->locked );
		$lists ['forumModerated'] = JHTML::_ ( 'select.genericlist', $yesno, 'moderated', 'class="inputbox" size="1"', 'value', 'text', $category->moderated );
		$lists ['forumReview'] = JHTML::_ ( 'select.genericlist', $yesno, 'review', 'class="inputbox" size="1"', 'value', 'text', $category->review );
		$lists ['allow_polls'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_polls', 'class="inputbox" size="1"', 'value', 'text', $category->allow_polls );
		$lists ['allow_anonymous'] = JHTML::_ ( 'select.genericlist', $yesno, 'allow_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->allow_anonymous );
		$lists ['post_anonymous'] = JHTML::_ ( 'select.genericlist', $post_anonymous, 'post_anonymous', 'class="inputbox" size="1"', 'value', 'text', $category->post_anonymous );
		return $lists;
	}

	function getAdminModerators() {
		$category = $this->getAdminCategory();
		if (!$category) return false;

		$moderators = KunenaUserHelper::loadUsers($category->getModerators(false));
		return $moderators;
	}
}