<?php
	#
	# $Id: commits.php,v 1.26 2003-04-24 14:38:26 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

	freshports_Start($FreshPortsSlogan,
					"$FreshSourceName - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug = 0;

if ($Debug) echo "UserID='$UserID'";

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

      $database=$db;
      if ($database) {

		if ($Debug) {
			echo "\$WatchListID = '$WatchListID'\n";
		}

if ($User->id) {
	$sql = "
SELECT SECURITY.*,
       W.*
FROM (
";
}

$sql .= "
SELECT COMMITELEMENT.*,
       security_notice.id  AS security_notice_id
FROM (
SELECT LCCLLC.*,
       element.name    AS name,
       element.status  AS status,
       element_pathname(element.id, FALSE) as element_pathname


FROM (
 SELECT LCCL.*, 
        commit_log_elements.element_id,
        commit_log_elements.revision_name
   FROM 
   (SELECT commit_log.id     AS commit_log_id, 
           commit_date       AS commit_date_raw,
           message_subject,
           message_id,
           committer,
           description       AS commit_description,
           to_char(commit_log.commit_date - SystemTimeAdjust(), 'DD Mon YYYY')  AS commit_date,
           to_char(commit_log.commit_date - SystemTimeAdjust(), 'HH24:MI')      AS commit_time,
           encoding_losses
     FROM commit_log JOIN


               (SELECT latest_commits.commit_log_id
                   FROM latest_commits
               ORDER BY latest_commits.commit_date DESC
                 LIMIT $MaxNumberOfPorts) AS LC


           ON commit_log.id = LC.commit_log_id) AS LCCL JOIN commit_log_elements
                         ON commit_log_elements.commit_log_id = LCCL.commit_log_id
                        AND commit_log_elements.commit_log_id > latest_commits_Anchor()) AS LCCLLC JOIN element



on LCCLLC.element_id = element.id

) AS COMMITELEMENT LEFT OUTER JOIN security_notice
ON COMMITELEMENT.commit_log_id = security_notice.commit_log_id
";

if ($User->id) {
   $sql .= "
) AS SECURITY LEFT OUTER JOIN
(SELECT element_id as wle_element_id, COUNT(watch_list_id) as watch
  FROM watch_list JOIN watch_list_element
    ON watch_list.id      = watch_list_element.watch_list_id
       AND watch_list.user_id = 1
GROUP BY element_id) AS W
ON        W.wle_element_id = SECURITY.element_id
";
}

$sql .= "order by commit_date_raw desc, element_pathname";


		if ($Debug) echo "\n<pre>sql=$sql</pre>\n";

		$result = pg_exec($database, $sql);
		if ($result) {
			$numrows = pg_numrows($result);
			if ($Debug) echo $numrows . " rows to fetch\n";
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
</TR>
<TR><TD>
<P>
Welcome to FreshSource, where you can find the latest information on your favourite
source.

<P>
See also <A HREF="http://www.FreshPorts.org/">FreshPorts</A> which
runs off the same database and displays only the port commits.
</P>

</TD></TR>

<?
				if ($Debug) print "NumRows = $NumRows\n<BR>";
				for ($i = 0; $i < $NumRows; $i++) {
					$HTML = "";
					$NumberOfElementsInThisCommit++;
					$myrow = $rows[$i];
					$ThisCommitLogID = $myrow["commit_log_id"];

					if ($LastDate <> $myrow["commit_date"]) {
						$LastDate = $myrow["commit_date"];
						$HTML .= '<TR><TD COLSPAN="3" BGCOLOR="#AD0040" HEIGHT="0">' . "\n";
						$HTML .= '   <FONT COLOR="#FFFFFF"><BIG>' . FormatTime($myrow["commit_date"], 0, "D, j M Y") . '</BIG></FONT>' . "\n";
						$HTML .= '</TD></TR>' . "\n\n";
					}


					$HTML .= "<TR><TD>\n";

					// OK, while we have the log change log, let's put the port details here.

					GLOBAL $freshports_mail_archive;

					$NumberOfElementsInThisCommit = 0;
					$MaxNumberElementssToShow     = 10;
					$j = $i;
					while ($j < $NumRows && $rows[$j]["commit_log_id"] == $ThisCommitLogID) {
						$NumberOfElementsInThisCommit++;
						$myrow = $rows[$j];

						if ($NumberOfElementsInThisCommit == 1) {

							$HTML .= '<SMALL>';
							$HTML .= '[ ' . $myrow["commit_time"] . ' ' . freshports_CommitterEmailLink($myrow["committer"]) . ' ]';
							$HTML .= '</SMALL>';
							$HTML .= '&nbsp;';
							$HTML .= freshports_Email_Link($myrow["message_id"]);

							$HTML .= "&nbsp;";

#							$HTML .= freshports_CommitFilesLinkIDOnly($myrow["id"]);


							if ($myrow["encoding_losses"] == 't') {
								$HTML .= '&nbsp;' . freshports_Encoding_Errors();
							}

#							$HTML .= '&nbsp;&nbsp;' . str_replace("cvs commit: ", "", $myrow["message_subject"]);
							$HTML .= "<BR>\n\n";
							$HTML .= "\n\n<BLOCKQUOTE>";

							# put the elements items into a table
							$HTML .= '<table border="0">' . "\n";
						}

						if ($NumberOfElementsInThisCommit <= $MaxNumberElementssToShow) {
							$HTML .= '<tr><td>';
							$HTML .= '<BIG><B>';
							$HTML .= '<A HREF="' . $freshports_CVS_URL . '/' . $myrow["element_pathname"] . '">';
							$HTML .= $myrow["element_pathname"] . " ";
							$HTML .= "</A></B></BIG>\n";

							$HTML .= '</td><td>';
							$HTML .= '&nbsp;&nbsp;'. $myrow["revision_name"] . ' ';
							$HTML .= '</td>';

							if ($WatchListID) {
								if ($myrow["watch"]) {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Remove($myrow["element_id"]) . ' ';
								} else {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Add($myrow["element_id"]) . ' ';
								}
							}

							$HTML .= '<td>';
							if ($myrow["revision_name"] !== "1.1") {
								# add a link to the diffs
								$HTML .= '<A HREF="' . $freshports_CVS_URL . '/' . $myrow["element_pathname"] . ".diff?r1=";
								$HTML .= $myrow["revision_name"] . '&amp;r2=' . freshportsNextRevisionNumber($myrow["revision_name"]). '">';
								$HTML .= 'diff</A>';
							}
							$HTML .= '</td></tr>' . "\n";
						}
						$j++;
					}

					$HTML .= '</table>' . "\n";
					$HTML .= "\n</BLOCKQUOTE>\n";


					if ($NumberOfElementsInThisCommit > $MaxNumberElementssToShow) {
						$HTML .= '<BR>' . freshports_MorePortsToShow($myrow["message_id"], $NumberOfElementsInThisCommit, $MaxNumberElementssToShow);
					}

					$i = $j - 1;

					$HTML .= "\n\n<BLOCKQUOTE>";
					$HTML .= freshports_PortDescriptionPrint($myrow["commit_description"], $myrow["encoding_losses"], $freshports_CommitMsgMaxNumOfLinesToShow, freshports_MoreCommitMsgToShow($myrow["message_id"], $freshports_CommitMsgMaxNumOfLinesToShow));
					$HTML .= "\n</BLOCKQUOTE>\n";

					$HTML .= "\n</TD></TR>\n\n\n";
					echo $HTML;
				}

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
