<?php
	# $Id: watch.php,v 1.5 2007-10-08 23:07:40 dan Exp $
	#
	# Copyright (c) 1998-2002 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");

$Debug = 0;

$visitor = $_COOKIE["visitor"];
$PageTitle = "your watched sources";

// if we don't know who they are, we'll make sure they login first
if (!$visitor) {
        header("Location: login.php?origin=" . $_SERVER["PHP_SELF"]);  /* Redirect browser to PHP web site */
        exit;  /* Make sure that code below does not get executed when we redirect. */
}

	freshports_Start($PageTitle,
					$FreshPortsTitle . " - source applications",
					"FreeBSD, index, applications, ports");
?>

<table width="<?php echo $TableWidth ?>" border="0" ALIGN="center">

<tr><td valign="top" width="100%">
<table width="100%" border="0">
<tr>
	<?php freshports_PageBannerText($PageTitle); ?>
</tr>
<tr><td>
<UL>
<LI>These are the sources which are on your watch list.<BR>
<LI>At present, it shows only those elements which already have a commit found within the database.  In future
it will show everything on your watch list.
</UL>
</td></tr>
<?php

$DESC_URL = "ftp://ftp.freebsd.org/pub/FreeBSD/branches/-current/ports";

if ($UserID == '') {
   echo '<tr><td>';
   echo 'You must be logged in order to view your watch lists.';
   echo '</td></tr>';
} else {


$WatchID = freshports_MainWatchID($UserID, $db);

// make sure the value for $sort is valid

echo '<TR HEIGHT="30" VALIGN="center"><td>' . "\nThis page is ";

$sort = $_GET["sort"];

switch ($sort) {
	case "filename":
		$sort ="element_pathname, commit_date_sort_field desc";
		echo 'sorted by file name.  but you can sort by <a href="' . $_SERVER["PHP_SELF"] . '?sort=commit">commit date</a>';
		break;

	default:
		$sort = "commit_date_sort_field desc, element_pathname";
		echo 'sorted by commit date.  but you can sort by <a href="' . $_SERVER["PHP_SELF"] . '?sort=filename">file name</a>';
		break;
}

echo "<BR></td></tr>\n";

$UpdateCache = 1;

if ($WatchID == '') {
   echo "<tr><td>Your watch list is empty.</td></tr>";
} else {

if ($UpdateCache == 1) {
//   echo 'time to update the cache';


#
# select the latest commits into a temporary table
#
$sql = "
select max(commit_log.commit_date) as commit_date, watch_list_element.element_id
 into temporary element_latest_commits
  from watch_list_element, commit_log_elements, commit_log 
 where watch_list_id = $WatchID
   and commit_log_elements.element_id = watch_list_element.element_id 
   and commit_log.id = commit_log_elements.commit_log_id
 group by watch_list_element.element_id ";

$result = pg_exec($db, $sql);
if (!$result) {
	echo pg_errormessage();
}

$sql = "";
$sql = "
SELECT element_pathname(element.id) as element_pathname, 
       to_char(commit_log.commit_date - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as updated, 
       commit_log.committer, 
       commit_log.description as update_description, 
       element.id as element_id, 
       to_char(commit_log.date_added - SystemTimeAdjust(), 'DD Mon YYYY HH24:MI:SS') as date_added, 
       to_char(commit_log.commit_date - SystemTimeAdjust(), 'DD Mon YYYY')     AS commit_date,
       to_char(commit_log.commit_date - SystemTimeAdjust(), 'HH24:MI')         AS commit_time,
       commit_log.commit_date as commit_date_sort_field, 
       element.status, 
       commit_log.message_id,
       1 as onwatchlist 
  from element, commit_log, commit_log_elements, watch_list_element INNER JOIN element_latest_commits
            ON (element_latest_commits.element_id = watch_list_element.element_id) 
 where commit_log_elements.commit_log_id  = commit_log.id 
   and watch_list_element.watch_list_id   = $WatchID
   and commit_log_elements.element_id     = watch_list_element.element_id 
   and element_latest_commits.commit_date = commit_log.commit_date 
   and element.id                         = watch_list_element.element_id 
";

$sql .= " order by $sort ";
//$sql .= " limit 20";

//$Debug=1;
if ($Debug) {
   echo $sql;
}

#exit;

$result = pg_exec($db, $sql);
if (!$result) {
	echo pg_errormessage();
}
//$HTML = "</tr></td><tr>";

$HTML .= '<tr><td>';

// get the list of topics, which we need to modify the order
$NumPorts=0;

require($_SERVER['DOCUMENT_ROOT'] . "/../classes/ports.php");
$port = new Port($db);
$port->LocalResult = $result;

$LastCategory='';
$GlobalHideLastChange = "N";
$numrows = pg_numrows($result);

$HTML .= '<DL>';

for ($i = 0; $i < $numrows; $i++) {
	$myrow = pg_fetch_array ($result, $i);
	$HTML .= "<DT>";
	if ($WatchID) {
		if ($myrow['onwatchlist']) {
			$HTML .= ' '. freshports_Watch_Link_Remove($myrow['element_id']);
		} else {
			$HTML .= ' '. freshports_Watch_Link_Add($myrow['element_id']);
		}
	}

							$HTML .= '<BIG><B>';
							$HTML .= '<A HREF="' . $freshports_CVS_URL . $myrow["element_pathname"] . '">';
							$HTML .= $myrow["element_pathname"] . " ";
							$HTML .= "</A></B></BIG>\n";
			$HTML .= "<BR>";
	$HTML .= "</DT><DD>";
            $HTML .= 'last change committed by ' . freshports_CommitterEmailLink($myrow["committer"]);  // separate lines in case committer is null
 
            $HTML .= ' on <font size="-1">' . $myrow["updated"] . '</font>' . "\n";

			$HTML .= freshports_Email_Link($myrow["message_id"]);

			if ($myrow["encoding_losses"] == 't') {
					$HTML .= '&nbsp;' . freshports_Encoding_Errors();
			}

			$HTML .= ' ' . freshports_Commit_Link($myrow["last_commit_id"]);
			$HTML .= ' ' . freshports_CommitFilesLink($myrow["last_commit_id"], $myrow["category"], $myrow["name"]);


			$HTML .= freshports_PortDescriptionPrint($myrow["update_description"], $myrow["encoding_losses"]);

	$HTML .= '</DD>';
}

$HTML .= "\n</DD>\n</DL>\n";

}

$HTML .= "</td></tr>\n";

$HTML .= "<tr><td><BR>$numrows elements found</td></tr>\n";

echo $HTML;

} // end if no WatchID
}

?>
</table>
</td>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
</td>
</tr>
</table>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
