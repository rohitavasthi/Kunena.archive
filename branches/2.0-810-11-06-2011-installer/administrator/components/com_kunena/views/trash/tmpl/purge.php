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

$document = JFactory::getDocument();
$document->addStyleSheet ( JURI::base(true).'/components/com_kunena/media/css/admin.css' );
?>
<div id="kadmin">
	<div class="kadmin-left"><?php include KPATH_ADMIN.'/views/common/tmpl/menu.php'; ?></div>
	<div class="kadmin-right">
	<form action="<?php echo KunenaRoute::_('index.php?option=com_kunena') ?>" method="post" name="adminForm">
			<table class="adminheading" cellpadding="4" cellspacing="0" border="0" width="100%"></table>
			<table class="adminlist" border="0" cellspacing="0" cellpadding="3" width="100%">
				<tr>
					<td><strong><?php echo JText::_('COM_KUNENA_NUMBER_ITEMS'); ?>:</strong>
						<br />
						<font color="#000066"><strong><?php echo count( $this->purgeitems ); ?></strong></font>
						<br /><br />
					</td>
					<td  valign="top" width="25%">
						<strong><?php echo JText::_('COM_KUNENA_ITEMS_BEING_DELETED'); ?>:</strong>
						<br />
						<?php echo "<ol>";
							foreach ( $this->purgeitems as $item ) {
								echo "<li>". $this->escape($item->subject) ."</li>";
							}
							echo "</ol>";
						?>
					</td>
					<td valign="top"><span style="color:red;"><strong><?php echo JText::_('COM_KUNENA_PERM_DELETE_ITEMS'); ?></strong></span>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_kunena" />
			<input type="hidden" name="view" value="trash" />
			<input type="hidden" name="task" value="purge" />
			<input type="hidden" name="boxchecked" value="1" />
			<input type="hidden" name="md5" value="<?php echo $this->md5Calculated ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
  </div>
  <div class="kadmin-footer">
		<?php echo KunenaVersion::getLongVersionHTML (); ?>
	</div>
</div>