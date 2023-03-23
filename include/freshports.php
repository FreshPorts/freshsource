<?php

	#
	# $Id: freshports.php,v 1.22 2012-11-06 00:53:35 dan Exp $
	#
	# Copyright (c) 1998-2005 DVL Software Limited
	#

	require_once("/usr/local/www/freshports/include/constants.php");
#	require_once($_SERVER['DOCUMENT_ROOT'] . "/../include/constants.php");

if ($Debug) echo "'" . $_SERVER["DOCUMENT_ROOT"] . "/../classes/watchnotice.php'<BR>";

require_once($_SERVER["DOCUMENT_ROOT"] . "/../classes/watchnotice.php");


#
# special HTMLified mailto to foil spam harvesters
#
$mailto = '&#109;&#97;&#105;&#108;&#116;&#111;';

function freshsource_Directory_Icon() {
	return '<IMG SRC="/images/dir.gif" ALT="[DIR]" BORDER="0" WIDTH="20" HEIGHT="22">';
}

function freshsource_File_Icon() {
	return '<IMG SRC="/images/text.gif" ALT="[TXT]" BORDER="0" WIDTH="20" HEIGHT="22">';
}

// common things needs for all freshports php3 pages

function freshsource_Logo() {
GLOBAL $TableWidth;
GLOBAL $LocalTimeAdjustment;
GLOBAL $FreshPortsLogo;
GLOBAL $FreshPortsSlogan;
GLOBAL $FreshPortsLogoWidth;
GLOBAL $FreshPortsLogoHeight;

#echo "$LocalTimeAdjustment<BR>";

echo '<BR>
<TABLE WIDTH="' . $TableWidth . '" BORDER="0" ALIGN="center">
<TR>
	<TD><A HREF="';

	if ($_SERVER["PHP_SELF"] == "/index.php") {
		echo 'other-copyrights.php';
	} else {
		echo '/';
	}
        echo '"><IMG SRC="' . $FreshPortsLogo . '" ALT="' . $FreshPortsSlogan . ' " WIDTH="' . $FreshPortsLogoWidth . '" HEIGHT="' . $FreshPortsLogoHeight . '" BORDER="0"></A></TD>
        <TD ALIGN="right" CLASS="sans" VALIGN="bottom">' . FormatTime(Date("D, j M Y g:i A T"), $LocalTimeAdjustment, "D, j M Y g:i A T") . '</TD>
</TR>
</TABLE>
';


}

function freshports_MainWatchID($UserID, $db) {

	$sql = "select users.last_watch_list_chosen as id ".
	       "  from users ".
	       " where id = $UserID";
	       
#	echo "<pre>$sql</pre>";

	$result = pg_exec ($db, $sql);

	if(pg_num_rows($result)) {
		$myrow = pg_fetch_array ($result, 0);
		$WatchID = $myrow["id"];
	}

	# if nothing supplied in the above
	if (!$WatchID) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/watch_lists.php');

		$WatchLists = new WatchLists($db);
		$WatchID = $WatchLists->GetDefaultWatchListID($UserID);
#		echo "WatchID found is '$WatchID'<br>\n";
	}

	return $WatchID;
}

function freshports_echo_HTML($text) {
//   echo $text;
   return $text;
}

function freshports_echo_HTML_flush() {
   echo $HTML_Temp;
}


function BreadcrumbArrayToLinks($prefix, $breadcrumbs) {
	$HTML = '';
	
	$Path = '';

	foreach($breadcrumbs as $value => $dir) {
		$HTML .= ' ';
		if (empty($dir)) {
			$Path .= '';
			$HTML .= '<a href="' . $prefix . '/">/</a>'. ' ';
#			'/ ' . $Path;
		} else {
			$Path .= '/' . $dir;
			$HTML .= '<a href="' . $prefix . $Path . '">' . $dir . '</a>'. ' / ';
		}
	}

	// remove trailing /
	$HTML = substr($HTML, 0, strlen($HTML) - 3);

	return $HTML;
}

function Breadcrumbs($db, $prefix, $element_id) {

	if (empty($element_id)) {
		$element_id = 0;
		$Breadcrumbs = '/';
	} else {

		$element_pathname = ElementPathname($db, $element_id);
	
		# syslog(LOG_NOTICE, $element_pathname);

		$DirectoryNames = explode('/', $element_pathname);
	
		$Breadcrumbs = BreadcrumbArrayToLinks($prefix, $DirectoryNames);
	}
	
	return $Breadcrumbs;
}
	
function ElementPathname($db, $element_id) {
	$ElementPathname = '';

    if (empty($element_id)) {
		$ElementPathname = '/';
	} else {
		$sql = "select element_pathname($element_id) as elementpathname";

		$result = pg_exec($db, $sql);
		if ($result) {
			if (pg_num_rows($result)) {
				$myrow = pg_fetch_array($result, 0);
				$ElementPathname = $myrow["elementpathname"];
			}
		} else {
			syslog(LOG_ERR, pg_errormessage() . ':' . $sql);
		}
	}

	return $ElementPathname;
}

function Pathname_ID($db, $Pathname) {
	$ElementID = 0;

	$sql = "select Pathname_ID('" . pg_escape_string($db, $Pathname) . "') as element_id";

	$result = pg_exec($db, $sql);
	if ($result) {
		if (pg_num_rows($result)) {
			$myrow = pg_fetch_array($result, 0);
			$ElementID = $myrow["element_id"];
		}
	} else {
		syslog(LOG_ERR, pg_errormessage() . ':' . $sql);
	}

	return $ElementID;
}

function GetParent($db, $element_id) {
	$ElementID = 0;
	if (empty($element_id)) {
		return 0;
	}

	$sql = "select GetParentID(" . pg_escape_string($db, $element_id) . ") as parent_id";

	$result = pg_exec($db, $sql);
	if ($result) {
		if (pg_num_rows($result)) {
			$myrow = pg_fetch_array($result, 0);
			$ElementID = $myrow["parent_id"];
		}
	} else {
		syslog(LOG_ERR, pg_errormessage() . ':' . $sql);
	}

	return $ElementID;
}

function freshports_SideBarHTMLParm($Self, $URL, $Parm, $Title) {
   if ($Self == $URL || ($Self == '/index.php' && $URL == '/')) {
      $HTML = $Title;
   } else {
      $HTML = '<a href="' . $URL . $Parm . '">' . $Title . '</a>';
   }
      
   return $HTML;
}

function freshports_PortDetails($port, $db, $ShowDeletedDate, $DaysMarkedAsNew, $GlobalHideLastChange, $HideCategory, $HideDescription, $ShowChangesLink, $ShowDescriptionLink, $ShowDownloadPortLink, $ShowEverything, $ShowHomepageLink, $ShowLastChange, $ShowMaintainedBy, $ShowPortCreationDate, $ShowPackageLink, $ShowShortDescription, $LinkToPort=0, $AddRemoveExtra='', $ShowCategory=1, $ShowDateAdded="N") {
//
// This php3 fragment does the basic port information for a single port.
//
	GLOBAL $freshports_CVS_URL;
	GLOBAL $freshports_FTP_URL;
	GLOBAL $ShowDepends;
	GLOBAL $WatchListID;

	$MarkedAsNew = "N";
	$HTML .= "<DL>\n";

	$HTML .= "<DT>";

	$HTML .= '<BIG><B>';

	if ($LinkToPort) {
		$HTML .= "<A HREF=\"/$port->category/$port->port/\">$port->port";
	} else {
		$HTML .= $port->port;
	}

	if (strlen($port->{version}) > 0) {
    	$HTML .= ' ' . $port->{version};
		if (strlen($port->{revision}) > 0 && $port->{revision} != "0") {
    		$HTML .= '-' . $port->{revision};
		}
	}

	if ($LinkToPort) {
		$HTML .= '</A>';
	}

	$HTML .= "</B></BIG>";

	if ($ShowCategory) {
		$HTML .= ' / <A HREF="/' . $port->category . '/">' . $port->category . '</A>';
	}

	if ($WatchListID) {
		if ($port->{onwatchlist}) {
			$HTML .= ' '. freshports_Watch_Link_Remove($port->{element_id});
		} else {
			$HTML .= ' '. freshports_Watch_Link_Add($port->{element_id});
		}
	}

	// indicate if this port has been removed from cvs
	if ($port->{status} == "D") {
		$HTML .= " " . freshports_Deleted_Icon() . "\n";
	}

	// indicate if this port needs refreshing from CVS
	if ($port->{needs_refresh}) {
		$HTML .= " " . freshports_Refresh_Icon() . "\n";
	}

	if ($port->{date_added} > Time() - 3600 * 24 * $DaysMarkedAsNew) {
		$MarkedAsNew = "Y";
		$HTML .= freshports_New_Icon() . "\n";
	}

	if ($port->{forbidden}) {
		$HTML .= freshports_Forbidden_Icon() . "\n";
	}

	$HTML .= "</DT>\n<DD>";
	# show forbidden and broken
	if ($port->forbidden) {
		$HTML .= freshports_Forbidden_Icon() . 'FORBIDDEN: ' . $port->forbidden . "<br>";
	}

	if ($port->broken) {
		$HTML .= freshports_Broken_Icon() . ' BROKEN: ' . $port->broken . "<br>"; ;
	}

   // description
   if ($port->short_description && ($ShowShortDescription == "Y" || $ShowEverything)) {
      $HTML .= htmlspecialchars($port->short_description);
      $HTML .= "<br>\n";
   }

   // maintainer
   if ($port->maintainer && ($ShowMaintainedBy == "Y" || $ShowEverything)) {
      $HTML .= '<i>';
      if ($port->status == 'A') {
         $HTML .= 'Maintained';
      } else {
         $HTML .= 'was maintained'; 
      }
      GLOBAL $mailto;
      $HTML .= ' by:</i> <A HREF="' . $mailto . ':' . $port->maintainer;
      $HTML .= htmlspecialchars('?cc=ports@FreeBSD.org&subject=FreeBSD%20Port:%20' . $port->port . '-' . $port->version) . '">';
      $HTML .= $port->maintainer . "</A><BR>";
  }

   // there are only a few places we want to show the last change.
   // such places set $GlobalHideLastChange == "Y"
   if ($GlobalHideLastChange != "Y") {
      if ($ShowLastChange == "Y" || $ShowEverything) {
         if ($port->updated != 0) {
            $HTML .= 'last change committed by ' . freshports_CommitterEmailLink($port->committer);  // separate lines in case committer is null
 
            $HTML .= ' on <font size="-1">' . $port->updated . '</font>' . "\n";

			$HTML .= freshports_Email_Link($port->message_id);

			if ($port->encoding_losses == 't') {
					$HTML .= '&nbsp;' . freshports_Encoding_Errors();
			}

			$HTML .= ' ' . freshports_Commit_Link($port->last_commit_id);
			$HTML .= ' ' . freshports_CommitFilesLink($port->last_commit_id, $port->category, $port->name);


			$HTML .= freshports_PortDescriptionPrint($port->update_description, $port->encoding_losses);
 
         } else {
            $HTML .= "no changes recorded in FreshPorts<br>\n";
         }
      }
   }

	# show the date added, if asked

	if ($ShowDateAdded == "Y" || $ShowEverything) {
		$HTML .= 'port added on <font size="-1">' . $port->date_added . '</font><BR>' . "\n";
	}

   if ($port->categories) {
      // remove the primary category
      $Categories = str_replace($port->category, '', $port->categories);
      $Categories = str_replace('  ', ' ', $Categories);
      if ($Categories) {
         $HTML .= "<i>Also listed in:</i> ";
         $CategoriesArray = explode(" ", $Categories);
         $Count = count($CategoriesArray);
         for ($i = 0; $i < $Count; $i++) {
            $Category = $CategoriesArray[$i];
            $CategoryID = freshports_CategoryIDFromCategory($Category, $db);
            if ($CategoryID) {
               // this is a real category
               $HTML .= '<a href="/' . $Category . '/">' . $Category . '</a>';
            } else {
               $HTML .= $Category;
            }
            if ($i < $Count - 1) {
               $HTML .= " ";
            }
         }
      $HTML .= "<br>\n";
      }
   }

/*
echo 'build = ' . $port->depends_build . "<br>\n";
echo 'run   = ' . $port->depends_run . "<br>\n";
*/

if ($ShowDepends) {
   if ($port->depends_build) {
      $HTML .= "<i>required to build:</i> ";

      // sometimes they have multiple spaces in the data...
      $temp = str_replace('  ', ' ', $port->depends_build);
      
      // split each depends up into different bits
      $depends = explode(' ', $temp);
      $Count = count($depends);
      for ($i = 0; $i < $Count; $i++) {
          // split one depends into the library and the port name (/usr/ports/<category>/<port>)

          $DependsArray = explode(':', $depends[$i]);

          // now extract the port and category from this port name
          $CategoryPort = str_replace('/usr/ports/', '', $DependsArray[1]) ;
          $CategoryPortArray = explode('/', $CategoryPort);
          $DependsPortID = freshports_PortIDFromPortCategory($CategoryPortArray[0], $CategoryPortArray[1], $db);

          $HTML .= '<A HREF="/' . $CategoryPortArray[0] . '/' . $CategoryPortArray[1] . '/">' . $CategoryPortArray[0] . '/' . $CategoryPortArray[1]. '</a>';
          if ($i < $Count - 1) {
             $HTML .= ", ";
          }
      }
      $HTML .= "<br>\n";
   }

   if ($port->depends_run) {
      $HTML .= "<i>required to run:</i> ";
      // sometimes they have multiple spaces in the data...
      $temp = str_replace('  ', ' ', $port->depends_run);

      // split each depends up into different bits
      $depends = explode(' ', $temp);
      $Count = count($depends);
      for ($i = 0; $i < $Count; $i++) {
          // split one depends into the library and the port name (/usr/ports/<category>/<port>)

          $DependsArray = explode(':', $depends[$i]);

          // now extract the port and category from this port name
          $CategoryPort = str_replace('/usr/ports/', '', $DependsArray[1]) ;
          $CategoryPortArray = explode('/', $CategoryPort);
          $DependsPortID = freshports_PortIDFromPortCategory($CategoryPortArray[0], $CategoryPortArray[1], $db);

          $HTML .= '<A HREF="/' . $CategoryPortArray[0] . '/' . $CategoryPortArray[1] . '/">' . $CategoryPortArray[0] . '/' . $CategoryPortArray[1]. '</a>';
          if ($i < $Count - 1) {
             $HTML .= ", ";
          }
      }
      $HTML .= "<BR>\n";
   }

}

   if (!$HideDescription && ($ShowDescriptionLink == "Y" || $ShowEverything)) {
      // Long descripion
      $HTML .= '<A HREF="/' . $port->category . '/' . $port->port .'/">Description</a>';

      $HTML .= ' <b>:</b> ';
   }

   if ($ShowChangesLink == "Y" || $ShowEverything) {
      // changes
      $HTML .= '<a HREF="' . $freshports_CVS_URL . '/ports/' .
               $port->category . '/' .  $port->port . '/">CVSWeb</a>';
   }

   // download
   if ($port->status == "A" && ($ShowDownloadPortLink == "Y" || $ShowEverything)) {
      $HTML .= ' <b>:</b> ';
      $HTML .= '<a HREF="http://www.freebsd.org/cgi/pds.cgi?ports/' .
               $port->category . '/' .  $port->port . '">Sources</a>';
   }

   if ($port->package_exists == "Y" && ($ShowPackageLink == "Y" || $ShowEverything)) {
      // package
      $HTML .= ' <b>:</b> ';
      $HTML .= '<a HREF="ftp://ftp5.FreeBSD.org/pub/FreeBSD/FreeBSD-stable/packages/' .
               $port->category . '/' .  $port->port . "-" . $port->version . '.tgz">Package</a>';
   }

   if ($port->homepage && ($ShowHomepageLink == "Y" || $ShowEverything)) {
      $HTML .= ' <b>:</b> ';
      $HTML .= '<a HREF="' . $port->homepage . '">Homepage</a>';
   }

	$HTML .= ' <b>:</b> ';
	$HTML .= '<A HREF="' . $freshports_FTP_URL . $port->category . '/' . $port->port . '/">' . 'FTP</A>';

   $HTML .= "\n</DD>\n</DL>\n";

   return $HTML;
}

function freshports_CommitFilesLink($CommitID, $Category, $Port) {

#	echo "freshports_CommitFilesLink gets $CommitID, $Category, $Port<BR>";

	$HTML .= '<A HREF="/' . $Category . '/' . $Port . '/files.php?' . $CommitID . '">';
	$HTML .= freshports_Files_Icon();
	$HTML .= '</A>';

	return $HTML;
}

function freshports_CommitFilesLinkIDOnly($CommitID) {

#	echo "freshports_CommitFilesLink gets $CommitID, $Category, $Port<BR>";

	$HTML .= '<A HREF="/files.php?' . $CommitID . '">';
	$HTML .= freshports_Files_Icon();
	$HTML .= '</A>';

	return $HTML;
}

function freshportsNextRevisionNumber($RevisionNumber) {
	# very basic and not very widely tested function
	# for 3.45 return 3.44, for 1.809, return 1.808

#	print "starting with '$RevisionNumber'<BR>\n";

	if (gettype($RevisionNumber) != "string") {
		exit("freshportsNextRevisionNumber needs to have a string");
	}

	$LastPosition = strrpos($RevisionNumber, ".");
#	print "\$LastPosition= '$LastPosition'<BR>\n";

	$RevisionFraction = substr($RevisionNumber, $LastPosition + 1);
#	print $RevisionFraction . "<BR>\n";

	$PreviousRevision = $RevisionFraction - 1;

	return  substr($RevisionNumber, 0, $LastPosition) . '.' . $PreviousRevision;
}

	function GetPreviousRevision( $revision ) {
	    // if we find a dot, decrement the bit after the last dot
	    // hence, a cvs revision
	    // if no dot, treat it as an svn revision

    	$dotPos = strrpos( $revision, '.' );

    	if ( $dotPos === false ) {
    	    $prev = intval ( $revision ) - 1;
	    } else {
	        $beforeLastDot = substr( $revision, 0, $dotPos + 1 );

	        $afterDot = substr( $revision, $dotPos + 1 );
	        if ( $afterDot === false ) {
	            syslog( LOG_ERR, 'decimal not found in ' . $revision);
	            $prev = $revision;
            } else {
                // previous revision is before dot || (after dot - 1)
	            $prev = $beforeLastDot . ( intval( $afterDot ) - 1 );
            }
        }


        return $prev;
	}


