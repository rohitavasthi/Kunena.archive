<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2009 Kunena Team All rights reserved
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

defined( '_JEXEC' ) or die('Restricted access');

$fbConfig =& CKunenaConfig::getInstance();
//Some initial thingies needed anyway:
if (!isset($htmlText)) $htmlText = '';

include_once(KUNENA_PATH_LIB .DS. 'kunena.bbcode.js.php');
?>
<div class="<?php echo $boardclass; ?>_bt_cvr1">
<div class="<?php echo $boardclass; ?>_bt_cvr2">
<div class="<?php echo $boardclass; ?>_bt_cvr3">
<div class="<?php echo $boardclass; ?>_bt_cvr4">
<div class="<?php echo $boardclass; ?>_bt_cvr5">
<table class = "fb_blocktable<?php echo $objCatInfo->class_sfx; ?>" id="fb_postmessage"  border = "0" cellspacing = "0" cellpadding = "0" width="100%">
    <thead>
        <tr>
            <th colspan = "2">
                <div class = "fb_title_cover fbm">
                    <span class = "fb_title fbl"> <?php echo _POST_MESSAGE; ?>"<?php echo stripslashes($objCatInfo->name); ?>"</span>
                </div>
            </th>
        </tr>
    </thead>

    <tbody id = "fb_post_message">
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "fb_leftcolumn">
                <strong><?php echo _GEN_NAME; ?></strong>:
            </td>

            <?php
            if (($fbConfig->regonly == "1" || $fbConfig->changename == '0') && $kunena_my->id != "" && !$is_Moderator) {
                echo "<td><input type=\"hidden\" name=\"fb_authorname\" size=\"35\" class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /><b>$authorName</b></td>";
            }
            else
            {
                if ($registeredUser == 1) {
                    echo "<td><input type=\"text\" name=\"fb_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"$authorName\" /></td>";
                }
                else
                {
                    echo "<td><input type=\"text\" name=\"fb_authorname\" size=\"35\"  class=\"" . $boardclass . "inputbox postinput\"  maxlength=\"35\" value=\"\" />";
                    echo "<script type=\"text/javascript\">document.postform.fb_authorname.focus();</script></td>";
                    $setFocus = 1;
                }
            }
            ?>
        </tr>

        <?php
        if ($fbConfig->askemail)
        {
            echo '<tr class = "'. $boardclass . 'sectiontableentry2"><td class = "fb_leftcolumn"><strong>' . _GEN_EMAIL . ' *</strong>:</td>';
            if (($fbConfig->regonly == "1" || $fbConfig->changename == '0') && $kunena_my->id != "" && !$is_Moderator) {
                echo "<td>$kunena_my_email</td>";
            }
            else
            {
                echo "<td><input type=\"text\" name=\"email\"  size=\"35\" class=\"" . $boardclass . "inputbox postinput\" maxlength=\"35\" value=\"$kunena_my_email\" /></td>";
            }
            echo '</tr>';
        }
        ?>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <?php
            if (!$fromBot)
            {
            ?>

                <td class = "fb_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "text" class = "<?php echo $boardclass; ?>inputbox postinput" name = "subject" size = "35" maxlength = "<?php echo $fbConfig->maxsubject;?>" value = "<?php echo $resubject;?>" />
                </td>

            <?php
            }
            else
            {
            ?>

                <td class = "fb_leftcolumn">
                    <strong><?php echo _GEN_SUBJECT; ?></strong>:
                </td>

                <td>
                    <input type = "hidden" class = "inputbox" name = "subject" size = "35" maxlength = "<?php echo $fbConfig->maxsubject;?>" value = "<?php echo $resubject;?>" /><?php echo $resubject; ?>
                </td>

            <?php
            }
            ?>

            <?php
            if ($setFocus == 0 && $replyto == 0 && !$fromBot)
            {
                echo "<script type=\"text/javascript\">document.postform.subject.focus();</script>";
                $setFocus = 1;
            }
            ?>
        </tr>

        <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
            <td class = "fb_leftcolumn">
                <strong><?php echo _GEN_TOPIC_ICON; ?></strong>:
            </td>

            <td class = "fb-topicicons">
                <?php
                $topicToolbar = smile::topicToolbar(0, $fbConfig->rtewidth);
                echo $topicToolbar;
                ?>
            </td>
        </tr>


        <?php
        if ($fbConfig->rtewidth == 0) {
            $useRte = 0;
        }
        else {
            $useRte = 1;
        }

        $fbTextArea = smile::fbWriteTextarea('message', $htmlText, $fbConfig->rtewidth, $fbConfig->rteheight, $useRte, $fbConfig->disemoticons);
        echo $fbTextArea;

        if ($setFocus == 0) {
            echo '<tr><td style="display:none;"><script type="text/javascript">document.postform.message.focus();</script></td></tr>';
        }

        //check if this user is already subscribed to this topic but only if subscriptions are allowed
        if ($fbConfig->allowsubscriptions == 1)
        {
            if ($replyto == 0) {
                $fb_thread = -1;
            }
            else
            {
                $kunena_db->setQuery("select thread from #__fb_messages where id=$replyto");
                $fb_thread = $kunena_db->loadResult();
            }

            $kunena_db->setQuery("SELECT thread from #__fb_subscriptions where userid=$kunena_my->id and thread='$fb_thread'");
            $fb_subscribed = $kunena_db->loadResult();

            if ($fb_subscribed == "" || $replyto == 0) {
                $fb_cansubscribe = 1;
            }
            else {
                $fb_cansubscribe = 0;
            }
        }
?>

               <!-- preview -->
        <tr class = "<?php echo $boardclass; ?>sectiontableentry2" id="previewContainer" style="display:none;">
           <td class = "fb_leftcolumn">
                <strong><?php echo _PREVIEW; ?></strong>:
            </td>
           <td>
  <div class="previewMsg" id="previewMsg" style="height:<?php echo $fbConfig->rteheight;?>px;overflow:auto;"></div>
            </td>
        </tr>
<!-- /preview -->
<?php
        if (($fbConfig->allowimageupload || ($fbConfig->allowimageregupload && $kunena_my->id != 0) || $is_Moderator) && ($no_upload == "0" || $no_image_upload == "0"))
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _IMAGE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'fb_button' name = 'attachimage' onmouseover = "helpline('iu')" />

                    <input type = "button" class = "fb_button" name = "addImagePH" value = "<?php echo _POST_ATTACH_IMAGE;?>" style = "cursor:hand; width: 4em" onclick = "javascript:emo(' [img] ');" onmouseover = "helpline('ip')" />
                </td>
            </tr>

        <?php
        }
        ?>

        <?php
        if (($fbConfig->allowfileupload || ($fbConfig->allowfileregupload && $kunena_my->id != 0) || $is_Moderator) && ($no_upload == "0" || $no_file_upload == "0"))
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry2">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _FILE_SELECT_FILE; ?></strong>
                </td>

                <td>
                    <input type = 'file' class = 'fb_button' name = 'attachfile' onmouseover = "helpline('fu')" style = "cursor:hand" />

                    <input type = "button" class = "fb_button" name = "addFilePH" value = "<?php echo _POST_ATTACH_FILE;?>" style = "cursor:hand; width: 4em" onclick = "javascript:emo(' [file] ');" onmouseover = "helpline('fp')" />
                </td>
            </tr>

        <?php
        }

        if ($kunena_my->id != 0 && $fbConfig->allowsubscriptions == 1 && $fb_cansubscribe == 1 && !$editmode)
        {
        ?>

            <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
                <td class = "fb_leftcolumn">
                    <strong><?php echo _POST_SUBSCRIBE; ?></strong>:
                </td>

                <td>
                    <?php
                    if ($fbConfig->subscriptionschecked == 1)
                    {
                    ?>

                            <input type = "checkbox" name = "subscribeMe" value = "1" checked/>

                            <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    else
                    {
                    ?>

                        <input type = "checkbox" name = "subscribeMe" value = "1" />

                        <i><?php echo _POST_NOTIFIED; ?></i>

                    <?php
                    }
                    ?>
                </td>
            </tr>

        <?php
        }
        ?>
		<?php
		// Begin captcha . Thanks Adeptus
		if ($fbConfig->captcha == 1 && $kunena_my->id < 1) { ?>
        <tr class = "<?php echo $boardclass; ?>sectiontableentry1">
            <td class = "fb_leftcolumn">&nbsp;<strong><?php echo _KUNENA_CAPDESC; ?></strong>&nbsp;</td>
            <td align="left" valign="middle" height="35px">&nbsp;<input name="txtNumber" type="text" id="txtNumber" value="" class="fb_button" style="vertical-align:top" size="15">
			<img src="index2.php?option=com_kunena&func=showcaptcha" alt="" />
		 </td>
         </tr>
        <?php
		}
		// Finish captcha
		?>
        <tr>
            <td id="fb_post_buttons" colspan = "2" style = "text-align: center;">
                <input type="submit" name="submit"  class="fb_button" value="<?php echo ' '._GEN_CONTINUE.' ';?>" onclick="return submitForm()" onmouseover = "helpline('submit')" />
                <input type="button" name="preview" class="fb_button" value="<?php echo ' '._PREVIEW.' ';?>"      onClick="fbGetPreview(document.postform.message.value,<?php echo KUNENA_COMPONENT_ITEMID?>);" onmouseover = "helpline('preview');" />
                <input type="button" name="cancel"  class="fb_button" value="<?php echo ' '._GEN_CANCEL.' ';?>"   onclick="javascript:window.history.back();" onmouseover = "helpline('cancel')" />
            </td>
        </tr>
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
<input type="hidden" value="<?php echo KUNENA_DIRECTURL . '/template/default';?>" name="templatePath" />
<input type="hidden" value="<?php echo JRoute::_(KUNENA_LIVEURLREL);?>" name="kunenaPath" />
</form>

</td>

</tr>

<tr>
    <td>
        <?php
        if ($fbConfig->askemail) {
            echo $fbConfig->showemail == '0' ? "<em>* - " . _POST_EMAIL_NEVER . "</em>" : "<em>* - " . _POST_EMAIL_REGISTERED . "</em>";
        }
        ?>
    </td>
</tr>

<tr>
    <td style = "text-align: left;">
        <br/>

    <?php
    $no_upload = "0"; //reset the value.. you just never know..

    if ($fbConfig->showhistory == 1) {
        listThreadHistory($replyto, $fbConfig, $kunena_db);
    }
?>
