<?
	# $Id: repo.php,v 1.5 2002-07-26 13:57:01 dan Exp $
	#
	# Copyright (c) 1998-2002 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start($FreshPortsSlogan,
					"$FreshSourceName - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug=0;

#phpinfo();

	$parent_id = $_GET["parent_id"];

?>

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">

<?php


	$numrows = $MaxNumberOfPorts;
	$database=$db;
	if ($database) {
#
# we limit the select to recent things by using a date
# otherwise, it joins the whole table and that takes quite a while
#
#$numrows=400;

		$sql = "SELECT id, name, parent_id, directory_file_flag, status
				FROM element";

		if ($parent_id) {
			$sql .= " WHERE parent_id = $parent_id";
		} else {
			$sql .= " WHERE parent_id is NULL";
		}
		$sql .= " ORDER BY directory_file_flag, name";

		if ($Debug) echo "\n<pre>sql=$sql</pre>\n";

		$result = pg_exec($database, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
#			echo $numrows . " rows to fetch\n";
			if ($numrows) { 

				$i=0;
				$GlobalHideLastChange = "N";
				for ($i = 0; $i < $numrows; $i++) {
					$myrow = pg_fetch_array ($result, $i);
					$rows[$i] = $myrow;
				}

				$NumRows = $numrows;
				$LastDate = '';
				if ($NumRows > 1) {
					$LastChangeLogID = $rows[$i]["change_log_id"];
					$LastChangeLogID = -1;
				}

?>

<TR><TD VALIGN="top" WIDTH="100%">
<TABLE WIDTH="100%" border="1" CELLSPACING="0" CELLPADDING="8">
<TR>
		<? freshports_PageBannerText("$MaxNumberOfPorts most recent commits", 3); ?>
        <? //echo ($StartAt + 1) . " - " . ($StartAt + $MaxNumberOfPorts) ?>
</TR>
<TR><TD>
<P>
Welcome to FreshSource, where you can find the latest information on your favourite
source.
</P>

<P>
Eventually, you'll be able to browse this tree using /path/to/directory, and links
will be provided from each page to various areas of the website.

</TD></TR>

<?
#				print "NumRows = $NumRows\n<BR>";
				echo "<TR><TD>\n";
				$HTML = "";
				for ($i = 0; $i < $NumRows; $i++) {
					$myrow = $rows[$i];
					if ($myrow["directory_file_flag"] == 'D') {
						$HTML .= freshsource_Directory_Icon() . ' <A HREF="' . $_SERVER["SCRIPT_NAME"] . '?parent_id=' . $myrow["id"] . '">' . $myrow["name"] . '</A><BR>' . "\n";
					} else {
						$HTML .= freshsource_File_Icon() . ' ' . $myrow["name"] . "<BR>\n";
					}
				}

				echo $HTML;

				echo "</TD></TR>\n";
				echo "</TABLE>\n";
			} else {
				echo "<P>Sorry, nothing found in the database....</P>\n";
			}
		} else {
			echo "read from test failed " . pg_errormessage() . ' ' . $sql;
		}
	} else {
		echo "no connection";
	}

?>
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


</body>
</html>
