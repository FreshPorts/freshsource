<?php
	#
	# $Id: commit.php,v 1.6 2012-11-06 00:53:35 dan Exp $
	#
	# Copyright (c) 1998-2005 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");

	if (IsSet($_GET["message_id"])) $message_id = AddSlashes($_GET["message_id"]);
	if (IsSet($_GET["commit_id"]))  $commit_id  = AddSlashes($_GET["commit_id"]);

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

if ($Debug) {
   echo "StartAt = $StartAt<br>\n";
}

?>

<TABLE WIDTH="<?php echo $TableWidth ?>" BORDER="0" ALIGN="center">

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
	select	element_pathname(element.id, false) as element_pathname,
			element_pathname(element.id) as pathname, 
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
			element.id as element_id,
			repo_id,
			svn_revision,
			repo.repo_hostname  as repo_hostname,
			repo.repository     as respository,
			repo.name           as repo_name,
			repo.path_to_repo   as path_to_repo,
			repo.db_root_prefix as db_root_prefix,
			system_branch.branch_name as branch_name
			 ";

	if ($WatchListID) {
		$sql .= ",
	       CASE when watch_list_element.element_id is null
    	      then 0
        	  else 1
	       END as watch ";
	}


	$sql .="
		   from commit_log LEFT OUTER JOIN repo                ON (commit_log.repo_id = repo.id)
		                   LEFT OUTER JOIN commit_log_branches ON commit_log.id = commit_log_branches.commit_log_id
		                   LEFT OUTER JOIN system_branch       ON commit_log_branches.branch_id = system_branch.id, element, commit_log_elements ";

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


#exit;

         $result = pg_exec($database, $sql);
         if ($result) {
            $numrows = pg_num_rows($result);
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

?>

<TR><TD VALIGN="top" WIDTH="100%">
<TABLE WIDTH="100%" border="1" CELLSPACING="0" CELLPADDING="8">
<TR>
		<?php 
		freshports_PageBannerText($Title, 3);
		?>
</TR>

<?php
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
							if ($WhichRepo != FREEBSD_REPO_GIT) {
								$HTML .= freshports_Email_Link($myrow["message_id"]);
							}

							if ($myrow["encoding_losses"] == 't') {
								$HTML .= '&nbsp;' . freshports_Encoding_Errors();
							}

#							$HTML .= '&nbsp;&nbsp;' . str_replace("cvs commit: ", "", $myrow["message_subject"]);
							$HTML .= "<BR>\n\n";
							$HTML .= "\n\n<BLOCKQUOTE>";
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
                    $HTML .= ' <A HREF="http://' . $myrow['repo_hostname']  . $myrow["element_pathname"] . '?view=log#rev' . $myrow["revision_name"] . '">' .  $myrow["element_pathname"] . '</a>';
                    break;

		case FREEBSD_REPO_GIT:
			# we want something like
			# https://github.com/freebsd/freebsd-ports/blame/0957c7db9bf1fc4313cdefdcdc2608a0c965dda7/sysutils/goaccess/Makefile
			# https://cgit.freebsd.org/src/blame/sys/netinet/sctp_timer.c?id=5ac839029d01c0f48e1b1ff1a599cb47cf5e98ee
			# but we're getting
			# http://cgit.freebsd.org/src/blame//base/main/sys/netinet/sctp_timer.c?id=b963ce4588b33f733aef3f7a7f3fbe5018a89728

			$url_text = freshports_Convert_Subversion_Path_To_Git($myrow["pathname"], $myrow['repo_name'], $myrow['db_root_prefix'], $myrow['branch_name']);
			# we want https://cgit.freebsd.org/src/diff/sys/netinet/sctp_timer.c?id=b963ce4588b33f733aef3f7a7f3fbe5018a89728
			# we want https://cgit.freebsd.org/src/tree/sys/netinet/sctp_timer.c

			$HTML .= ' <a href="http://' . $myrow['repo_hostname'] . '/' . $myrow["repo_name"] . '/tree/' . $url_text . '" title="View file in repo">';
			$HTML .= '<CODE CLASS="code">' . $myrow["element_pathname"] . "</CODE></a>";
			break;
            }
							$HTML .= "</B></BIG>\n";

							if ($WatchListID) {
								if ($myrow["watch"]) {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Remove($myrow["element_id"]) . ' ';
								} else {
									$HTML .= '&nbsp; '. freshports_Watch_Link_Add   ($myrow["element_id"]) . ' ';
								}
							}

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
            		$HTML .= ' <A HREF="http://' . $myrow['repo_hostname'] . '/' . $myrow["element_pathname"] . '?r1=' . $myrow["revision_name"] . '&amp;r2=' . $previousRevision . '&amp;pathrev=' . $myrow["revision_name"] . '">';
        		$HTML .= freshports_Diff_Icon() . '</a> ';
                        break;

                    case FREEBSD_REPO_GIT:
			# we want: https://cgit.freebsd.org/src/diff/sys/netinet/sctp_timer.c?id=b963ce4588b33f733aef3f7a7f3fbe5018a89728
			$url_text = freshports_Convert_Subversion_Path_To_Git($myrow["pathname"], $myrow['repo_name'], $myrow['db_root_prefix'], $myrow['branch_name']);
			$HTML .= freshports_git_commit_Link_diff($myrow['message_id'], $myrow['repo_name'], $myrow['repo_hostname'], $url_text);
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
                    $HTML .= ' <A HREF="http://' . $myrow['repo_hostname'] . '/' .$myrow["element_pathname"] . '?annotate=' . $myrow["revision_name"] . '">';
                    $HTML .= freshports_Revision_Icon() . '</a> ';
                    break;

                case FREEBSD_REPO_GIT:
                	# something like
			# https://cgit.freebsd.org/src/blame/sys/netinet/sctp_timer.c?id=b963ce4588b33f733aef3f7a7f3fbe5018a89728
			$url_text = freshports_Convert_Subversion_Path_To_Git($myrow["pathname"], $myrow['repo_name'], $myrow['db_root_prefix'], $myrow['branch_name']);
			$HTML .= ' <A HREF="http://' . $myrow['repo_hostname'] . $myrow["path_to_repo"] . '/blame/' . $url_text . '?id=' . $myrow["revision_name"] . '">';
			$HTML .= freshports_Annotate_Icon() . '</a> ';
			break;
            }


						// indicate if this port has been removed from cvs
						if ($myrow["status"] == "D") {
							$HTML .= " " . freshports_Deleted_Icon() . "\n";
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
<?php freshports_PageBannerText($Title, 3); ?>
</TR>
<TR>
<?php
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

?>

<P>Eventually, this page will contain a complete list of all files changed with links to the CVS repository.
</TD>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
   <?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/side-bars.php") ?>
 </TD>
</TR>
</TABLE>

<BR>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
