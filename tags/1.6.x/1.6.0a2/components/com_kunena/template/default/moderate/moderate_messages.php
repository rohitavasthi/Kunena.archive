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
// Dont allow direct linking
defined( '_JEXEC' ) or die();


$kunena_db = &JFactory::getDBO();
$kunena_app =& JFactory::getApplication();
$kunena_my = &JFactory::getUser();
//securing form elements
$catid = (int)$catid;

if (!CKunenaTools::isModerator($kunena_my->id, $catid)) {
    die ("You are not a moderator!!<br />This error is logged and your IP address has been sent to the SuperAdmin(s) of this site; sorry..");
}

//but we don't send the email; we might do that in the future, but for now we just want to scare 'em off..
// determine what to do
$action = JRequest::getVar('action', 'list');
$cid = JRequest::getVar('cid', array ());

switch ($action)
{
    case JText::_('COM_KUNENA_MOD_DELETE'):
        switch (jbDeletePosts($kunena_db, $cid))
        {
            case -1:
                $kunena_app->redirect(KUNENA_LIVEURL . 'func=review&amp;catid=' . $catid, "ERROR: The post has been deleted but the text could not be deleted\n Check the #__kunena_messages_text table for mesid IN " . explode(',', $cid));

                break;

            case 0:
                $kunena_app->redirect(KUNENA_LIVEURL . '&amp;func=review&amp;catid=' . $catid, JText::_('COM_KUNENA_MODERATION_DELETE_ERROR'));

                break;

            case 1:
            default:
                $kunena_app->redirect(KUNENA_LIVEURL . '&amp;func=review&amp;catid=' . $catid, JText::_('COM_KUNENA_MODERATION_DELETE_SUCCESS'));

                break;
        }

        break;

    case JText::_('COM_KUNENA_MOD_APPROVE'):
        switch (jbApprovePosts($kunena_db, $cid))
        {
            case 0:
                $kunena_app->redirect(KUNENA_LIVEURL . 'amp;func=review&amp;catid=' . $catid, JText::_('COM_KUNENA_MODERATION_APPROVE_ERROR'));

                break;

            default:
            case 1:
                $kunena_app->redirect(KUNENA_LIVEURL . '&amp;func=review&amp;catid=' . $catid, JText::_('COM_KUNENA_MODERATION_APPROVE_SUCCESS'));

                break;
        }

        break;

    default:
    case 'list':
        echo '<p class="sectionname">' . JText::_('COM_KUNENA_MESSAGE_ADMINISTRATION') .'</p>';

        $kunena_db->setQuery("SELECT m.id, m.time, m.name, m.subject, m.hold, t.message FROM #__kunena_messages AS m JOIN #__kunena_messages_text AS t ON m.id=t.mesid WHERE hold='1' AND catid='{$catid}' ORDER BY id ASC");
        $allMes = $kunena_db->loadObjectList();
        KunenaError::checkDatabaseError();

        if (count($allMes) > 0)
            jbListMessages($allMes, $catid);
        else
            echo '<p style="text-align:center">' . JText::_('COM_KUNENA_MODERATION_MESSAGES') . '</p>';

        break;
}
/**
 * Lists messages to be moderated
 * @param array    allMes list of object
 * @param string fbs action string
 */
function jbListMessages($allMes, $catid)
{
    $kunena_config = KunenaFactory::getConfig ();
?>

   <form action="<?php echo CKunenaLink::GetReviewURL(); ?>" name="moderation" method="post">
    <script>
        function ConfirmDelete()
        {
            if (confirm("<?php echo JText::_('COM_KUNENA_MODERATION_DELETE_MESSAGE'); ?>"))
                document.moderation.submit();
            else
                return false;
        }
    </script>

    <table width = "100%" border = 0 cellspacing = 1 cellpadding = 3>
        <tr height = "10" class = "ktable_header">
            <th align = "center">
                <b><?php echo JText::_('COM_KUNENA_GEN_DATE'); ?></b>
            </th>

            <th width = "8%" align = "center">
                <b><?php echo JText::_('COM_KUNENA_GEN_AUTHOR'); ?></b>
            </th>

            <th width = "13%" align = "center">
                <b><?php echo JText::_('COM_KUNENA_GEN_SUBJECT'); ?></b>
            </th>

            <th width = "55%" align = "center">
                <b><?php echo JText::_('COM_KUNENA_GEN_MESSAGE'); ?></b>
            </th>

            <th width = "13%" align = "center">
                <b><?php echo JText::_('COM_KUNENA_GEN_ACTION'); ?></b>
            </th>
        </tr>

        <?php
        $i = 1;
        //avoid calling it each time
        $kunena_emoticons = smile::getEmoticons("");

        foreach ($allMes as $message)
        {
            $i = 1 - $i;
            echo '<tr class="kmessage' . $i . '">';
            echo '<td valign="top">' . CKunenaTimeformat::showDate($message->time) . '</td>';
            echo '<td valign="top">' . $message->name . '</td>';
            echo '<td valign="top"><b>' . $message->subject . '<b></td>';


            $fb_message_txt = $message->message;
            echo '<td valign="top">' . smile::smileReplace($fb_message_txt, 0, $kunena_config->disemoticons, $kunena_emoticons) . '</td>';
            echo '<td valign="top"><input type="checkbox" name="cid[]" value="' . $message->id . '" /></td>';
            echo '</tr>';
        }
        ?>

<tr>
    <td colspan = "5" align = "center" valign = "top" style = "text-align:center">
        <input type = "hidden" name = "catid" value = "<?php echo $catid; ?>"/>

        <input type = "submit"
            class = "button" name = "action" value = "<?php echo JText::_('COM_KUNENA_MOD_APPROVE'); ?>" border = "0"> <input type = "submit" class = "button" name = "action" onclick = "ConfirmDelete()" value = "<?php echo JText::_('COM_KUNENA_MOD_DELETE'); ?>" border = "0">
    </td>
</tr>

<tr height = "10" bgcolor = "#e2e2e2">
    <td colspan = "5">
        &nbsp;
    </td>
</tr>
    </table>

    </form>

<?php
}
/**
 * delete selected messages
 * @param object database
 * @param array    cid post ids
 * @param string fbs action string
 */
function jbDeletePosts($kunena_db, $cid)
{
    if (count($cid) == 0)
        return 0;

    $ids = implode(',', $cid);
    $kunena_db->setQuery('DELETE FROM `#__kunena_messages` WHERE `id` IN (' . $ids . ')');

    if ($kunena_db->query())
    {
        $kunena_db->setQuery('DELETE FROM `#__kunena_messages_text` WHERE `mesid` IN (' . $ids . ')');

        if ($kunena_db->query())
            return 1;
        else
            return -1;
    }
    KunenaError::checkDatabaseError();

    return 0;
}
/**
 * approve selected messages
 * @param object database
 * @param array cid post ids
 */
function jbApprovePosts($kunena_db, $cid)
{
    if (count($cid) == 0)
        return 0;

    $ret = 1;
    reset($cid);
    foreach($cid as $id) {
    	$id = (int)$id;
        $newQuery = "SELECT * FROM #__kunena_messages WHERE id='{$id}'";
        $kunena_db->setQuery($newQuery, 0, 1);
        $msg = null;
        $msg = $kunena_db->loadObject();
        if (KunenaError::checkDatabaseError()) return 0;
        if(!$msg) { continue; }
        // continue stats
        $kunena_db->setQuery("UPDATE `#__kunena_messages` SET hold='0' WHERE id='{$id}'");
        $kunena_db->query();
		if (KunenaError::checkDatabaseError()) return 0;
        CKunenaTools::modifyCategoryStats($id, $msg->parent, $msg->time, $msg->catid);
    }
    return $ret;
}
?>
