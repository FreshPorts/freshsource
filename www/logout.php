<?php
	# $Id: logout.php,v 1.1 2002-07-27 20:11:20 dan Exp $
	#
	# Copyright (c) 1998-2001 DVL Software Limited

	require($_SERVER['DOCUMENT_ROOT'] . "/../include/common.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/freshports.php");
	require($_SERVER['DOCUMENT_ROOT'] . "/../include/databaselogin.php");

	freshports_CookieClear();

	header("Location: http://" . $_SERVER["HTTP_HOST"] . "/");  /* Redirect browser to PHP web site */
	exit;  /* Make sure that code below does not get executed when we redirect. */
?>

<html>

<head>
<title></title>
</head

<body>
</body>
</html>
