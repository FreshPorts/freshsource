<?php
	# $Id: new-user.php,v 1.7 2008-10-18 23:35:20 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/include/databaselogin.php");

	$origin = $_GET["origin"];
	$submit = $_POST["submit"];

if ($submit) {

	// process form

	/*
	while (list($name, $value) = each($HTTP_POST_VARS)) {
		echo "$name = $value<BR>\n";
	}
	*/


	$OK = 1;

	$errors = "";

	$UserLogin				= $_REQUEST["UserLogin"];
	$email					= $_REQUEST["email"];
	$Password1				= $_REQUEST["Password1"];
	$Password2				= $_REQUEST["Password2"];
	$numberofdays			= $_REQUEST["numberofdays"];

	if ($UserLogin == '') {
		$errors .= "Please enter a user id.<BR>";
		$OK = 0;
	}

	if ($UserLogin != trim($UserLogin, "\t\n\r\x0B")) {
		syslog(LOG_ERR, 'FreshSource warning (odd UserLogin): '. $UserLogin);
		$errors .= "Please, just use plain text for your user id.  This event has been logged.<br>";
		$OK = 0;
	}

	if ($email != trim($email, "\t\n\r\x0B")) {
		syslog(LOG_ERR, 'FreshSource warning (odd email): '. $email);
		$errors .= "Please, just use plain text for your email.  This event has been logged.<br>";
		$OK = 0;
	}

	if (!freshports_IsEmailValid($email)) {
		$errors .= "That email address doesn't look right to me<BR>";
		$OK = 0;
	}

	if ($Password1 != $Password2) {
		$errors .= "The password was not confirmed.  It must be entered twice.<BR>";
		$OK = 0;
	} else {
		if ($Password1 == '') {
			$errors .= 'A password must be supplied<BR>';
			$OK = 0;
		}
	}

	if (is_numeric($numberofdays)) {
		if ($numberofdays < 0 || $numberofdays > 9) {
			$numberofdays = 0;
		}
	} else {
		$numberofdays = 0;
	}

	$UserCreated = 0;
	if ($OK) {
		$Cookie = UserToCookie($UserLogin);
//		echo "checking database\n";

		// test for existance of user id

		$sql = "select * from users where cookie = '$Cookie'";

		$result = pg_exec($db, $sql) or die('query failed');

		// create user id if not found
		if(!pg_numrows($result)) {
 //			echo "confirmed: user id is new\n";

			# no need to validate that value as it's not put directly into the db.
			if ($emailsitenotices_yn == "ON") {
				$emailsitenotices_yn_value = "Y";
			} else {
				$emailsitenotices_yn_value = "N";
			}

			$UserID = freshports_GetNextValue($Sequence_User_ID, $db);
			if (IsSet($UserID)) {			
				$sql = "insert into users (id, name, password_hash, cookie, email, " . 
						"watch_notice_id, emailsitenotices_yn, type, ip_address, number_of_days) values (";
				$sql .= AddSlashes($UserID) . ", '" . AddSlashes($UserLogin) . "', 
                        crypt('" . AddSlashes($Password1) . "' , gen_salt('md5')), '" . AddSlashes($Cookie) . "', '" . 
						AddSlashes($email) . "', '1', 'N', 'U', '" . $_SERVER["REMOTE_ADDR"] . "', " .
						AddSlashes($numberofdays) . ')';

				syslog(LOG_ERR, 'FreshSource new user: '. $sql);
				$errors .= "<BR>sql=" . $sql;

				$result = pg_exec($db, $sql);
				if ($result) {
					$UserCreated = 1;

					# if the mail out fails, we aren't handling it properly here.
					# we will.  eventually.
					#
					freshports_UserSendToken($UserID, $db);
				} else {
					$errors .= "OUCH! I couldn't add you to the database\n";
					$OK = 0;
				}
			} else {
				$errors .= "OUCH! I couldn't assign you a new UserID\n";
				$OK = 0;
			}

	    } else {
			$errors .= 'That User ID is already in use.  Please select a different  User ID.<BR>';
    	}
	}

	if ($UserCreated) {
		header("Location: welcome.php?origin=" . $origin);  /* Redirect browser to PHP web site */
		exit;  /* Make sure that code below does not get executed when we redirect. */
	}
} else {
	// not submit

	// we can't do this if we are submitting because it overwrites the incoming values
	require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");
	$emailsitenotices_yn = "ON";
}

   freshports_Start("New User",
               "freshports - new ports, applications",
               "FreeBSD, index, applications, ports");
?>

<SCRIPT TYPE="text/javascript">
<!--
function setfocus() { document.f.UserLogin.focus(); }
// -->
</SCRIPT>

<TABLE WIDTH="<?php echo $TableWidth; ?>" BORDER="0" ALIGN="center">
<TR><TD VALIGN="top" WIDTH="100%">
<?php
if ($errors) {
echo '<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0 BGCOLOR="#AD0040" WIDTH=100%>
<TR>
<TD>
<TABLE WIDTH=100% BORDER=0 CELLPADDING=1>
<TR BGCOLOR="#AD0040"><TD><B><FONT color="#ffffff" size=+0>Access Code Failed!</FONT></B></TD>
</TR>
<TR BGCOLOR="#ffffff">
<TD>
  <TABLE WIDTH=100% CELLPADDING=3 CELLSPACING=0 BORDER=0>
  <TR VALIGN=top>
   <TD><IMG SRC="/images/warning.gif"></TD>
   <TD WIDTH=100%>
  <p>Some errors have occurred which must be corrected before your login can be created.</p>';

/*
  while (list($name, $value) = each($HTTP_POST_VARS)) {
    echo "$name = $value<BR>\n";
  }
*/
echo $errors;

echo '<p>If you need help, please post a message on the forum. </p>
 </TD>
 </TR>
 </TABLE>
</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
<BR>';
}

if (!$submit && !$errors) {
  // provide default values for an empy form.
  require($_SERVER['DOCUMENT_ROOT'] . "/include/getvalues.php");
}

?>

<TABLE CELLSPACING="0" BORDER="0" WIDTH="100%" CELLPADDING="5">
      <TR>
		<?php freshports_PageBannerText("New User Details"); ?>
      </TR>
      <TR>
        <TD>

<P><BIG><BIG>NOTE:</BIG>You must supply a valid email address.<BR>Instructions to enable your account 
will be emailed to you at that address.  If you have are using a spam filter, please allow all
mail from <CODE CLASS="code">unixathome.org</CODE> and <CODE CLASS="code">freshports.org</CODE>.</BIG></P>
<P>Your browser must allow cookies for the login to work.</P>
<P>&nbsp;</P>

<?php include($_SERVER['DOCUMENT_ROOT'] . "/include/new-user.php"); ?>

    </TD>
  </TR>
</TABLE>
</TD>
  <TD VALIGN="top" WIDTH="*" ALIGN="center">
   <?php include($_SERVER['DOCUMENT_ROOT'] . "/include/side-bars.php") ?>
 </TD>
</TR>
</TABLE>
<?php include("./include/footer.php") ?>
</BODY>
</HTML>
