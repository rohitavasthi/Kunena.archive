<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2009 www.kunena.com All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*/

// no direct access
defined( '_JEXEC' ) or die('Restricted access');
?>

Changelog
------------
This is a non-exhaustive (but still near complete) changelog for
the Kunena 1.x, including beta and release candidate versions.
The Kunena 1.x is based on the FireBoard releases but includes some
drastic technical changes.
Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Kunena 1.5.0

3-April-2009 fxstein
^ [#15784] Merge final 1.0.9 fixes from revision 450 to 601

24-March-2009 fxstein
^ [#15627] Prepare for 1.5.0a Alpha release

24-March-2009 Matias
# [#15131] Changing password works again

21-March-2009 Matias
# [#15131] Attachment uploads are working again
# [#15131] Fixes for JRequest::getVar()

17-March-2009 Matias
# [#15131] Massive renaming of defines
# [#15131] Replaced many legacy functions
# [#15131] Replaced all occurences of $my_id with $my->id
# [#15131] Replaced global $database with local reference of JFactory::getDBO()
# [#15131] Replaced global $my with local reference of JFactory::getUser()
# [#15131] Replaced global $acl with local reference of JFactory::getACL()
# [#15131] Replaced remaining mosGetParam() with JRequest::getVar()

14-March-2009 Matias
# [#15131] Replaced many legacy functions, removed misplaced JText::_()

7-March-2009 fxstein
- [#15378] Drop CKunenaTemplate - not longer needed/supported
# [#15378] Re-apply fixes to class.kunena.php that did not make the merge

6-March-2009 Matias
# [#15378] Post merge cleanup: PHP errors

6-March-2009 Matias
# [#15378] Merge new changes from branch 1.5 to a tree with history (frontend)
# [#15378] Merge latest revision from Branch 1.0 back into 1.5 (reverse merge)

5-March-2009 fxstein
^ [#15378] Kunena branch 1.0.9 merged into 1.5 (r498)
# [#15378] Post merge cleanup: Incorrect list assignement in configuration

25-February-2009 fxstein
# [#15131] Initial cut at working Kunena 1.5 native backend

23-February-2009 fxstein
# Various syntax errors after initial port fixed


Kunena 1.0.9

3-April-2009 fxstein
# [#15781] Minor typo in language file: Missing closing tag ] for twitter url
# [#15782] Session category check regression

3-April-2009 Matias
# [#15671] CB integration: do not pass array() as reference
# [#15671] CB integration: extra parameters 'subject', 'messagetext' and 'messageobject' to be passed as reference into showProfile()

2-April-2009 fxstein
+ [#15724] Added bbcode and smilie support for forum headers AND descriptions in default AND default_ex
+ [#15724] Added bbcode and smilie support to forum announcements
^ [#15771] Minor change: Update sample data on fresh installs to contain bbcode in forum headers & descriptions instead of html

2-April-2009 Matias
^ [#15671] CB integration: Show Profile: Provide all needed information to CB plugin (Kunena Profile, username from message)
# [#15761] Added missing php close tags in lib/ where they were missing
# [#15567] 1.0.9 internal regression: security issue fixed in search

1-April-2009 fxstein
# [#15761] Regression fix: Added missing php close tag to class.kunena.php

1-April-2009 Matias
^ [#15671] CB integration: Changed CB Migration API
^ [#15671] CB integration: If internal fbprofile page is accessed, forward request to CB
+ [#15671] CB integration: New class CKunenaVersion, make lib/kunena.version.php safe to be included by external components
! New translations: make version string localized
+ [#15671] CB integration: make lib/kunena.user.class.php and lib/kunena.config.class.php self-contained

31-March-2009 Matias
# [#15567] Implement working advanced search: fix user search without search words
+ [#15671] CB integration: Source code documentation for CKunenaUserprofile class variables
+ [#15671] CB integration: Added lib/kunena.communitybuilder.php for CB compability
# [#15671] CB integration: Workaround for Community Builder: don't redefine $database
+ [#15671] CB integration: New class CKunenaCBProfile, use it for integration
+ [#15671] CB integration: Added callback for profile integration changes, code cleanup

30-March-2009 Matias
# [#15638] Latest member profile link causes fatal error
! Regression: fixed user count and latest user from Forum Stats (1.0.8 behaviour)
! Create new profile with default values if profile does not exist

30-March-2009 fxstein
+ [#15724] Add bbcode and smilie support to forum headers in default_ex
# [#15139] Fixed broken IP address lookup link

29-March-2009 Matias
# [#15677] Fix UI issues: Showcat does not validate
# [#15677] Fix UI issues: Latestx shows "Show last visit" option for anonymous users
# [#15677] Fix UI issues: Latestx "x time ago" options are not fully ordered
# [#15677] Fix UI issues: Latestx, showcat do not validate for moderators
# [#15677] Fix UI issues: Message view does not mark new messages by green icon
# [#15677] Fix UI issues: Message view contains some code for Quick Reply even if it's disabled

28-March-2009 fxstein
# [#15702] Fix broken RSS feed on Joomla 1.0.x without SEF
+ [#15705] Short names for external module positions for Joomla 1.0.x
  kunena_profilebox -> kna_pbox, kunena_announcement -> kna_ancmt,
  kunena_msg_'n' -> kna_msg'n', kunena_bottom -> kna_btm

28-March-2009 Matias
# [#15638] Latest member profile link causes fatal error
! User profile detects now nonexistent users and user profiles
# [#15639] User list incomplete
! Only users who have Kunena user profile are listed
# [#15567] Implement working advanced search: add backwards compability for old templates

27-March-2009 Matias
# [#15677] Fix UI issues: IE7 bug having collapsed tabs
# [#15677] Fix UI issues: Big empty space on some templates
# [#15677] Fix UI issues: Spoiler icon has wrong URL if Joomla is not in document root
# [#15677] Fix UI issues: Open Kunena.com to a new window/tab (=external link)
# [#15677] Fix UI issues: Show announcements also in latestx

26-March-2009 Matias
# [#15567] Fix pagination in search
+ [#15671] Add API for changing user settings in CB (part 1)
! Renamed fbUserprofile to CKunenaUserprofile. It can be found from lib/kunena.user.class.php
# [#15667] Missing argument 3 for mb_convert_encoding
# [#15154] Auto linked email addresses contain two slashes in front of the address

24-March-2009 fxstein
# [#15625] Prepare for 1.0.9 build
# [#15624] Fix language file re-declaration

22-March-2009 Matias
# [#15565] Board Categories showed only public categories
# [#15566] Fireboard 1.0.1 didn't contain directory com_kunena/uploaded
+ [#15567] Implement working advanced search

22-February-2009 Matias
# [#15157] Empty messages in Joomla 1.0 part 2
# [#15170] Add backward compability to FB 1.0.5: Same meaning for 0 in latestcategory

21-February-2009 Matias
# [#15157] Empty messages in Joomla 1.0
# [#15162] Two same smileys in a row do not work
# [#15163] Fetch Kunena template from Joomla theme fails with warnings

20-Februray-2009 fxstein
# [#15151] Ensure fbConfig is array during legacy config file write - Thx JoniJnm!

20-February-2009 Matias
# [#15148] Post emails for moderators: name missing
# [#15150] Don't send email to banned users

19-Februray-2009 fxstein
# Incorrect permissions handling fixed

19-Februray-2009 Matias
# Search: Fixed SEF/no SEF issues. Pagination and links should now work for all
# Pathway: comma (,) was missing between usernames if there were no guests
# Thread View: Minor fix in pagination

18-Februray-2009 Matias
# Broke PHP4 compability, added emulation for htmlspecialchars_decode()
# Pathway: removed multiple instances of one user, use alphabetical order

Kunena 1.0.8

17-Februray-2009 fxstein
# Missing category check added to default_ex Recent Discussions tab
  Backend Show Category setting in Recent Posts can now limit the categories displayed
+ Added category id to display in backend forum administration
# Integration dependent myprofile page fixes
- Remove broken "Close all tags" when writing the message in all places
+ Installer upgrade added for recent posts categories setting
# minor naming change in 1.0.8 upgrade procedure

17-Februray-2009 Matias
# Strip extra slashes in preview
# Regression: Quick Reply Cancel button does not work
# Backend: you removing user's avatar dind't work
# My Profile: Wrong click here text after uploading avatar

16-Februray-2009 fxstein
# Fix the fix - url tags now have http added only when needed but then for sure
# jquery Cookie error: Prevent JomSocial from loading their jquery library

16-February-2009 Matias
# Fix broken link in "Mark all forums read"
# Regression: Moderator tools in showcat didn't work
# Changed all "Hacking attempt!" messages to be less radical and not to die().
# Regression: Unsticky and Unlock didn't work
^ Changed behavior of Mark all forums read, Mark forum read - no more extra screen
^ Changed behavior of Subscribe, Favorite, Sticky, Lock - no more extra screen
# Fixed broken layout in FireFox 2

15-February-2009 Matias
^ Change time formating in announcements
# Regression: Removed &#32 ; from report emails
# Fixed report URLs
# Regression: Typos in template exists checks
# Regression: Missing css class in default template
# Removed extra slashes in headerdesc, moved it to the right place in showcat
^ Tweaks in css, fix dark themes
# Missing define for _POST_NO_FAVORITED_TOPIC in kunena.english.php
# Show user only once in pathway
# Fix broken search pagination
# Fix Search contents

15-Februray-2009 fxstein
# Proper favicon in menu for Joomla 1.0.x
+ Add missing user sync language string to language file
- load and remove sample data images removed as functionality has been depriciated
# Regression: Proper Joomla 1.5 vs 1.0 detection during install
^ Backend Kunena Information updated
^ Initial base for new 3rd party profile integration framework
- Removal of legacy CB integration for profile fields. New functionality
  through plugin for all 3rd party profile providers
# Missing http:// on url codes for url that do not start with www

14-Februray-2009 fxstein
# Added missing SEF call to mark all forums read button
+ Replace com_fireboard with com_kunena in all messages and signatures
^ Preview button label now with capital 'P'
# Incorrect button css classes on write message screen fixed
^ Extra spacing for text buttons to conform with Joomla button style
# Regression: Disabled the submit button because of incorrect type - fixed
# Regression: Moderator tools got disabled during relocation - fixed
+ Missing search icon in default_ex userlist added
+ Missing css styling added to forum header description
^ Forumjump: put Go button to the right side of the drop down category list

14-February-2009 Matias
# Try 2: Use default_ex template if current template is missing
^ Changed Quick Reply icon.
^ Use the same style in all buttons. CSS simplifications, fixes

13-Februray-2009 fxstein
# Minor bug fix in automatic upgrade that re-ran 1.0.6 portion unneccesarily

13-February-2009 Matias
# Regression in r381: New pathway was slightly broken, also some css was missing
# Fixed sender in all emails. It's now "BOARD_TITLE Forum"

12-Februray-2009 fxstein
^ TOOLBAR_simpleBoard renamed to CKunenaToolbar
- Legacy FB sample data code removed
+ New Kunena sample data added for new installs

12-February-2009 Riba
^ Pathway: Removed hardcoded styling
^ Pathway: Edited html output and CSS styles for easier customization
# Pathway: Removed comma separator after last user

12-February-2009 Noel Hunter
# Changes to icons in default_ex for transparency, visibility

12-February-2009 Matias
^ Improved pagination in latestx
^ Improved pagination in showcat
^ Improved pagination in view
+ Added pathway to the bottom of the showcat page
+ Added pathway to the bottom of the view page
^ Improved looks of the showcat page
^ Improved looks of the listcat page
^ Improved looks of the view page
# Missing addslashes for signature in admin.kunena.php
# Regression in r362: Broke UTF-8 letters in many places
^ Moved Thread specific moderator tools from message contents to action list

11-February-2009 fxstein
# fixed and rename various Joomla module positions for Kunena:
  kunena_profilebox, kunena_announcement, kunena_bottom
  in addtion to the previously changed kunena_msg_1 ... n
+ Increase php timepout and memory setting once Kunena install starts
# updated database error handling for upgrade base class
# minor language file cleanup, removed none ascii characters
+ additional language strings for initial board setup on fresh install

11-February-2009 Matias
# default: No menu entry pointed to Categories if default Kunena page wasn't Categories
# Huge amount of missing slashes added and extra slashes removed from templates
# Fixed broken timed redirects

10-February-2009 fxstein
# Incorrect error message on version table creation for new installs

10-February-2009 Matias
# Regression in r338: Broke My Profile
# Regression in r338: Broke FB Profile
# Regression in r246: Broke Quick Reply for UTF-8 strings
^ Show Topic in Quick Reply
# Do not add smiley if it is attached to a letter, for example TV:s, TV:seen

9-February-2009 fxstein
# Broken RSS feed in Joomla 1.0.x fixed
^ FBTools Changed to CKunenaTools
# Updated README
# Regression: Accidentially modified MyPMSTools::getProfileLink parameters

9-February-2009 Noel Hunter
# Significant leading and trailing spaces in language file replaced with
  &#32; to avoid inadvertant omission in translation

9-February-2009 severdia
# English: Spelling & grammar corrected
# README.txt: Spelling & grammar corrected

9-February-2009 Matias
^ Changed email notification sent to subscribed users
^ Changed email notification sent to moderators
^ Changed email when someone reports message to moderators
# Topic was slightly broken in default_ex (moved, unregistered)
^ Shadow message (MOVED) will now have moderator as its author
# Regression: moving messages in viewcat didn't work for admins
# New user gets PHP warning
# No ordering for child boards

8-February-2009 fxstein
+ Community Builder 1.2 basic integration
+ Make images clickable and enable lightbox/slimbox if present in template
^ changed $obj_KUNENA_search to $KunenaSearch to match new naming convention
^ clickable images and lightboxes only on non nested images; images within URL
  codes link to the URL specified
^ fb_1 module position renamed to kunena_profilebox to match new module position naming
# Avoid forum crash when JomSocial is selected in config but not installed on system

8-February-2009 Matias
# Image and file attachments should now work in Windows too
# Fix error when deleting message(s) with missing attachment files
# Fix error when deleting message(s) written by anonymous user
# Regression: fixed an old bbCode bug again..
# Fixed error in search when there are no categories

7-February-2009 Matias
# Moderators can now move messages outside their own area (no more Hacking Attempt!)
# Remove users name and email address from every message in the view (Quick Reply)
# Fix "Post a new message" form when email is mandatory
# Allow messages to be sent even if user has no email address
# Require email address setting wasn't enforced when you posted a message

6-February-2009 fxstein
+ additional jomSocial CSS integration for better looking PM windows
^ $fbversion is now $KunenaDbVersion
+ additional db check in class.kunena.php
+ basic version info on credits page
+ enhanced version info including php and mysql on debug screen
# added default values for various user fields in backend save function
# fix broken viewtypes during upgrade and reset to flat
# modified logic to detect Kunena user profiles to avoid forum crash in rare cases
# remove avatar update from backend save to avoid user profile corruption
^ Search class renamed to CKunenaSearch
- Removed depriciated threaded view option from forum tools menu

6-February-2009 Matias
# Use meaningful page titles, add missing page titles
^ Small fixes to CSS
# Regression, done this again: Removed all short tags: < ?=

5-February-2009 Matias
# Try 2: Work around IE bug which prevented jump to last message
# Removed odd number that was sometimes showing up
^ Added Kunena Copyright to all php files

4-February-2009 Noel Hunter
^ Changes to colors in kunena.forum.css to prevent inheritance of colors
  from joomla templates making text unreadable
^ Changes to kunena.forum.css to expand whos-online in pathway for
  longer lists, reduce line height, additional color fixes
^ Remove centering from code tags in parser, to fix ie bug

4-February-2009 fxstein
^ font size regression fix: reply counts in default_ex back to x-large
^ New ad module position logic. Much Simplified with support for n module positions: kunena_msg_1
  through kunena_msg_n. n being the number of posts per page.

4-February-2009 Matias
+ First version of CKunenaUser(s) class
# Backend, User Profile: include path fixed
^ Backend, User Profile: Removed bbcode, it didn't work
^ Removed flat/threaded setting, it wasn't used
# Backend, Ranks: fixed bug when you had no ranks
# You may now have more than one announcement moderator
# Removed all short tags: < ?=
# Fixed My Profile / Forum Settings / Look and Layout

3-February-2009 fxstein
# Reverse sort bug fix. Newest messages first now work in threads.
# Minor regression and syntax fixes
# Correct last message link when reverse order is selected by the user

2-February-2009 Noel Hunter
^ Change all references from forum.css to kunena.forum.css
+ If kunena.forum.css is present in the current Joomla template css directory,
  load it instead of Kunena template's kunena.forum.css
^ Change font sizes in kunena.forum.css for default_ex from px to relative sizes (small, medium, etc)
^ Change names in for forum tools in kunena.forum.css from fireboard to kunena, add z-index:2 to menu
^ Fix css typos for forum tools menu, add z-index
- Removed unused group styles from kunena.forum.css, and associated images files from default_ex images

2-February-2009 Matias
^ Move forced width from message text to [code] tag
^ Remove confusing link from avatar upload
^ default_ex: Update latestx redirect to use CKunenaLink class

2-February-2009 fxstein
^ Removed addition left over HTML tags and text for prior threaded view support in profile
# htmlspecialchars_decode on 301 redirects to remove &amps from getting into the browser URL
^ fb_Config class changed to CKunenaConfig, boj_Config class changed to CKunenaConfigBase
+ new CKunenaConfig class functionality to support user specific settings
^ kunena_authetication changed to CKunenaAuthentication

1-February-2009 Noel Hunter
^ Use default_ex if current template is missing
+ Add title tags to reply and other buttons in "default" template
^ Work around ie bug which prevented jump to last message

1-February-2009 Matias
# xhtml fixes
# My Messages will redirect to Last Messages if user has logged out
# Regression: Fix broken icon in Joomla Backend

31-January-2009 fxstein
^ default_ex jscript and image cleanup

31-January-2009 Matias
# Additional BBCode fixes

30-January-2009 fxstein
# Additional jQuery fixes
- Removed outdated jquery.chili 1.9 libraries (different file structure)
+ Added new jquery.chili 2.2 libraries
^ Moved jquery.chili jscripts to load at the bottom of the page for faster pageloads
+ add jomSocial css in header when integration is on to enable floating PM window

30-January-2009 Matias
# Regression: favorite star didn't usually show up
+ default_ex: Added grey favorite icon for other peoples favorite threads

29-January-2009 fxstein
# Fixed incorrect MyProfile link logic with various integration options
- Removed unsusable threaded view option

29-January-2009 Matias
# Regression: Backend won't be translated

28-January-2009 fxstein
# Fixed broken display with wide code
# Fixed jQuery conflicts caused by $() usage
+ PHP and MYSQL version checks during install

28-January-2009 Matias
# Replace all occurences of jos_fb_ with #__fb_
# Don't allow anonymous users to subscribe/favorite
# Do not send email on new post if the category is moderated
# Fix broken tables fb_favorites and fb_subscriptions
# Regression from Kunena 1.0.7b: avatar upload page internal error
# Avatar upload was broken if you didn't use profile integration
# default_ex: My Profile internal link was wrong

27-January-2009 fxstein
# BBCode fix for legacy [code:1] support

Kunena 1.0.7 beta

26-January-2009 fxstein
+ JomSocial userlist integration for Kunena userlist link in front stats
- Remove old unused legacy code
^ Fixed broken PDF display
^ Corrected upgrade logic order

26-January-2009 Matias
# default_ex: Link to first unread message was sometimes broken
^ view: Message is marked new only if thread hasn't been read
+ kunena.credits.php: Added myself
# Stats should work again (typos fixed)
* My Profile: My Avatar didn't have security check for anonymous users

25-January-2009 fxstein
+ Basic JomSocial Integration
^ updated jquery to latest 1.3.1 minimized
^ fb_link class changes to CKunenaLinks
# Minor typo in include paths fixed
^ kunena.credits.php: Updated credits page
^ Various links updated
+ Kunena logos added to default and default_ex tamplates
# smile.class.php: parser references fixed

25-January-2009 Matias
# Stats: Visible even if they were disabled
# Stats: Wrong count in topics and messages
# Stats: Today/yesterday stats didn't include messages between 23:59
  and 00:01.
^ Stats: Optimized SQL queries for speed and saved 11-20 queries
! DATABASE UPDATED: new keys added to fb_messages and fb_users
# Emoticons: Broken "more emoticons" pop up in IE7.
# Forum Tools: Fixed CSS rules in default_ex
^ Anonymous user cannot be admin, saves many SQL queries
# Removing moved thread (or written by anonymous user) didn't
  work in showcat
+ view: Make new messages visible (green topic icon).
+ default_ex: Show number of new messages (just like in category view).
+ default_ex: Jump to first new message by clicking new message indicator.
! Current behaviour is "first message after logout or mark all forums read".
^ showcat, latestx: Use faster query to find all messages in a thread.
# Message posted notification page redirects after you click a link

24-January-2009 Matias
# Fixed over 100 xhtml bugs
^ No default size for [img]
^ Category parent list: jump to Board Categories with "Go" button
^ Forum stats show users in alphabetical order

01-January-2009 fxstein
+ Initial fork from FireBoard 1.0.5RC3

