<?
	# $Id: commit.php,v 1.3 2002-07-30 14:14:45 dan Exp $
	#
	# Copyright (c) 1998-2002 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	$message_id = AddSlashes($_GET["message_id"]);
	$commit_id  = AddSlashes($_GET["commit_id"]);
	$Title = "Commit found by ";
	if ($message_id) {
		$Title .= "message id";
	} else {
		$Title .= "commit id";
	}
	freshports_Start($Title,
					"$FreshPortsName - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug=0;

if ($Debug) echo "UserID='$UserID'";

$Debug = 0;

if (!$StartAt) {
   if ($Debug) {
      echo "setting StartAt to zero<br>\n";
      echo "UserID = $UserID<br>\n";
   }
   $StartAt = 0;
} else {
   $NewStart = floor($StartAt / $MaxNumberOfPorts) * $MaxNumberOfPorts;
   if ($NewStart != $StartAt) {
      $URL = basename($_SERVER["PHP_SELF"]);
      if ($NewStart > 0) {
         $URL .= "?StartAt=$NewStart";
      } else {
         $URL = "/";
      }
      header("Location: " . $URL );
      // Make sure that code below does not get executed when we redirect.
      exit;
   }
}

if ($Debug) {
   echo "StartAt = $StartAt<br>\n";
}

?>

<TABLE WIDTH="<? echo $TableWidth ?>" BORDER="0" ALIGN="center">

<?
if (file_exists("announcement.txt") && filesize("announcement.txt") > 4) {
?>
  <TR>
    <TD colspan="2">
       <? include ("announcement.txt"); ?>
    </TD>
  </TR>
<?
}
?>

<script language="php">

function StripQuotes($string) {
	$string = str_replace('"', '', $string);

	return $string;
}


function GetPortNameFromFileName($file_name) {

	list($fake, $subtree, $category, $port, $extra) = split('/', $file_name, 4);

#	return $subtree;
	return "$category/$port";

}

      $numrows = $MaxNumberOfPorts;
      $database=$db;
      if ($database) {
#
# we limit the select to recent things by using a date
# otherwise, it joins the whole table and that takes quite a while
#
#$numrows=400;

	if ($Debug) {
		echo "\$WatchListID = '$WatchListID'\n";
	}

#	$message_id = '200204081321.g38DLJL09396@freefall.freebsd.org';
#	$commit_id  = '
	

	$sql = "
	select	element_pathname(element.id) as element_pathname, 
			commit_log_elements.commit_log_id, 
			to_char(commit_log.commit_date - SystemTimeAdjust(), 'DD Mon YYYY')  as commit_date,
			to_char(commit_log.commit_date - SystemTimeAdjust(), 'HH24:MI:SS')   as commit_time,
			commit_log_elements.change_type,
			element.name as filename,
			commit_log.committer, 
			commit_log.message_id,
			commit_log.encoding_losses, 
			commit_log.description,
			commit_log_elements.revision_name as revision_name,
			element.status,
			element.id as element_id ";

	if ($WatchListID) {
		$sql .= ",
	       CASE when watch_list_element.element_id is null
    	      then 0
        	  else 1
	       END as watch ";
	}


	$sql .="
		   from commit_log, element, commit_log_elements ";

	#
	# if the watch list id is provided (i.e. they are logged in and have a watch list id...)
	#
	if ($WatchListID) {
		$sql .="
	            left outer join watch_list_element
				on (watch_list_element.element_id    = commit_log_elements.element_id
			   and  watch_list_element.watch_list_id = $WatchListID) ";
	}

	$sql .= "
	 where commit_log.message_id             = '$message_id'
	   and commit_log_elements.commit_log_id = commit_log.id 
	   and commit_log_elements.element_id    = element.id
	 order by 1";

	if ($Debug) echo '<PRE>' . $sql . '</PRE>';

#	$sql = "select id, message_id, message_date, message_subject, date_added,
#	               to_char(commit_date - SystemTimeAdjust(), 'DD Mon YYYY')           AS commit_date,
#    	           to_char(commit_date - SystemTimeAdjust(), 'HH24:MI')               AS commit_time,
#	               committer, description, system_id, encoding_losses
#	          from commit_log
#			  WHERE	commit_log.message_id = '$message_id'";


if ($Debug) echo "\n<pre>sql=$sql</pre>\n";

#exit;

         $result = pg_exec($database, $sql);
         if ($result) {
            $numrows = pg_numrows($result);
			if ($numrows) { 

				$i=0;
				$GlobalHideLastChange = "N";
				while ($myrow = pg_fetch_array ($result, $i)) {
					$rows[$i] = $myrow;

					#
					# if we do a limit, it applies to the big result set
					# not the resulting set if we also do a DISTINCT
					# thus, count the commit id's ourselves.
					#
#					if ($ThisChangeLogID <> $myrow["commit_log_id"]) {
#						$ThisChangeLogID  = $myrow["commit_log_id"];
						$i++;
#					}
#					echo "$i, ";
					if ($i >= $numrows) break;
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
		<? 
		freshports_PageBannerText($Title, 3);
		?>
        <? //echo ($StartAt + 1) . " - " . ($StartAt + $MaxNumberOfPorts) ?>
</TR>

<?
#				print "NumRows = $NumRows\n<BR>";
				$HTML = "";
				unset($ThisChangeLogID);
				for ($i = 0; $i < $NumRows; $i++) {
					$myrow = $rows[$i];
					$ThisChangeLogID = $myrow["commit_log_id"];


					if ($LastDate <> $myrow["commit_date"]) {
						$LastDate = $myrow["commit_date"];
						$HTML .= '<TR><TD COLSPAN="3" BGCOLOR="#AD0040" HEIGHT="0">' . "\n";
						$HTML .= '   <FONT COLOR="#FFFFFF"><BIG>' . FormatTime($myrow["commit_date"], 0, "D, j M Y") . '</BIG></FONT>' . "\n";
						$HTML .= '</TD></TR>' . "\n\n";
					}

					$j = $i;

					$HTML .= "<TR><TD>\n";

					// OK, while we have the log change log, let's put the port details here.
					$MultipleFilesThisCommit = 0;
					while ($j < $NumRows && $rows[$j]["commit_log_id"] == $ThisChangeLogID) {
						$myrow = $rows[$j];

						if ($MultipleFilesThisCommit) {
							$HTML .= '<BR>';
						}

						if (!$MultipleFilesThisCommit) {
							GLOBAL $freshports_mail_archive;

							$HTML .= '<SMALL>';
							$HTML .= '[ ' . $myrow["commit_time"] . ' ' . freshports_CommitterEmailLink($myrow["committer"]) . ' ]';
							$HTML .= '</SMALL>';
							$HTML .= '&nbsp;';
							$HTML .= freshports_Email_Link($myrow["message_id"]);

							if ($myrow["encoding_losses"] == 't') {
								$HTML .= '&nbsp;' . freshports_Encoding_Errors();
							}

#							$HTML .= '&nbsp;&nbsp;' . str_replace("cvs commit: ", "", $myrow["message_subject"]);
							$HTML .= "<BR>\n\n";
							$HTML .= "\n\n<BLOCKQUOTE>";
						}

							$HTML .= '<BIG><B>';
							$HTML .= '<A HREF="' . $freshports_CVS_URL . $myrow["element_pathname"] . '">';
							$HTML .= $myrow["element_pathname"] . " ";
							$HTML .= "</A></B></BIG>\n";

							if ($WatchListID) {
								if ($myrow["watch"]) {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Remove($myrow["element_id"]) . ' ';
								} else {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Add($myrow["element_id"]) . ' ';
								}
							}

							GLOBAL $freshports_FirstRevisionNumber;
							if ($myrow["revision_name"] !== $freshports_FirstRevisionNumber) {
								# add a link to the diffs
								$HTML .= '<A HREF="' . $freshports_CVS_URL . $myrow["element_pathname"] . ".diff?r1=";
								$HTML .= $myrow["revision_name"] . '&r2=' . freshportsNextRevisionNumber($myrow["revision_name"]). '">';
								$HTML .= 'diff</A>';
							}



#						$HTML .= freshports_CommitFilesLink($myrow["commit_log_id"], $myrow["category"], $myrow["port"]);

						// indicate if this port has been removed from cvs
						if ($myrow["status"] == "D") {
							$HTML .= " " . freshports_Deleted_Icon() . "\n";
						}

						if ($myrow["date_added"] > Time() - 3600 * 24 * $DaysMarkedAsNew) {
							$MarkedAsNew = "Y";
							$HTML .= freshports_New_Icon() . "\n";
						}

						$j++;
						$MultipleFilesThisCommit = 1;
					} // end while

					$i = $j - 1;

					$HTML .= freshports_PortDescriptionPrint($myrow["description"], $myrow["encoding_losses"]);

					$HTML .= "\n</BLOCKQUOTE>\n</TD></TR>\n\n\n";
				}

				echo $HTML;

	            echo "</TABLE>\n";
			} else {
			?>
<TR><TD VALIGN="top" WIDTH="100%">
<TABLE WIDTH="100%" border="1" CELLSPACING="0" CELLPADDING="8">
<TR>
<? freshports_PageBannerText($Title, 3); ?>
</TR>
<TR>
<?
				echo "<TD><P>Sorry, nothing found in the database....</P>\n";
				echo "</TD></TR></TABLE>";
			}
         } else {

            echo "read from test failed $sql";
         }

#         pg_exec ($database, "end");
      } else {
         echo "no connection";
      }

</script>

<P>Eventually, this page will contain a complete list of all files changed with links to the CVS repository.
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
