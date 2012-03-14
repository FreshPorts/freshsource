<?php if ( !defined( "_COMMON_PHP" ) ) return; ?>
<?php
	#
	# $Id: header.php,v 1.8 2012-03-14 18:51:18 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start($FreshPortsSlogan,
					"$FreshPortsName - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug = 0;

?>
<div class="PhorumForumTitle"><strong><?php echo $ForumName; ?></strong></div>
<br />
<?php
#
# custom_BannerForum is invoked by read.php, post.php and list.php.
# It is a place holder for any customizations by the local phorum
#

function custom_BannerForum($ForumName, $article_id) {
	return '';
}

# init syslog()
openlog('FreshSource', LOG_PID, LOG_SYSLOG);

?>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD valign="top">
