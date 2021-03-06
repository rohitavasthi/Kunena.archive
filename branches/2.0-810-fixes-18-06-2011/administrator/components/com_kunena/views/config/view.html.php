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
 * About view for Kunena config backend
 */
class KunenaAdminViewConfig extends KunenaView {
	function displayDefault() {
		$this->setToolBarDefault();
		$this->lists = $this->get('Configlists');
		$this->config = KunenaFactory::getConfig ();

		$this->display ();
	}

	protected function setToolBarDefault() {
		JToolBarHelper::title ( '&nbsp;', 'kunena.png' );
		JToolBarHelper::spacer();
		JToolBarHelper::save('save');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('setdefault', 'restore.png','restore_f2.png', 'COM_KUNENA_RESET_CONFIG', false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel('config');
	}
}
