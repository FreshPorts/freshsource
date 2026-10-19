<?php
	# $Id: legal.php,v 1.2 2004-01-01 14:02:41 dan Exp $
	#
	# Copyright (c) 1998-2026 Dan Langille

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");
	require_once('/usr/local/www/freshports/include/freshports.php');

	freshports_Start("Legal Notice",
					"freshports - new ports, applications",
					"FreeBSD, index, applications, ports");

?>

<TABLE WIDTH="<?php echo $TableWidth ?>" BORDER="0" ALIGN="center">
<TR><TD VALIGN="top">
<TABLE WIDTH="100%" BORDER="0" CELLSPACING="0" CELLPADDING="5">
  <TR>
    <TD bgcolor="#AD0040" height="32"><FONT COLOR="#FFFFFF" SIZE="+1">LEGAL NOTICE</FONT></TD>
  </TR>
  <TR><TD>This page contains our obligatory legal notice.  I really don't like having to say
          these things, but given the nature of some people, I must.  For the rest of you,
          if you respect my work and my right to it, you'll have no problem.  Thanks.
  </TD></TR>
  <TR><TD height="20"></TD></TR>
  <TR>
    <TD BGCOLOR="#AD0040" height="32"><FONT COLOR="#FFFFFF" SIZE="+1">COPYRIGHT</FONT></TD>
  </TR>
  <TR><TD>
  <p>Copyright <?php echo COPYRIGHTYEARS; ?> Dan Langille.
  All rights reserved.&nbsp; Copyright in
  this document is owned by Dan Langille. &nbsp; Any person is hereby authorized to
  view, copy, print, and distribute this document subject to the following conditions: <ol>
    <li>The document may be used for informational purposes only.</li>
    <li>The document may only be used for non-commercial purposes.</li>
    <li>Any copy of this document or portion thereof must include this copyright notice.</li>
  </ol>
  <p>Note that any product, process or technology described in the document may be the
  subject of other Intellectual Property rights reserved by Dan Langille and are not
  licensed hereunder.</p>
  </TD></TR>
  <TR><TD height="20"></TD></TR>
  <TR>
    <TD BGCOLOR="#AD0040" height="32"><FONT COLOR="#FFFFFF" SIZE="+1">CONTENT AND LIABILITY DISCLAIMER</FONT></TD>     
  </TR>
  <TR><TD>
  <p>Dan Langille shall not be responsible for any errors or omissions contained at
  this Web Site, and reserves the right to make changes without notice.&nbsp; Accordingly,
  all Dan Langille and third party information is provided &quot;AS IS&quot;. </p>
  <p>Dan Langille DISCLAIMS ALL WARRANTIES WITH REGARD TO THE INFORMATION (INCLUDING
  ANY SOFTWARE) PROVIDED, INCLUDING THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
  FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.&nbsp; Some jurisdictions do not allow the
  exclusion of implied warranties, so the above exclusion may not apply to you.</p>
  <p>In no event shall Dan Langille be liable for any damages whatsoever, and in
  particular Dan Langille shall not be liable for special, indirect , consequential,
  or incidental damages, or damages for lost profits, loss of revenue, or loss of use,
  arising out of or related to any Dan Langille Web Site or the information
  contained in it, whether such damages arise in contract, negligence, tort, under statute,
  in equity, at law or otherwise. </p>
  </TD></TR>
  <TR><TD height="20"></TD></TR>
  <TR>
    <TD BGCOLOR="#AD0040" height="32"><FONT COLOR="#FFFFFF" SIZE="+1">FEEDBACK INFORMATION</FONT></TD>
  </TR>
  <TR><TD>
  <p>Any information provided to Dan Langille in connection with any Dan Langille
  Web Site shall be provided by the submitted and received by Dan Langille
  on a non-confidential basis. Dan Langille shall be free to use such information on
  an unrestricted basis. </p>
  </TD></TR>
  <TR><TD height="20"></TD></TR>
  <TR>
    <TD BGCOLOR="#AD0040" height="32"><FONT COLOR="#FFFFFF" SIZE="+1">TRADEMARKS</FONT></TD>
  </TR>
  <TR><TD>
  <p>All Dan Langille's product names are trademarks or registered trademarks of Dan Langille.&nbsp; Other brand and product names are trademarks or registered
  trademarks of their respective holders. </p>
  </TD></TR>
</TABLE>
</TD>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/side-bars.php") ?>
 </TD>
</TR>
</TABLE>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/footer.php") ?>
</body>
</html>
