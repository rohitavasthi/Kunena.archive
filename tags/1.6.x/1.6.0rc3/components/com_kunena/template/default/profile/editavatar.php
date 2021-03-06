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
 **/
defined( '_JEXEC' ) or die();
$i=0;
?>

<div class="kblock keditavatar">
	<div class="kheader">
		<h2><span><?php echo JText::_('COM_KUNENA_PROFILE_EDIT_AVATAR_TITLE'); ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
<table class="<?php echo isset ( $this->objCatInfo->class_sfx ) ? ' kblocktable' . $this->escape($this->objCatInfo->class_sfx) : ''; ?>">
<?php if ($this->profile->avatar): ?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first">
			<label for="kavatar-keep"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_KEEP');?></label>
		</td>
		<td class="kcol-mid">
			<input id="kavatar-keep" type="radio" name="avatar" value="keep" checked="checked" />
		</td>
	</tr>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first">
			<label for="kavatar-delete"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_DELETE');?></label>
		</td>
		<td class="kcol-mid">
			<input id="kavatar-delete" type="radio" name="avatar" value="delete" />
		</td>
	</tr>
<?php endif; ?>
<?php if ($this->config->allowavatarupload):?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first">
			<label for="kavatar-upload"><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_UPLOAD');?></label>
			</td><td class="kcol-mid">
			<div><input id="kavatar-upload" type="file" class="button" name="avatarfile" /></div>
		</td>
	</tr>
<?php endif; ?>
<?php if ($this->config->allowavatargallery):?>
		<tr class="krow<?php echo ($i^=1)+1;?>">
			<td class="kcol-first">
			<label><?php echo JText::_('COM_KUNENA_PROFILE_AVATAR_GALLERY');?></label>
		</td>
		<td class="kcol-mid">
			<table class="kblocktable" id ="kforumua_gal">
				<tr>
					<td class="kuadesc"><?php echo $this->galleries; ?></td>
				</tr>
				<tr>
					<td class="kuadesc">
					<?php //FIXME: move to js folder ?>
					<script type="text/javascript">
						<!--
						function switch_avatar_category(gallery)
						{
						if (gallery == "")
							return;
						var url = "<?php echo CKunenaLink::GetMyProfileUrl ( intval($this->user->id), 'edit', false, '&gallery=_GALLERY_' )?>";
						var urlreg = new  RegExp("_GALLERY_","g");
						location.href=url.replace(urlreg,gallery);
						}
						// -->
					</script>

					<?php
					$kid = 0;
					foreach ($this->galleryimg as $avatarimg) : ?>
					<span>
						<label for="kavatar<?php echo $kid ?>"><img src="<?php echo $this->galleryurl .'/'. ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>" alt="" /></label>
						<input id="kavatar<?php echo $kid ?>" type="radio" name="avatar" value="<?php echo 'gallery/' . ($this->gallery ? $this->gallery.'/':'') . $avatarimg ?>"/>
					</span>
					<?php $kid++; endforeach; ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php endif; ?>
</table>
		</div>
	</div>
</div>