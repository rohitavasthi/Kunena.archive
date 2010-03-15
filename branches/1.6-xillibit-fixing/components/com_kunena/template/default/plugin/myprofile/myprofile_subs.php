<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/
defined( '_JEXEC' ) or die();


global $total, $limitstart, $limit;
$kunena_db = &JFactory::getDBO();
?>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<form action = "<?php echo JRoute::_(KUNENA_LIVEURLREL.'&amp;func=myprofile&amp;do=unsubscribe'); ?>" method = "post" name = "postform">
	<input type = "hidden" name = "do" value = "unsubscribe"/>
	<table class = "kblocktable" id = "kforumprofile_sub" border = "0" cellspacing = "0" cellpadding = "0" width="100%">
		<thead>
			<tr>
				<th colspan = "5">
					<div class = "ktitle_cover">
						<span class = "ktitle"><?php echo JText::_('COM_KUNENA_MY_SUBSCRIPTIONS'); ?></span>
					</div></th>
			</tr>
		</thead>

		<tbody id = "kuserprofile_tbody">
			<tr class = "ksth">
				<th class = "th-1 ksectiontableheader"><?php echo JText::_('COM_KUNENA_GEN_TOPICS'); ?>
				</th>

				<th class = "th-2 ksectiontableheader" style = "text-align:center; width:25%"><?php echo JText::_('COM_KUNENA_GEN_AUTHOR'); ?>
				</th>

				<th class = "th-3 ksectiontableheader" style = "text-align:center; width:25%"><?php echo JText::_('COM_KUNENA_GEN_DATE'); ?>
				</th>

				<th class = "th-3 ksectiontableheader" style = "text-align:center; width:5%"><?php echo JText::_('COM_KUNENA_GEN_HITS'); ?>
				</th>

				<th class = "th-4 ksectiontableheader"><?php echo JText::_('COM_KUNENA_GEN_DELETE'); ?>
				</th>
			</tr>

			<?php
			$enum = 1; //reset value
			$tabclass = array
			(
				"sectiontableentry1",
				"sectiontableentry2"
			);         //alternating row CSS classes

			$k    = 0; //value for alternating rows

			jimport('joomla.html.pagination');
			$pageNav = new JPagination($total, $limitstart, $limit);

			if ($this->kunena_csubslist > 0)
			{
				foreach ($this->kunena_subslist as $subs)
				{ //get all message details for each subscription
					$kunena_db->setQuery("SELECT * FROM #__fb_messages WHERE id='{$subs->thread}'");
					$subdet = $kunena_db->loadObjectList();
						check_dberror("Unable to load messages.");

					foreach ($subdet as $sub)
					{
						$k = 1 - $k;
?>
						<tr class="k<?php echo $tabclass[$k];?>">
							<td class="td-1" width="54%" align="left"><?php echo $enum;?>:
								<a href="<?php
									echo JRoute::_(KUNENA_LIVEURLREL . '&amp;func=view&amp;catid=' . $sub->catid .
										'&amp;id=' . $sub->id);?>"><?php
									echo kunena_htmlspecialchars(stripslashes($sub->subject));?></a>
							</td>

							<td class = "td-2" style = "text-align:center; width:15%"> <?php
								echo kunena_htmlspecialchars(stripslashes($sub->name)); ?></td>

							<td class = "td-3" style = "text-align:center; width:25%"> <?php
								echo '' . date(JText::_('COM_KUNENA_DATETIME'), $sub->time) . ''; ?></td>

							<td class = "td-4" style = "text-align:center; width:5%"> <?php echo $sub->hits; ?></td>

							<td class = "td-5" width = "1%">
								<input id = "cid<?php echo $enum;?>" name = "cid[]" value = "<?php
									echo $subs->thread; ?>"  type = "checkbox"/>
							</td>
						</tr>
<?php
						$enum++;
					}
				}
			?>

				<tr>
					<td colspan = "5" class = "kprofile-bottomnav" style = "text-align:right">
<?php echo JText::_('COM_KUNENA_USRL_DISPLAY_NR'); ?>

<?php
// echo $pageNav->getLimitBox("index.php?option=com_kunena&amp;func=myprofile&amp;do=showsub" . KUNENA_COMPONENT_ITEMID_SUFFIX);
?>

			<input type = "submit" class = "button" value = "<?php echo JText::_('COM_KUNENA_GEN_DELETE');?>"/>
					</td>
				</tr>

			<?php
			}
			else
			{
				echo '<tr class="k' . $tabclass[$k] . '"><td class="td-1" colspan = "5" >' . JText::_('COM_KUNENA_USER_NOSUBSCRIPTIONS') . '</td></tr>';
			}
			?>

			<tr><td colspan = "5" class = "kprofile-bottomnav">
					<?php
					// TODO: fxstein - Need to perform SEO cleanup
					echo $pageNav->getPagesLinks("index.php?option=com_kunena&amp;func=myprofile&amp;do=showsub" . KUNENA_COMPONENT_ITEMID_SUFFIX);
					?>

					<br/>
<?php echo $pageNav->getPagesCounter(); ?>
				</td>
			</tr>
		</tbody>
	</table>
</form>
</div>
</div>
</div>
</div>
</div>