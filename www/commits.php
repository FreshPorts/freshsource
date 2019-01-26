<?php
	#
	# $Id: commits.php,v 1.33 2012-11-06 00:53:35 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");
#	require($_SERVER['DOCUMENT_ROOT'] . "/../classes/files-display.php");

	freshports_Start($FreshPortsSlogan,
					"$FreshPortsName - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug = 0;

if ($Debug) echo "UserID='$UserID'";

?>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">

<?php
if (file_exists("announcement.txt") && filesize("announcement.txt") > 4) {
?>
  <TR>
    <TD colspan="2">
       <?php include ("announcement.txt"); ?>
    </TD>
  </TR>
<?php
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
			$sql = '';

		if ($Debug) {
			echo "\$WatchListID = '$WatchListID'\n";
		}

GLOBAL $User;
GLOBAL $UserID;

if ($UserID) {
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
        commit_log_elements.revision_name,
        commit_log_elements.change_type
   FROM 
   (SELECT commit_log.id     AS commit_log_id, 
           commit_date       AS commit_date_raw,
           message_subject,
           message_id,
           committer,
           commit_log.description       AS commit_description,
           to_char(commit_log.commit_date - SystemTimeAdjust(), 'DD Mon YYYY')  AS commit_date,
           to_char(commit_log.commit_date - SystemTimeAdjust(), 'HH24:MI')      AS commit_time,
           encoding_losses,
           repo_id,
           svn_revision,
           repo.svn_hostname as svn_hostname
     FROM commit_log LEFT OUTER JOIN repo on commit_log.repo_id = repo.id
     
     JOIN 


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

if ($UserID) {
   $sql .= "
) AS SECURITY LEFT OUTER JOIN
(SELECT element_id as wle_element_id, COUNT(watch_list_id) as onwatchlist
  FROM watch_list JOIN watch_list_element
    ON watch_list.id      = watch_list_element.watch_list_id
       AND watch_list.user_id = " . $UserID . "
       AND watch_list.in_service
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

?>

<TR><TD VALIGN="top" WIDTH="100%">
<TABLE WIDTH="100%" border="1" CELLSPACING="0" CELLPADDING="8">
<TR>
		<?php freshports_PageBannerText("$MaxNumberOfPorts most recent commits", 3); ?>
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

<?php
				if ($Debug) print "NumRows = $NumRows\n<BR>";
				$NumberOfElementsInThisCommit = 0;
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
					
					$WhichRepo = freshports_MessageIdToRepoName($myrow["message_id"]);
					switch ($myrow["change_type"]) {
					  case "M":
					    $Change_Type = "modify";
					    break;

					  case "A":
					    $Change_Type = "import"; 
					    break;

					  case "R":
					    $Change_Type = "remove"; 
					    break;

					  default:
					    $Change_Type = $myrow["change_type"] ; 
					}


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

							$HTML .= ' <a href="commit.php?message_id=' . $myrow["message_id"] . '"><img src="images/copy.gif" width="16" height="16"></a>';


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
							
							if ($WatchListID) {
								if ($myrow["onwatchlist"]) {
									$HTML .= freshports_Watch_Link_Remove($myrow["element_id"]) . ' ';
								} else {
									$HTML .= freshports_Watch_Link_Add   ($myrow["element_id"]) . ' ';
								}
							}


							$HTML .= '<BIG><B>';
							switch($WhichRepo)
							{
								case FREEBSD_REPO_CVS:
									$HTML .= freshports_cvsweb_Revision_Link($myrow["element_pathname"] , $myrow["revision_name"]); 
									break;

								case FREEBSD_REPO_SVN:
									# we want something like
									# http://svnweb.freebsd.org/ports/head/textproc/bsddiff/Makefile?view=log#rev300953
									$HTML .= ' <A HREF="http://' . $myrow['svn_hostname'] . '/' . $myrow["element_pathname"] . '?view=log#rev' . $myrow["revision_name"] . '">' . 
									$myrow["element_pathname"] . '</a>';
									break;
							}

							$HTML .= "</B></BIG>\n";

							$HTML .= '</td>';

							$HTML .= '<td>';

							if ( $Change_Type == "modify" ) {
							switch($WhichRepo)
							{
								case FREEBSD_REPO_CVS:
									$HTML .= ' ';
									$previousRevision =  GetPreviousRevision( $myrow["revision_name"] );
									$HTML .= freshports_cvsweb_Diff_Link($myrow["element_pathname"] , $previousRevision, $myrow["revision_name"]);
									break;

								case FREEBSD_REPO_SVN:
									$HTML .= ' ';
									$previousRevision =  GetPreviousRevision( $myrow["revision_name"] );
									# we want something like http://svnweb.freebsd.org/ports/head/www/p5-App-Nopaste/Makefile?r1=300951&r2=300950&pathrev=300951
									$HTML .= ' <A HREF="http://' . $myrow['svn_hostname'] . '/' . $myrow["element_pathname"] . '?r1=' . 
										$myrow["revision_name"] . '&amp;r2=' . $previousRevision . '&amp;pathrev=' . $myrow["revision_name"] . '">';
									$HTML .= freshports_Diff_Icon() . '</a> ';
									break;
							}
						}

						switch($WhichRepo)
						{
							case FREEBSD_REPO_CVS:
							$HTML .= freshports_cvsweb_Annotate_Link($myrow["element_pathname"] , $myrow["revision_name"]); 
							break;

							case FREEBSD_REPO_SVN:
								# we want something like
								# http://svn.freebsd.org/ports/head/x11-wm/awesome/Makefile
								$HTML .= ' <A HREF="http://' . $myrow['svn_hostname'] . '/' .$myrow["element_pathname"] . '?annotate=' . $myrow["revision_name"] . '">';
								$HTML .= freshports_Revision_Icon() . '</a> ';
								break;
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
   <?php include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
 </TD>
</TR>
</TABLE>

<BR>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>


</body>
</html>
