<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$kunena_config = KunenaFactory::getConfig ();
$document = JFactory::getDocument ();
$document->setTitle ( JText::_('COM_KUNENA_GEN_HELP') . ' - ' . $kunena_config->board_title );
$introtext = CKunenaTools::getRulesHelpDatas($kunena_config->help_cid);
?>
<div class="kblock">
	<div class="ktitle">
		<h1><?php echo JText::_('COM_KUNENA_FORUM_HELP'); ?></h1>
	</div>
	<div class="kcontainer">
		<div class="khelprulescontent">
			<?php echo $introtext; ?>
		</div>
	</div>
</div>
<!-- Begin: Forum Jump -->
<div class="kblock">
	<div class="kcontainer">
		<div class="khelprulesjump">
		<?php
		if ($kunena_config->enableforumjump) {
			CKunenaTools::loadTemplate('/forumjump.php');
		}
		?>
		</div>
	</div>
</div>
<!-- Finish: Forum Jump -->