<?php
	#
	# $Id: news.php,v 1.3 2003-04-14 01:04:22 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");

	$Debug=0;

	$ServerName = str_replace("freshsource", "FreshSource", $_SERVER["SERVER_NAME"]);

	$MyMaxArticles = 10;

	if (!$MaxArticles || $MaxArticles < 1 || $MaxArticles > $MyMaxArticles) {
		$MaxArticles = $MyMaxArticles;
	}

	if ($MaxArticles == $MyMaxArticles) {
		$OutputFromCach = 1;
	} else {
		$OutputFromCach = 0;
	}

	# From https://www.carronmedia.com/create-an-rss-feed-with-php/
	header("Content-Type: application/rss+xml; charset=UTF-8");
	$HTML .= '<rss version="0.91">' . "\n";

	$HTML .= "\n";

	$HTML .= '<channel>' . "\n";
	$HTML .= '  <title>' . $FreshPortsName . ' - ' . $FreshPortsSlogan . '</title>' . "\n";
	$HTML .= '  <link>http://' . $ServerName . '/</link>' . "\n";
	$HTML .= '  <description>The easiest place to find source</description>' . "\n";
	$HTML .= '  <language>en-us</language>' . "\n";

	$sql = "select id, message_id, message_date, message_subject, date_added,
	               to_char(commit_date - SystemTimeAdjust(), 'DD Mon YYYY')           AS commit_date,
    	           to_char(commit_date - SystemTimeAdjust(), 'HH24:MI')               AS commit_time,
	               committer, description, system_id, encoding_losses
	          from commit_log order by commit_log.commit_date desc";


	$sql .= " limit 20";

	if ($Debug) {
		echo $sql;
		}

	$result = pg_exec ($db, $sql);
	if (!$result) {
		echo $sql . 'error = ' . pg_errormessage();
		exit;
	}

	$numrows = pg_num_rows($result);
	for ($i = 0; $i < $numrows; $i++) {
		$myrow = pg_fetch_array ($result, $i);
		$HTML .= '  <item>' . "\n";
		$HTML .= '    <title>' . $myrow["message_subject"] . '</title>' . "\n";
		$HTML .= '    <link>http://' . $ServerName . '/commit.php?message_id=' . $myrow["message_id"] . '</link>' . "\n";
		$HTML .= '    <description>' . htmlspecialchars(trim($myrow["description"])) . '</description>' . "\n";
		$HTML .= '  </item>' . "\n";
	}

	$HTML .= '</channel>' . "\n";
	$HTML .= '</rss>' . "\n";
   
	echo '<?xml version="1.0"?>', "\n";
	echo $HTML;
