<?php
	#
	# $Id: welcome.php,v 1.2 2006-12-17 12:06:19 dan Exp $
	#
	# Copyright (c) 1998-2003 DVL Software Limited
	#

	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/common.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/freshports.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/databaselogin.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/getvalues.php');
	require_once('/usr/local/www/freshports/include/freshports.php');

	$Title = 'New User';
	freshports_Start($Title,
					$Title,
					'FreeBSD, index, applications, ports');

?>
	<?php echo freshports_MainTable(); ?>

	<tr><td class="content">

	<?php echo freshports_MainContentTable(); ?>
  <tr>
	<?php echo freshports_PageBannerText("Account created"); ?>
  </tr>
	<TR>
	<td>
	<P>
	Your account has been created.
	</P>

	<P>
	You should soon receive an email at the mail address you supplied.
	It will contain instructions to enable your account.

	<P>
	If you do not receive that email, please attempt to login and you
	will have an opportunity to resend the notification.
	</P>
</td></TR>
</table>
</td>

  <td class="sidebar">
   <?php include($_SERVER['DOCUMENT_ROOT'] . "/../include/side-bars.php") ?>
  </td>
</TR>
</table>

<?php
echo freshports_ShowFooter();
?>

</BODY>
</HTML>
