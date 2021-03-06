<?php
/**
 * @version		$Id: view.raw.php 994 2009-08-16 08:18:03Z fxstein $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

kimport('application.view');
kimport('html.bbcode');

/**
 * The Raw Kunena recent view.
 *
 * @package		Kunena
 * @subpackage	com_kunena
 * @since		1.6
 */
class KunenaViewPost extends KView
{
	/**
	 * Display the view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function display($tpl = null)
	{
		$this->assignRef ('state', $this->get ('State'));

		// Create shortcut to parameters.
		$params = $this->state->get('params');

		$bbcode = KBBCode::getInstance();
/*
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		foreach ($this->path as &$category) $pathway->addItem($this->escape($category->name), JHtml::_('klink.categories', 'url', $category->id, '', ''));
		$pathway->addItem($this->escape($this->messages[0]->subject));

		$category = end($this->path);
		$this->assign('description', $bbcode->Parse(stripslashes($category->headerdesc)));

		jimport( 'joomla.application.menu' );
		$menu = JSite::getMenu();
		$menuitem = $menu->getActive();

		$this->assign ( 'title', ($params->get('show_page_title') && $menuitem->query['view'] == 'messages' && $menuitem->query['thread'] == $this->state->thread ?
		$params->get('page_title') : $this->messages[0]->subject));
*/
		parent::display($tpl);
	}
}