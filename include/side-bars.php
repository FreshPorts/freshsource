<?php
	#
	# $Id: side-bars.php,v 1.17 2012-03-14 18:22:20 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	$ColumnWidth = 155;

	require_once($_SERVER['DOCUMENT_ROOT'] . "/include/burstmedia.php");

	$Debug=0;
?>

<TABLE WIDTH="<? echo $ColumnWidth ?>" BORDER="1" CELLSPACING="0" CELLPADDING="5">
	<TR>
		<TD BGCOLOR="#AD0040" height="30"><FONT COLOR="#FFFFFF"><BIG><B>Login</B></BIG></FONT></TD>
	</TR>

	<TR>
	<TD>

<?php
	switch (basename($_SERVER["PHP_SELF"])) {
//		case "watch.php":
//		case "watch-categories.php":
//		case "customize.php":
//		case "port-watch.php":
//			$OriginLocal = '/';
//			break;

		default:
			$OriginLocal = rawurlencode($HTTP_SERVER_VARS["REQUEST_URI"]);
			break;
	}

	if (IsSet($_COOKIE["visitor"])) $visitor = $_COOKIE["visitor"];
	if ($Debug) echo "OriginLocal = '$OriginLocal'<BR>\n";
	if ($visitor) {
		echo '<FONT SIZE="-1">Logged in as ', $UserName, "</FONT><BR>";

		if ($EmailBounceCount > 0) {
			echo '<IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><BR>';
			echo '<FONT SIZE="-1">your email is <A HREF="bouncing.php?origin=' . $OriginLocal. '">bouncing</A></FONT><BR>';
			echo '<IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><BR>';
		}
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/customize.php',        "?origin=$OriginLocal", "Customize"              ) . '</FONT><BR>';


		# for a logout, where we go depends on where we are now
		#
		switch ($_SERVER["PHP_SELF"]) {
			case "customize.php":
			case "watch-categories.php":
			case "watch.php":
			$args = "?origin=$OriginLocal";
			break;

			default:
				$args = '';
		}
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/logout.php',           $args,                  "Logout"                 ) . '</FONT><BR>';
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/watch.php',            '',                     "your watched sources"   ) . '</FONT><BR>';
	} else {
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/login.php',            "?origin=$OriginLocal", "User Login"             ) . '</FONT><BR>';
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/new-user.php',         "?origin=$OriginLocal", "Create account"         ) . '</FONT><BR>';
	}
?>

	</TD>
	</TR>
</TABLE>

<P>

<TABLE WIDTH="<? echo $ColumnWidth ?>" BORDER="1" CELLSPACING="0" CELLPADDING="5">
	<TR>
		<TD BGCOLOR="#AD0040" height="30"><FONT COLOR="#FFFFFF"><BIG><B>This site</B></BIG></FONT></TD>
	</TR>
	<TR>
	<TD VALIGN="top">
<?
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/about.php",            "What is FreshSource?") . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/phorum/",              "Forums")               . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/authors.php",          "About the Authors")    . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/privacy.php",          "Privacy")              . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/commits.php",			"Commits")				. '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/repo.php",				"Repository browse")	. '</FONT><BR>';
?>
	</TD>
	</TR>
</TABLE>


<?php
	GLOBAL $BannerAd;
	GLOBAL $ShowAds;

	if ($ShowAds) {

		BurstMediaCode();
		echo '<TABLE BORDER="0" CELLPADDING="5">
			  <TR><TD ALIGN="center">
			 ';

		BurstSkyscraperAd();
		echo '</TD></TR>
			  </TABLE>
			 ';
	}

?>


<BR>


<CENTER>
<A NAME="button_image"></A><A HREF="javascript:addNetscapePanel();"><IMG SRC="/images/sidebar-add-button.gif" BORDER="0" HEIGHT="45" WIDTH="100" ALT="Add tab to Netscape 6"></A>
</CENTER>

