<?
	# $Id: side-bars.php,v 1.1 2002-06-24 19:42:12 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	$ColumnWidth = 155;

	require_once($_SERVER['DOCUMENT_ROOT'] . "/include/burstmedia.php");

	BurstMediaCode();
	echo '<TABLE BORDER="0" CELLPADDING=5">
		  <TR><TD ALIGN="center">
		 ';

	BurstSkyscraperAd();
	echo '</TD></TR>
		  </TABLE>
		 ';

?>


<BR>

<TABLE WIDTH="<? echo $ColumnWidth ?>" BORDER="1" CELLSPACING="0" CELLPADDING="5">
	<TR>
		<TD BGCOLOR="#AD0040" height="30"><FONT COLOR="#FFFFFF"><BIG><B>This site</B></BIG></FONT></TD>
	</TR>
	<TR>
	<TD VALIGN="top">
<?
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/about.php",            "What is FreshSource?") . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/authors.php",          "About the Authors")   . '</FONT><BR>';
	echo '<FONT SIZE="-1">' . freshports_SideBarHTML($_SERVER["PHP_SELF"], "/privacy.php",          "Privacy")             . '</FONT><BR>';
?>
	</TD>
	</TR>
</TABLE>


<BR>

<SCRIPT TYPE="text/javascript">


   function addNetscapePanel() {
      if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function"))
      {
         window.sidebar.addPanel ("FreshPorts",
         "http://test.FreshSource.org/sidebar.php","");
      }
      else
      {
         var rv = window.confirm ("This page is enhanced for use with Netscape 6.  " + "Would you like to upgrade now?");
         if (rv)
            document.location.href = "http://home.netscape.com/download/index.html";
      }
   }
//-->

</SCRIPT>

<CENTER>
<A NAME="button_image"></A><A HREF="javascript:addNetscapePanel();"><IMG SRC="/images/sidebar-add-button.gif" BORDER="0" HEIGHT="45" WIDTH="100" ALT="Add tab to Netscape 6"></A>
</CENTER>

