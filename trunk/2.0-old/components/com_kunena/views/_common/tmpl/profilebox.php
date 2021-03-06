<?php
/**
 * @version		$Id: $
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2008 - 2009 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

defined('_JEXEC') or die;

$profile = KFactory::getProfile();
$pms = KFactory::getPMS();
//print_r($this->profile);
?>
<div class="profilebox">
	<div>
		<?php echo JHtml::_('klink.user', 'atag', $this->profile->userid, $this->escape($this->profile->name), $this->escape($this->profile->name)); ?>
	</div>
	<div>
<?php
echo $profile->showAvatar($this->profile->userid, 'avatar', false);
?>
	</div>
<?php if ($this->profile->userid): ?>
	<div class="cover"><?php echo $this->profile->rank_title; ?></div>

	<div class="cover">
		<img src="<?php echo KURL_COMPONENT_MEDIA; ?>images/ranks/<?php echo $this->profile->rank_image; ?>" alt="" />
	</div>
	<div class="cover">Posts: <?php echo (int)$this->profile->posts; ?></div>
<!-- 	<div class="cover">
		<div>
			<img src="<?php echo KURL_COMPONENT_MEDIA; ?>images/graph/col9m.png" height="4" width="0" alt="graph" />
			<img src="<?php echo KURL_COMPONENT_MEDIA; ?>eimages/moticons/graph.gif" height="4" width="60" alt="graph" />
		</div>
	</div> -->
	<div>
		<img src="<?php echo KURL_COMPONENT_MEDIA; ?>images/icons/offline.gif" alt="User Offline" />
		<?php echo $pms->showSendPMIcon($this->profile->userid); ?>
		<?php echo JHtml::_('klink.user', 'atag', $this->profile->userid, '<img src="'.KURL_COMPONENT_MEDIA.'images/icons/profile.gif" alt="Click here to see the profile of this user" title="Click here to see the profile of this user" />', $this->escape($this->profile->name)); ?>
	</div>
	<div></div>
<?php else: ?>
	<div class="cover">Visitor</div>
<?php endif; ?>
</div>
