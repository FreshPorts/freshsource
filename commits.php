<?
	# $Id: commits.php,v 1.5 2002-06-24 20:11:35 dan Exp $
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

if ($Debug) echo "UserID='$UserID'";

function freshports_SummaryForDay($MinusN) {          
   $BaseDirectory = "./archives";                     
   $Now = time();                                    
//   echo "$MinusN<br>\n"; 
   $File = $BaseDirectory . "/" . date("Y/m/d", $Now - 60*60*24*$MinusN) . ".inc";  
//   echo "$File<br>\n";
   if (file_exists($File)) {
      echo '<br><TABLE WIDTH="152" BORDER="1" CELLSPACING="0" CELLPADDING="5">';
      echo '  <TR>';
      echo '<TD bgcolor="#AD0040" height="30"><font color="#FFFFFF" SIZE="+1">';
      echo date("l j M", $Now - 60*60*24*$MinusN);
      echo '</font></TD>';
      echo '       </TR>';
      echo '        <TR>';
      echo '         <TD>';
      include($File);
      echo '   </TD>';
      echo '   </TR>';
      echo '   </TABLE>';
   }
}


//$Debug = 1;

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

<TABLE WIDTH="<? echo $TableWidth; ?>" BORDER="0" ALIGN="center">

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

<?php

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

if ($WatchListID) {
	$sql = " SELECT	commits_latest.*, 
				 	CASE when watch_list_element.element_id is null
						then 0
						else 1
					END as watch
			   FROM	commits_latest left outer join watch_list_element
					 ON commits_latest.element_id        = watch_list_element.element_id
					AND watch_list_element.watch_list_id = $WatchListID
		   ORDER BY commit_date_raw desc, category, port";
} else {
	$sql = "select id, message_id, message_date, message_subject, date_added,
	               to_char(commit_date - SystemTimeAdjust(), 'DD Mon YYYY')           AS commit_date,
    	           to_char(commit_date - SystemTimeAdjust(), 'HH24:MI')               AS commit_time,
	               committer, description, system_id, encoding_losses
	          from commit_log order by commit_log.commit_date desc limit $MaxNumberOfPorts";
}

if ($Debug) echo "\n<pre>sql=$sql</pre>\n";

#exit;

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

</TD></TR>

<?
#				print "NumRows = $NumRows\n<BR>";
				$HTML = "";
				for ($i = 0; $i < $NumRows; $i++) {
					$myrow = $rows[$i];

					if ($LastDate <> $myrow["commit_date"]) {
						$LastDate = $myrow["commit_date"];
						$HTML .= '<TR><TD COLSPAN="3" BGCOLOR="#AD0040" HEIGHT="0">' . "\n";
						$HTML .= '   <FONT COLOR="#FFFFFF"><BIG>' . FormatTime($myrow["commit_date"], 0, "D, j M") . '</BIG></FONT>' . "\n";
						$HTML .= '</TD></TR>' . "\n\n";
					}


					$HTML .= "<TR><TD>\n";

					// OK, while we have the log change log, let's put the port details here.

					GLOBAL $freshports_mail_archive;

					$HTML .= '<SMALL>';
					$HTML .= '[ ' . $myrow["commit_time"] . ' ' . freshports_CommitterEmailLink($myrow["committer"]) . ' ]';
					$HTML .= '</SMALL>';
					$HTML .= '&nbsp;';
					$HTML .= freshports_Email_Link($myrow["message_id"]);

					$HTML .= "&nbsp;";

#					$HTML .= freshports_CommitFilesLinkIDOnly($myrow["id"]);


					$HTML .= "\n\n<BR>\n\n<BLOCKQUOTE>";

					$HTML .= str_replace("cvs commit: ", "", $myrow["message_subject"]);

					if ($myrow["encoding_losses"] == 't') {
						$HTML .= '&nbsp;' . freshports_Encoding_Errors();
					}
					if ($WatchListID) {
						if ($myrow["watch"]) {
							$HTML .= ' '. freshports_Watch_Link_Remove($myrow["element_id"]) . ' ';
						} else {
							$HTML .= ' '. freshports_Watch_Link_Add($myrow["element_id"]) . ' ';
						}
					}


					$HTML .= freshports_PortDescriptionPrint($myrow["description"], $myrow["encoding_losses"], $freshports_CommitMsgMaxNumOfLinesToShow, freshports_MoreCommitMsgToShow($myrow["message_id"], $freshports_CommitMsgMaxNumOfLinesToShow));

					$HTML .= "\n</BLOCKQUOTE>\n</TD></TR>\n\n\n";
				}

				echo $HTML;

	            echo "</TABLE>\n";
			} else {
				echo "<P>Sorry, nothing found in the database....</P>\n";
			}
         } else {
            echo "read from test failed";
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
