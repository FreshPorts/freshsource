<?php
	#
	# $Id: side-bars.php,v 1.18 2012-03-14 18:25:17 dan Exp $
	#
	# Copyright (c) 1998-2026 Dan Langille
	#

	GLOBAL $User;

	$ColumnWidth = 155;

	$Debug=0;
?>

<TABLE WIDTH="<? echo $ColumnWidth ?>" BORDER="1" CELLSPACING="0" CELLPADDING="5">
	<TR>
		<TD BGCOLOR="#AD0040" height="30"><FONT COLOR="#FFFFFF"><BIG><B>Login</B></BIG></FONT></TD>
	</TR>

	<TR>
	<TD>

<?php
	if (IsSet($User) && !empty($User->id)) {
		echo '<FONT SIZE="-1">Logged in as ', htmlentities($User->name), "</FONT><BR>";

		if ($User->emailbouncecount > 0) {
			echo '<IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><BR>';
			echo '<FONT SIZE="-1">your email is <A HREF="bouncing.php">bouncing</A></FONT><BR>';
			echo '<IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><IMG SRC="/images/warning.gif"><BR>';
		}
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/customize.php',        '', "Your Account"           ) . '</FONT><BR>';


		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/logout.php',           '', "Logout"                 ) . '</FONT><BR>';
	} else {
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/login.php',            '', "User Login"             ) . '</FONT><BR>';
		echo '<FONT SIZE="-1">' . freshports_SideBarHTMLParm($_SERVER["PHP_SELF"], '/new-user.php',         '', "Create account"         ) . '</FONT><BR>';
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
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/about.php",    "What?",   "What is FreshSource?") . '</FONT>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/authors.php",  "About",   "About the Authors")    . '</FONT>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/privacy.php",  "Privacy", "Privacy")              . '</FONT>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/commits.php",  "Commits", "Commits")              . '</FONT>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/repo.php",     "Repo",    "Repository browse")    . '</FONT>';
?>
	</TD>
	</TR>
</TABLE>

<BR>
