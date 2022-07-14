<?php
	#
	# $Id: customize.php,v 1.3 2008-10-18 23:35:20 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/getvalues.php');

	GLOBAL $User;

if (IsSet($_REQUEST['submit'])) $submit = $_REQUEST['submit'];
$visitor = pg_escape_string($_COOKIE[USER_COOKIE_NAME]);

// if we don't know who they are, we'll make sure they login first
if (!$visitor) {
	header("Location: login.php");  /* Redirect browser to PHP web site */
	exit;  /* Make sure that code below does not get executed when we redirect. */
}

if ($submit) {

	$Debug = 0;

	// process form

	$email         = pg_escape_string($_POST['email']);
	$Password      = $_POST['Password'];
	$Password1     = $_POST['Password1'];
	$Password2     = $_POST['Password2'];
	$numberofdays  = pg_escape_string($_POST['numberofdays']);
	$page_size     = pg_escape_string($_POST['page_size']);


	if (!is_numeric($numberofdays) || $numberofdays < 0 || $numberofdays > 9) {
		$numberofdays = 9;
	}


   if ($Debug) {
      while (list($name, $value) = each($_REQUEST)) {
         echo "$name = $value<br>\n";
      }
   }

   $OK = 1;

   $errors = "";

	if ($email && !freshports_IsEmailValid($email)) {
		$errors .= "That email address doesn't look right to me<BR>";
		$OK = 0;
	}
	
   if ($Password1 != $Password2) {
      $errors .= "The password was not confirmed.  It must be entered twice.<BR>";
      $OK = 0;
   }

   $AccountModified = 0;
   if ($OK) {
      if ($emailsitenotices_yn == "ON") {
          $emailsitenotices_yn_value = "t";
      } else {
          $emailsitenotices_yn_value = "f";
      }

	# if no errors so far, and all three password fields are populated
	if ($OK && $Password && $Password1 && $Password2) {
		$result = getLoginDetails($db, LOGIN_QUERY, $User->name, $Password);
		# there must be only 1 row in there.
		if ($Debug) echo 'number of rows is ' . pg_num_rows($result) . '<br>';
		if (pg_num_rows($result) != 1) {
			$errors .= 'That is NOT your current password.<br>';
			$OK = 0;
		}
	}

	if ($OK) {
      // get the existing email in case we need to reset the bounce count
      $sql = "select email from users where cookie = '$visitor'";
      $result = pg_exec($db, $sql);
      if ($result) {
         $myrow = pg_fetch_array ($result, 0);

         $sql = "update users set ";
         if (!empty($email)) {
          $sql .= "email			= '$email', ";
         }
         $sql .= "emailsitenotices_yn = '$emailsitenotices_yn_value',";
         $sql .= "number_of_days      = $numberofdays ";

         // if they are changing the email, reset the bouncecount.
         if (!empty($email) && $myrow["email"] != $email) {
            $sql .= ", emailbouncecount = 0 ";
         }

         if ($Password1 != '') {
				$sql .= ", password_hash = crypt('" . pg_escape_string($Password1) . "'";
				$sql .= ", gen_salt('" . PW_HASH_METHOD . "', " . PW_HASH_COST ."))";
         }

         $sql .= " where cookie = '$visitor'";

         if ($Debug) {
            echo $sql;
         }

         $result = pg_exec($db, $sql);
         if ($result) {
			$AccountModified = 1;
         }
      }
      
      } # $OK

      if ($AccountModified == 1) {
         if ($Debug) {
            echo "I would have taken you to '/' now, but debugging is on<br>\n";
         } else {
            header("Location: /");
            exit;  /* Make sure that code below does not get executed when we redirect. */
         }
      } else {
         $errors .= 'Something went terribly wrong there.<br>';
         $errors .= $sql . "<br>\n";
         $errors .= pg_errormessage();
      }
   }
} else {
}

   freshports_Start("Customize User Account",
               "freshports - new ports, applications",
               "FreeBSD, index, applications, ports");
?>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD VALIGN="top" width="100%">
<TABLE width="100%" border="0">
  <TR>
    <TD height="20"><?php

if (!$submit) {
	include( $_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");
    $numberofdays = $NumberOfDays;
}

if ($errors) {
echo '<TABLE CELLPADDING="1" BORDER="0" BGCOLOR="#AD0040" width="100%">
<TR>
<TD>
<TABLE width="100%" BORDER="0" CELLPADDING="1">
<TR BGCOLOR="#AD0040"><TD><b><font color="#ffffff" size=+0>Access Code Failed!</font></b></TD>
</TR>
<TR BGCOLOR="#ffffff">
<TD>
  <TABLE width="100%" CELLPADDING="3" BORDER="0">
  <TR VALIGN=top>
   <TD><img src="/images/warning.gif"></TD>
   <TD width="100%">
  <p>Some errors have occurred which must be corrected before your login can be created.</p>';

echo $errors;

echo '<p>If you need help, please contact us via email. </p>
 </TD>
 </TR>
 </TABLE>
</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
<br>';
}
if ($AccountModified) {
   echo "Your account details were successfully updated.";
} else {
  // provide default values for an empy form.
//  $daystoshow = 20;
//  $maxarticles = 40;
//  $daysnew = 20;
//   require($_SERVER['DOCUMENT_ROOT'] . "/../include/commonphp3.inc");
//	echo $DaysToShow,  '= days to show';

echo '<TABLE CELLPADDING="1" BORDER="0" BGCOLOR="#AD0040" WIDTH="100%">
<TR>
<TD VALIGN="top">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="1">
<TR>
<TD BGCOLOR="#AD0040" HEIGHT="29" COLSPAN="1"><FONT COLOR="#FFFFFF"><BIG><BIG>Customize</BIG></BIG></FONT></TD>
</TR>
<TR BGCOLOR="#ffffff">
<TD>';

echo 'If you wish to change your password, supply your new password twice.  Otherwise, leave it blank.<br>';
include($_SERVER['DOCUMENT_ROOT'] . "/../include/getvalues.php");

$Customize=1;
include($_SERVER['DOCUMENT_ROOT'] . "/../include/new-user.php");

echo "</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>";
}

?></TD>
</TABLE>
</TD>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
   <?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/side-bars.php") ?>
 </TD>
</TR>
</TABLE>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/footer.php") ?>
</TD></TR>
</TABLE>

</body>
</html>
