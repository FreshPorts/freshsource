<?php if ( !defined( "_COMMON_PHP" ) ) return; ?>
<?php
	#
	# $Id: header.php,v 1.6 2012-03-14 18:23:25 dan Exp $
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
<html>
<head>
<meta name="PhorumVersion" content="<?php echo $phorumver; ?>" />
<meta name="PhorumDB" content="<?php echo $DB->type; ?>" />
<meta name="PHPVersion" content="<?php echo phpversion(); ?>" />
<title>phorum<?php if(isset($ForumName)) echo " - $ForumName"; ?><?php if(isset($title)) echo $title; ?> - FreshSource!</title>
<link rel="STYLESHEET" type="text/css" href="<?php echo phorum_get_file_name("css"); ?>" />
</head>
<body bgcolor="<?php echo (empty($ForumBodyColor)) ? $default_body_color : $ForumBodyColor; ?>" link="<?php echo (empty($ForumBodyLinkColor)) ? $default_body_link_color : $ForumBodyLinkColor; ?>" alink="<?php echo (empty($ForumBodyALinkColor)) ? $default_body_alink_color : $ForumBodyALinkColor; ?>" vlink="<?php echo (empty($ForumBodyVLinkColor)) ? $default_body_vlink_color : $ForumBodyVLinkColor; ?>">
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
<TR><TD align="top">
