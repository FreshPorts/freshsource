<?
	# $Id: authors.php,v 1.1 2002-06-24 19:14:04 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start("The Authors",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

?>
<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<tr><td valign="top">
<table width="100%" border="0">
  <tr>
	<? freshports_PageBannerText("About the authors"); ?>
  </tr>
<TR><TD>
<p>Dan Langille thought up the idea, found the data sources, bugged people to 
write scripts, and did the html and database work. But he certainly didn't 
do it alone.</p>

<P>
Most of what you see here is based upon <A HREF="http://www.FreshPorts.org/">FreshPorts</A>.
FreshSource is merely a generalized application of the specific technology used by FreshPorts.
</P>

</TD>
</TR>
</TABLE>
</td>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
    <? include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
 </td>
</tr>
</table>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
