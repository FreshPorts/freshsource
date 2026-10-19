<?php
    #
	# $Id: repo.php,v 1.7 2012-03-14 18:16:45 dan Exp $
	#
	# Copyright (c) 1998-2026 Dan Langille
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");
	require_once('/usr/local/www/freshports/include/freshports.php');

	freshports_Start($FreshPortsSlogan,
					"FreshSource - new ports, applications",
					"FreeBSD, index, applications, ports");
$Debug = 0;

#phpinfo();
    $parent_id = 0;
    $path      = '/';

    if (IsSet($_GET["parent_id"])) {
      $parent_id = intval($_GET["parent_id"]);
    }

    if (IsSet($_GET["path"])) {
      $path = htmlentities($_GET["path"]);
      if ($Debug) syslog(LOG_NOTICE, 'path = ' . $path);
      $parent_id = Pathname_ID($db, $path);
      if ($parent_id === NULL) {
        $path = '/';
      } else {
        # do not trust what they gave us.
        $ElementPathname = ElementPathname($db, $parent_id);
        $path = htmlentities($ElementPathname);
      }
    }

    $Breadcrumbs = Breadcrumbs($db, '?path=', $parent_id);

    $PathToParent = ElementPathname($db, GetParent($db, $parent_id));
    if ($PathToParent == '' || $PathToParent == '/') {
      $ParentGraphic = '<img src="images/parent_dir.png">';
    } else {
      $ParentGraphic = '<a href="' .  $_SERVER["SCRIPT_NAME"] . '?path=' . htmlentities($PathToParent) . '"><img src="images/parent_dir.png"></a>';
    }

?>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">

<?php


	$numrows = $MaxNumberOfPorts;
	$database=$db;
	if ($database) {
#
# we limit the select to recent things by using a date
# otherwise, it joins the whole table and that takes quite a while
#
#$numrows=400;

		$sql = 'SELECT id, name, parent_id, directory_file_flag, status';
		if ($WatchListID) {
		  $sql .= ',
	       CASE when watch_list_element.element_id is null
    	      then 0
        	  else 1
	       END as watch ';
	}


	$sql .= " FROM element ";

    	if ($WatchListID) {
	    	$sql .= '
	            left outer join watch_list_element
				on (watch_list_element.element_id    = element.id
			       and  watch_list_element.watch_list_id = $2) ';
        }

	if ($parent_id) {
		$sql .= ' WHERE parent_id = $1';
	} else {
		$sql .= ' WHERE parent_id is NULL';
	}
	$sql .= ' ORDER BY directory_file_flag, name';

	if ($Debug) echo "\n<pre>sql=$sql</pre>\n";

	if ($parent_id && $WatchListID) {
		$params = array($parent_id, $WatchListID);
	} elseif ($parent_id) {
		$params = array($parent_id);
	} elseif ($WatchListID) {
		$params = array($WatchListID);
	} else {
		$params = array();
	}
	$result = pg_query_params($database, $sql, $params);
	if ($result) {
		$numrows = pg_num_rows($result);
#		echo $numrows . " rows to fetch\n";
		if ($numrows) {

			$GlobalHideLastChange = "N";
			for ($i = 0; $i < $numrows; $i++) {
				$myrow = pg_fetch_array ($result, $i);
				$rows[$i] = $myrow;
			}

			$NumRows = $numrows;
?>

<TR><TD VALIGN="top" WIDTH="100%">
<TABLE WIDTH="100%" border="1" CELLSPACING="0" CELLPADDING="8">
<TR>
		<?php freshports_PageBannerText("$MaxNumberOfPorts most recent commits", 3); ?>
        <?php //echo ($StartAt + 1) . " - " . ($StartAt + $MaxNumberOfPorts) ?>
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

<?php
#				print "NumRows = $NumRows\n<BR>";
				echo "<TR><TD>\n";
                echo '<p>' . $ParentGraphic . ' ' . $Breadcrumbs . '<p>';
				$HTML = "";
				if (empty($path)) {
				  $PathPrefix = '/';
                } else {
                  $PathPrefix = $path;
                }
                if (substr($PathPrefix, strlen($PathPrefix) - 1) != '/') {
                  $PathPrefix .= '/';
                }

				for ($i = 0; $i < $NumRows; $i++) {
					$myrow = $rows[$i];
					if ($WatchListID) {
						if ($myrow["watch"]) {
							$HTML .= '&nbsp; '. freshports_Watch_Link_Remove($myrow["id"]) . ' ';
						} else {
							$HTML .= '&nbsp; '. freshports_Watch_Link_Add   ($myrow["id"]) . ' ';
						}
					}

					if ($myrow["directory_file_flag"] == 'D') {
						$HTML .= freshsource_Directory_Icon() . ' <A HREF="' . $_SERVER["SCRIPT_NAME"] . '?path=' . $PathPrefix . $myrow["name"] . '">' . $myrow["name"] . '</A><BR>' . "\n";
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
			echo "read from test failed " . pg_last_error($db) . ' ' . $sql;
		}
	} else {
		echo "no connection";
	}

?>
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
