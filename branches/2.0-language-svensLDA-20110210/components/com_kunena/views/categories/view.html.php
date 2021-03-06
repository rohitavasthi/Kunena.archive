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

kimport ( 'kunena.view' );

/**
 * Categories View
 */
class KunenaViewCategories extends KunenaView {
	function displayDefault($tpl = null) {
		$this->assignRef ( 'category', $this->get ( 'Category' ) );
		if ($this->category->id && !$this->get ( 'Category')->authorise('read')) {
			$this->setError($this->category->getError());
		}
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->sections = isset($this->categories[0]) ? $this->categories[0] : array();
		$this->me = KunenaFactory::getUser();
		$this->config = KunenaFactory::getConfig();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			if ($this->me->isAdmin(null)) {
				$this->category_manage = CKunenaLink::GetHrefLink(KunenaRoute::_('index.php?option=com_kunena&view=categories&layout=manage&catid='.$this->category->id), $this->getButton ( 'moderate', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES') ), $title = '', 'nofollow', 'kicon-button kbuttonmod btn-left', '', JText::_('COM_KUNENA_BUTTON_MANAGE_CATEGORIES_LONG'));
			}

			// meta description and keywords
			$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
			$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

			$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->setTitle ( JText::_('COM_KUNENA_VIEW_CATEGORIES_DEFAULT') );
			$this->display ($tpl);
		}
	}

	function displayUser($tpl = null) {
		$this->assignRef ( 'categories', $this->get ( 'Categories' ) );
		$this->me = KunenaFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->config = KunenaFactory::getConfig();

		$errors = $this->getErrors();
		if ($errors) {
			$this->displayNoAccess($errors);
		} else {
			$this->header = $this->title = JText::_('COM_KUNENA_CATEGORY_SUBSCRIPTIONS');

			// meta description and keywords
			$metaDesc = (JText::_('COM_KUNENA_CATEGORIES') . ' - ' . $this->config->board_title );
			$metaKeys = (JText::_('COM_KUNENA_CATEGORIES') . ', ' . $this->config->board_title . ', ' . JFactory::getApplication ()->getCfg ( 'sitename' ));

			$metaDesc = $this->document->get ( 'description' ) . '. ' . $metaDesc;
			$this->document->setMetadata ( 'keywords', $metaKeys );
			$this->document->setDescription ( $metaDesc );

			$this->setTitle ( JText::_('COM_KUNENA_VIEW_CATEGORIES_USER') );
			$this->display ($tpl);
		}
	}

	function displayManage($tpl) {
		$admin = KunenaForumCategoryHelper::getCategories(false, false, 'admin');
		if (empty($admin)) {
			$this->setError(JText::_('COM_KUNENA_NO_ACCESS'));
			$this->displayNoAccess($this->getErrors());
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_kunena',JPATH_ADMINISTRATOR);

		$this->assignRef ( 'categories', $this->get ( 'AdminCategories' ) );
		$this->assignRef ( 'navigation', $this->get ( 'AdminNavigation' ) );
		$header = JText::_('COM_KUNENA_ADMIN');
		$this->assign ( 'header', $header );
		$this->setTitle ( $header );

		$this->display ($tpl);
	}

	public function getCategoryIcon($category, $thumb = false) {
		if (! $thumb) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if ($category->getNewCount()) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_on.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_on.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_off.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_off.gif\" border=\"0\" class='kforum-cat-image' alt=\" \"  />";
					} else {
						return $this->getIcon ( 'kreadforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_notlogin.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_notlogin.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		} elseif ($this->config->showchildcaticon) {
			if ($this->config->shownew && $this->me->userid != 0) {
				if ($category->getNewCount()) {
					// Check Unread    Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_on_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_on_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kunreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NEWPOST' ) );
					}
				} else {
					// Check Read Cat Images
					if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_off_childsmall.gif" )) {
						return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_off_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
					} else {
						return $this->getIcon ( 'kreadforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
					}
				}
			} else {
				// Not Login Cat Images
				if (is_file ( KUNENA_ABSCATIMAGESPATH . $category->id . "_notlogin_childsmall.gif" )) {
					return "<img src=\"" . KUNENA_LIVEUPLOADEDPATH ."/{$config->catimagepath}/" . $category->id . "_notlogin_childsmall.gif\" border=\"0\" class='kforum-cat-image' alt=\" \" />";
				} else {
					return $this->getIcon ( 'knotloginforum-sm', JText::_ ( 'COM_KUNENA_GEN_FORUM_NOTNEW' ) );
				}
			}
		}
		return '';
	}

	function displayInfoMessage() {
		$this->common->header = $this->escape($this->category->name);
		$this->common->body = '<p>'.JText::sprintf('COM_KUNENA_VIEW_CATEGORIES_INFO_EMPTY', $this->escape($this->category->name)).'</p>';
		$this->common->html = true;
		echo $this->common->display('default');
	}
}