<?php
	#
	# $Id: index.php,v 1.14 2003-04-23 22:16:17 dan Exp $
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


<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>

<H2>What is <BIG>FreshSource</BIG>?</H2>

<P>
The <BIG>FreshSource</BIG> project allows you to track source code changes.  Features of the project include:
</P>

<UL>
<LI>register your interest in portions of the tree (via a <B>watch list</B>)</LI>
<LI>optionally receive notification of the changes which occur to items on your watch list</LI>
</UL>

<H3>Example page</H3>

We know you want it.... Here's <A HREF="/commits.php">the working copy</A> of what the home page might look like.
That page is live and shows the most recent commits.  Compare that page with the 
<A HREF="http://www.FreshPorts.org/">FreshPorts</A> look.  You'll even see some overlap in the commit messages (i.e.
the port commits will appear on both pages).  Also note that both websites are using the same data from the same 
database.
</P>

<H3>All the news you want</H3>
<P>
We also have <A HREF="/news.php">an RSS news feed</A> and <A HREF="sidebar.php">a sidebar</A> (click on the "Add Tab to
Netscape 6" link
at the bottom of the right hand column of any page).
</P>

<H2>How does <BIG>FreshSource</BIG> work?</H2>

<P>
<BIG>FreshSource</BIG> parses the log messages that are generated as new code is checked into the
repository.  The origin of the log message is not critical to the function of <BIG>FreshSource</BIG> 
because all input is first translated to a system-independent XML document.  This allows FreshSource 
to work with any source code tree.
</P>

<P>
After the XML is processed by FreshSource, the information is stored in a database.  The database
is then used to produce webpages and watch list notifications.
</P>

<H2>What does <BIG>FreshSource</BIG> work with?</H2>

<P>
<BIG>FreshSource</BIG> has been designed to work with any source tree.  But we had to start somewhere.
So we chose an operating system which most of the developers used: <A HREF="http://www.FreeBSD.org/">FreeBSD</A>.
As the project progresses, other operating systems will be included.
</P>

<H2>How do I get my source tree involved?</H2>

<P>
We won't be ready to include other source trees until after we officially launch the project.
But when we are ready, all we'll need is your source tree log changes in XML format according to the
DTD (see below).  The DTD will be an evolving document.  It may need to change as new requirements
come to our attention.  It was designed with the 
<A HREF="http://docs.freebsd.org/mail/current/cvs-all.html">FreeBSD cvs-all mailing list</A> in mind 
but with future expansion a key consideration.
</P>

<OL>
<LI><A HREF="http://docs.freebsd.org/cgi/getmsg.cgi?fetch=29447+0+archive/2002/cvs-all/20020106.cvs-all">archived 

FreeBSD message</A></LI>
<LI><A HREF="/samples/raw.2002.01.01.08.46.16.27025.txt">message as received by FreshSource</A></LI>
<LI><A HREF="/samples/xml.2002.01.01.08.46.16.27025.txt">XML version of message</A></LI>
<LI><A HREF="/samples/xml.dtd.txt">XML Document Type Definition (DTD)</A></LI>
<LI><A HREF="/samples/process_cvs_mail.pl">perl script</A> to convert message to XML</LI>
</OL>

<P>
If you are eager to get your source tree included, the first step is to produce a script which will take 
convert your log messages to XML format.  The log messages also need to be sent out on a mailing list.  
</P>

<P>
<BIG>FreshSource</BIG> takes input from mail messages, uses a script to convert them to XML,
and then processes the XML.  But it will be some time before we are ready to accept other source trees.
</P>

<H2>How can I help?</H2>

<P>
All of the technology involved in the <BIG>FreshSource</BIG> has come from
<A HREF="http://www.FreshPorts.org/"><BIG>FreshPorts</BIG></A>.  You can help
by joining the <BIG>FreshPorts</BIG> <A HREF="http://develop.FreshPorts.org">development team</A>.
We have many small stand-alone projects which vary in size from a few hours to a few weeks.  
We need help in the following areas:
</P>

<UL>
<LI>graphics designers - we need logos, banners, buttons, and general website design</LI>
<LI>shell scripts</LI>
<LI>perl scripts</LI>
<LI>PHP coding (getting information from the database onto the webpages)</LI>
<LI>HTML coding (we need webpages designed and set up)</LI>
<LI>Someone who can type (we need items written such as web page content, announcements, etc; we know 
what needs to go into these things, we just need someone to write them)</LI>
</UL>

<H2>Ideas for future work</H2>
<UL>
<LI>watch lists (same as what FreshPorts does only bigger)</LI>
<LI>filtering (not interested in an area of the tree?  filter it out)</LI>
<LI>add links to cvsweb and include commit history</LI>
<LI>searching (all commits done by jmallett)</LI>
<LI>commits to specific branches</LI>
</UL>

<H2>Who provides hosting services for <BIG>FreshSource</BIG>?</H2>

<A HREF="http://www.bchosting.com/">BChosting</A> provides the box on which <BIG>FreshSource</BIG> and 
<A HREF="http://www.FreshPorts.org/"><BIG>FreshPorts</BIG></A> runs</A>.

</TD>


  <TD VALIGN="top" WIDTH="*" ALIGN="center">
   <? include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
 </TD>

</TR>
</TABLE>

<BR>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<? include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</BODY>
</HTML>