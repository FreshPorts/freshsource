<?
	# $Id: login.php,v 1.2 2005-09-06 00:14:12 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST"  name=f>
      <input type="hidden" name="custom_settings" value="1"><input type="hidden" name="LOGIN" value="1">
      <p>User ID:<br>
      <input SIZE="15" NAME="UserID" value="<?php echo htmlentities($UserID ?? '') ?>"></p>
      <p>Password:<br>
      <input TYPE="PASSWORD" NAME="Password" VALUE = "<?php echo htmlentities($Password ?? '') ?>" size="20"></p>
      <p><input TYPE="submit" VALUE="Login" name=submit> &nbsp;&nbsp;&nbsp;&nbsp; <input TYPE="reset" VALUE="reset form">
</form>
