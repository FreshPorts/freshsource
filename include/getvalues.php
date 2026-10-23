<?php
	#
	# $Id: getvalues.php,v 1.4 2007-10-08 23:08:14 dan Exp $
	#
	# Copyright (c) 1998-2026 Dan Langille
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/../classes/user.php');
	require_once("/usr/local/www/freshports/include/constants.php");
	require_once('/usr/local/www/freshports/include/freshports.php');

GLOBAL $User;
$User = new User($db);

$Debug = 0;

$FormatDateDefault	= "%W, %b %e";
$FormatTimeDefault	= "%H:%i";
$DaysMarkedAsNewDefault	= 10;


// there are only a few places we want to show the last change.
// such places set $GlobalHideLastChange == "Y"   
$GlobalHideLastChange   = "Y";

$DaysToShow  = 20;
$MaxArticles = 40;
$DaysNew     = 10;

GLOBAL $MaxNumberOfPorts;
GLOBAL $ShowShortDescription;
GLOBAL $ShowMaintainedBy;
GLOBAL $ShowLastChange;
GLOBAL $ShowDescriptionLink;
GLOBAL $ShowChangesLink;
GLOBAL $ShowDownloadPortLink;
GLOBAL $ShowPackageLink;
GLOBAL $ShowHomepageLink;
GLOBAL $FormatDate;
GLOBAL $FormatTime;
GLOBAL $DaysMarkedAsNew;
GLOBAL $EmailBounceCount;
GLOBAL $CVSTimeAdjustment;
GLOBAL $UserName;
GLOBAL $UserID;
GLOBAL $visitor;
GLOBAL $db;
GLOBAL $WatchListID;
GLOBAL $NumberOfDays;

$MaxNumberOfPorts	= 100;
$ShowShortDescription	= "Y";
$ShowMaintainedBy	= "Y";
$ShowLastChange		= "Y";
$ShowDescriptionLink	= "Y";
$ShowChangesLink	= "Y";
$ShowDownloadPortLink	= "Y";
$ShowPackageLink	= "Y";
$ShowHomepageLink	= "Y";
$FormatDate		= $FormatDateDefault;
$FormatTime		= $FormatTimeDefault;
$DaysMarkedAsNew	= $DaysMarkedAsNewDefault;
$EmailBounceCount	= 0;
$CVSTimeAdjustment	= -10800;	# this is number of seconds the web server is relative to the cvs server.
									# a value of -10800 means the web server is three hours east of the cvs server.
									# we can override that for a particular user.

$LocalTimeAdjustment	= 0;		# This can be used to display the time the webpage was loaded.
$WatchListID		= 0;
$NumberOfDays		= 9;

#
# flags for showing various port parts.
#
$ShowEverything		= 0;
$ShowPortCreationDate	= 0;

$UserName		= "";
$UserID			= "";

// This is used to determine whether or not the cach can be used.
$DefaultMaxArticles = $MaxArticles;

if (IsSet($_COOKIE[USER_COOKIE_NAME])) $visitor = $_COOKIE[USER_COOKIE_NAME];
if (!empty($visitor)) {
	if ($User->FetchByCookie($visitor) != 1) {
		if ($Debug) echo ' no cookie found for that person ';
		# we were given a cookie which didn't refer to a cookie we found.
		freshports_CookieClear();
		unset($visitor);
	} else {
		if ($User->status == $UserStatusDisabled) {
			#
			# the account has become disabled after they have
			# logged in.  Let's just leave them a simple
			# message for them to contact us.
			#

			freshports_CookieClear();
			echo 'Database error: Account details corrupted.  Please contact ' . PROBLEM_SOLVER_EMAIL_ADDRESS . '.<BR>';
			echo 'You have been logged out.';
			exit;
		}

		$EmailBounceCount = $User->emailbouncecount;

		// record their last login
		$sql = 'update users set lastlogin = current_timestamp where id = $1';
		$result = pg_query_params($db, $sql, array($User->id));
	}
	if ($Debug) {
		echo "UserName = $User->name\n<br>UserID=$User->id<br>\n";
	}
} else {
	if ($Debug) echo "we have no \$visitor\n<BR>";
}
?>
