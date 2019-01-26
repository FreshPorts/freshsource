<?php
	# $Id: about.php,v 1.1 2002-06-24 19:14:04 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start("About this site",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

?>
<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><td VALIGN=TOP>
<TABLE>

<TR>
	<?php
	freshports_PageBannerText("OK, whose bright idea was this?");
	?>
</TR>

<TR><TD>
<P>This site was created by Dan Langille.  His other web feats include 
<A HREF="http://www.freebsddiary.org/">The FreeBSD Diary</A>, <a 
href="http://www.racingsystem.com">The Racing System</A>, <A HREF="http://www.FreshPorts.org/">FreshPorts</A>, and an ability
to avoid reading the inane comments on <A HREF="http://slashdot.org">slashdot</A>.
But Dan didn't create the site all by himself.  Have a look at <A HREF="authors.php">
About the Authors</A> for details of who else helped.</P>
</TD></TR>

</TABLE>
</TD>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
    <?php
       $ShowPoweredBy = 1;
       include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php");
    ?>
 </TD>
</TABLE>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</BODY>
</HTML>
