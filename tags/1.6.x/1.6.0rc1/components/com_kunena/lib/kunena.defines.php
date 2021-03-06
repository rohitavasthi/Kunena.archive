<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
* @Copyright (C) 2010 Kunena All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS .'com_kunena'. DS . 'api.php');

// Default values
define('KUNENA_LANGUAGE_DEFAULT', 'english');
define('KUNENA_TEMPLATE_DEFAULT', 'default');

define('KUNENA_LANGUAGE', 'english');

// File system paths
define('KUNENA_COMPONENT_RELPATH', 'components' .DS. KUNENA_COMPONENT_NAME);

define('KUNENA_ROOT_PATH', JPATH_ROOT);
define('KUNENA_ROOT_PATH_ADMIN', JPATH_ADMINISTRATOR);

define('KUNENA_PATH', KUNENA_ROOT_PATH .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_LIB', KUNENA_PATH .DS. 'lib');
define('KUNENA_PATH_FUNCS', KUNENA_PATH .DS. 'funcs');
define('KUNENA_PATH_TEMPLATE', KUNENA_PATH .DS. 'template');
define('KUNENA_PATH_TEMPLATE_DEFAULT', KUNENA_PATH_TEMPLATE .DS. KUNENA_TEMPLATE_DEFAULT);

define('KUNENA_PATH_ADMIN', KUNENA_ROOT_PATH_ADMIN .DS. KUNENA_COMPONENT_RELPATH);
define('KUNENA_PATH_ADMIN_LIB', KUNENA_PATH_ADMIN .DS. 'lib');
define('KUNENA_PATH_ADMIN_LANGUAGE', KUNENA_PATH_ADMIN .DS. 'language');
define('KUNENA_PATH_ADMIN_INSTALL', KUNENA_PATH_ADMIN .DS. 'install');
define('KUNENA_PATH_ADMIN_IMAGES', KUNENA_PATH_ADMIN .DS. 'images');

// Kunena uploaded files directory
define('KUNENA_RELPATH_UPLOADED', 'media/kunena/attachments');
define('KUNENA_PATH_UPLOADED', KUNENA_ROOT_PATH .DS. KUNENA_RELPATH_UPLOADED);

// Kunena uploaded avatars directory
define('KUNENA_RELPATH_AVATAR_UPLOADED', '/media/kunena/avatars');
define('KUNENA_PATH_AVATAR_UPLOADED', KUNENA_ROOT_PATH . KUNENA_RELPATH_AVATAR_UPLOADED);

// Kunena legacy uploaded files directory
define('KUNENA_RELPATH_UPLOADED_LEGACY', '/images/fbfiles');
define('KUNENA_PATH_UPLOADED_LEGACY', KUNENA_ROOT_PATH . KUNENA_RELPATH_UPLOADED_LEGACY);

// The tunmbnail folder is relative to any image file folder
define('KUNENA_FOLDER_THUMBNAIL', 'thumb');

// Files
define('KUNENA_FILE_LANGUAGE_DEFAULT', KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . KUNENA_LANGUAGE_DEFAULT . '.php');
define('KUNENA_FILE_LANGUAGE', KUNENA_PATH_ADMIN_LANGUAGE .DS. 'kunena.' . KUNENA_LANGUAGE . '.php');
define('KUNENA_FILE_INSTALL', KUNENA_PATH_ADMIN .DS. 'kunena.xml');

// Version information

// This special check to detect svn based dev environments that are lacking the proper variables
if ('@kunenaversion@' == '@'.'kunenaversion'.'@') {
	$changelog = file_get_contents(KUNENA_PATH.DS.'CHANGELOG.php', NULL, NULL, 0, 1000);
	preg_match('|\$Id\: CHANGELOG.php (\d+) (\S+) (\S+) (\S+) \$|', $changelog, $svn);
	preg_match('|~~\s+Kunena\s(\d+\.\d+.\d+\S*)|', $changelog, $version);
}

// Version information
define ('KUNENA_VERSION', ('@kunenaversion@' == '@'.'kunenaversion'.'@') ? strtoupper($version[1].'-SVN') : strtoupper('@kunenaversion@'));
define ('KUNENA_VERSION_DATE', ('@kunenaversiondate@' == '@'.'kunenaversiondate'.'@') ? $svn[2] : '@kunenaversiondate@');
define ('KUNENA_VERSION_NAME', ('@kunenaversionname@' == '@'.'kunenaversionname'.'@') ? 'SVN Revision' : '@kunenaversionname@');
define ('KUNENA_VERSION_BUILD', ('@kunenaversionbuild@' == '@'.'kunenaversionbuild'.'@') ? $svn[1] : '@kunenaversionbuild@');

// Time related
define ('KUNENA_SECONDS_IN_HOUR', 3600);
define ('KUNENA_SECONDS_IN_YEAR', 31536000);

// Database defines
define ('KUNENA_DB_MISSING_COLUMN', 1054);
