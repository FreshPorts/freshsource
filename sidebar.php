<?
	# $Id: sidebar.php,v 1.2 2002-06-24 19:58:09 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_HTML_Start();

?>

<HEAD>
	<TITLE><? echo $FreshPortsTitle; ?></TITLE>

	<STYLE TYPE="text/css">
	BODY, TD, TR, P, UL, OL, LI, INPUT, SELECT, DL, DD, DT, FONT
	{
		font-family: Helvetica, Verdana, Arial, Clean, sans-serif;
		font-size: 12px;
	}
	ul { padding-left: 20px;}
	</STYLE>

	<META HTTP-EQUIV="Refresh" CONTENT="1200; URL=http://st.FreshPorts.org/sidebar.php">
	<META http-equiv="Pragma"              content="no-cache">
	<META NAME="MSSmartTagsPreventParsing" content="TRUE">

</HEAD>

<?
	freshports_body();
	$ServerName = str_replace("freshports", "FreshPorts", $_SERVER["SERVER_NAME"]);
?>

	<CENTER>
	<A HREF="http://<? echo $ServerName; ?>/" TARGET="_content"><IMG SRC="/images/freshsource-mini.gif" ALT="<? echo $FreshPortsName . ' - ' . $FreshPortsSlogan; ?>" WIDTH="128" HEIGHT="28" BORDER="0"></A>

	<BR>

	<SMALL>
	<script LANGUAGE="JavaScript">
		var d = new Date();  // today's date and time.
	    document.write(d.toLocaleString());
	</script>
	</SMALL>
	</CENTER>

<?

	$sql = "select id, message_id, message_date, message_subject, date_added,
	               to_char(commit_date - SystemTimeAdjust(), 'DD Mon YYYY')           AS commit_date,
    	           to_char(commit_date - SystemTimeAdjust(), 'HH24:MI')               AS commit_time,
	               committer, description, system_id, encoding_losses
	          from commit_log order by commit_log.commit_date desc";

	$sql .= " limit 40";

	if ($Debug) {
		echo $sql;
		}

	$result = pg_exec ($db, $sql);
	if (!$result) {
		echo $sql . 'error = ' . pg_errormessage();
		exit;
	}
?>

	<UL>
<?
	$numrows = pg_numrows($result);
	for ($i = 0; $i < $numrows; $i++) {
		$myrow = pg_fetch_array ($result, $i);
		echo '	<LI><SMALL><A HREF="http://' . $ServerName . '/commit.php?message_id=' . $myrow["message_id"] . '" TARGET="_content">';
		echo htmlspecialchars(str_replace("cvs commit: ", "", $myrow["message_subject"])) . '</A>';
		echo '</SMALL></LI>';
		echo "\n";
	}
?>
	</UL>

<P ALIGN="right">
<? echo freshports_copyright(); ?>
</P>

</body>
</html>
