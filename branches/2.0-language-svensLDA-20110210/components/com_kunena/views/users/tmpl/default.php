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

$this->document->addScriptDeclaration( "// <![CDATA[
function tableOrdering( order, dir, task ) {
	var form=document.adminForm;
	form.filter_order.value=order;
	form.filter_order_Dir.value=dir;
	document.adminForm.submit( task );
}
document.addEvent('domready', function() {
	// Attach auto completer to the following ids:
	new Autocompleter.Request.JSON('kusersearch', '" . CKunenaLink::GetJsonURL('autocomplete', 'getuser') . "', { 'postVar': 'value' });
});
// FIXME: do not use alert:
function validate() {
	if ((document.usrlform.search == '') || (document.usrlform.search.value == '')) {
		alert('" . JText::_('COM_KUNENA_USRL_SEARCH_ALERT') . "');
		return false;
	} else {
		return true;
	}
}
// ]]>");
?>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="searchuser_tbody"></a></span>
		<h2><span><?php printf(JText::_('COM_KUNENA_USRL_REGISTERED_USERS'), $this->app->getCfg('sitename'), intval($this->total));?></span></h2>
	</div>
	<div class="kcontainer" id="searchuser_tbody">
		<div class="kbody">
			<div class="search-user">
				<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistPostURL(); ?>" onsubmit="return validate()">
					<input id="kusersearch" type="text" name="search" class="inputbox"
						value="<?php echo $this->escape($this->state->get('list.search', JText::_('COM_KUNENA_USRL_SEARCH'))); ?>" onblur="if(this.value=='') this.value='<?php echo $this->escape(JText::_('COM_KUNENA_USRL_SEARCH')); ?>';" onfocus="if(this.value=='<?php echo $this->escape(JText::_('COM_KUNENA_USRL_SEARCH')); ?>') this.value='';" />
					<input type="image" src="<?php echo KUNENA_TMPLTMAINIMGURL .'/images/usl_search_icon.png' ;?>" alt="<?php echo JText::_('COM_KUNENA_USRL_SEARCH'); ?>" align="top" style="border: 0px;" />
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
			</div>
			<div class="userlist-jump">
				<?php $this->displayForumjump(); ?>
			</div>
		</div>
	</div>
</div>
<div class="kblock">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" title="<?php echo JText::_('COM_KUNENA_TOGGLER_COLLAPSE') ?>" rel="userlist-tbody"></a></span>
		<h2><span><?php echo JText::_('COM_KUNENA_USRL_USERLIST'); ?></span></h2>
	</div>
	<div class="kcontainer" id="userlist-tbody">
		<div class="kbody">
			<form action="<?php echo CKunenaLink::GetUserlistPostURL(); ?>" method="post" name="adminForm">
				<input type="hidden" name="view" value="users">
				<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.order'); ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->get('list.order_dir'); ?>" />
				<table>
					<tr class="ksth userlist">
						<th class="frst"> # </th>

						<?php if ($this->config->userlist_online) : ?>
						<th><?php echo JText::_('COM_KUNENA_USRL_ONLINE'); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_avatar) : ?>
						<th><?php echo JText::_('COM_KUNENA_USRL_AVATAR'); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_name) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_NAME', 'name', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_username) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_USERNAME', 'username', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_posts) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_POSTS', 'posts', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_karma) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_KARMA', 'karma', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_email) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_EMAIL', 'email', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_usertype) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_USERTYPE', 'usertype', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_joindate) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_JOIN_DATE', 'registerDate', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_lastvisitdate) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_LAST_LOGIN', 'lastvisitDate', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>

						<?php if ($this->config->userlist_userhits) : ?>
						<th class="usersortable"><?php echo JHTML::_( 'grid.sort', 'COM_KUNENA_USRL_HITS', 'uhits', $this->state->get('list.order_dir'), $this->state->get('list.order')); ?></th>
						<?php endif; ?>
					</tr>

					<?php
					$i=1;

					foreach ($this->users as $user) :
						$evenodd = $i % 2;
						$nr = $i + $this->state->get('list.start');
						$i++;

						if ($evenodd == 0) {
							$usrl_class="row1";
						}
						else {
							$usrl_class="row2";
						}

						$profile=KunenaFactory::getUser(intval($user->id));
						$uslavatar=$profile->getAvatarLink('usl_avatar', 'list');
					?>

					<tr class="k<?php echo $usrl_class ;?>">
						<td class="kcol-first"><?php echo $nr; ?></td>

						<?php if ($this->config->userlist_online) : ?>
						<td class="kcol-mid">
							<span class="kicon-button kbuttononline-<?php echo $profile->isOnline(true) ?>"><span class="online-<?php echo $profile->isOnline(true) ?>"><span><?php echo $profile->isOnline() ? JText::_('COM_KUNENA_ONLINE') : JText::_('COM_KUNENA_OFFLINE'); ?></span></span></span>
						</td>
						<?php endif; ?>

						<?php if ($this->config->userlist_avatar) : ?>
						<td class="kcol-mid"><?php echo !empty($uslavatar) ? CKunenaLink::GetProfileLink(intval($user->id), $uslavatar) : '&nbsp;' ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_name) : ?>
						<td class="kcol-mid"><?php echo CKunenaLink::GetProfileLink(intval($user->id), $this->escape($user->name)); ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_username) : ?>
						<td class="kcol-mid"><?php echo CKunenaLink::GetProfileLink(intval($user->id), $this->escape($user->username)); ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_posts) : ?>
						<td class="kcol-mid"><?php echo intval($user->posts); ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_karma) : ?>
						<td class="kcol-mid"><?php echo intval($user->karma); ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_email) : ?>
						<td class="kcol-mid"><a href=mailto:"<?php echo $this->escape($user->email) ?>"><?php echo $this->escape($user->email) ?></a></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_usertype) : ?>
						<td class="kcol-mid"><?php echo $this->escape($user->usertype) ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_joindate) : ?>
						<td class="kcol-mid" title="<?php echo CKunenaTimeformat::showDate($user->registerDate, 'ago', 'utc') ?>"><?php echo CKunenaTimeformat::showDate($user->registerDate, 'datetime_today', 'utc') ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_lastvisitdate) : ?>
						<td class="kcol-mid" title="<?php echo CKunenaTimeformat::showDate($user->lastvisitDate, 'ago', 'utc') ?>"><?php echo CKunenaTimeformat::showDate($user->lastvisitDate, 'datetime_today', 'utc') ?></td>
						<?php endif; ?>

						<?php if ($this->config->userlist_userhits) : ?>
						<td class="kcol-mid"><?php echo $this->escape($profile->uhits) ?></td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</table>
			</form>
			<form name="usrlform" method="post" action="<?php echo CKunenaLink::GetUserlistPostURL(); ?>" onsubmit="return false;">
				<table class="kblocktable" id="kuserlist-bottom">
					<tr>
						<td>
							<div>
								<?php
								// TODO: fxstein - Need to perform SEO cleanup
								echo $this->pageNav->getPagesLinks(CKunenaLink::GetUserlistURL());
								?>
							<span style="float:right">
								<?php echo $this->pageNav->getPagesCounter(); ?> | <?php echo JText::_('COM_KUNENA_USRL_DISPLAY_NR'); ?> <?php echo $this->pageNav->getLimitBox(CKunenaLink::GetUserlistURL()); ?>
							</span>
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?php $this->displayWhoIsOnline(); ?>