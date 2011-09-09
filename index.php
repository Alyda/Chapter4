<?php

if (get_magic_quotes_gpc()) {
	
	function stripslashes_deep($value) {
		$value = is_array($value) ?
		array_map('stripslashes_deep', $value) :
		stripslashes($value);
		return $value;
	}

	$_POST = array_map('stripslashes_deep', $_POST);
	$_GET = array_map('stripslashes_deep', $_GET);
	$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
	$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}

if (isset($_GET['addjoke'])) {
	include 'form.html.php';
	exit();
}

$link = mysqli_connect('localhost', 'root', 'Smile :)');

if (!$link) {
	$output = 'Unable to connect to the database server.';
	include 'output.html.php';
	exit();
}

if (!mysqli_set_charset($link, 'utf8')) {
	$output = 'Unable to set database connection encoding.';
	include 'error.html.php';
	exit();
}

if (!mysqli_select_db($link, 'ijdb')) {
	$output = 'Unable to locate the joke database.';
	include 'error.html.php';
	exit();
}

if (isset($_POST['joketext'])) {
	$joketext = mysqli_real_escape_string($link, $_POST['joketext']);
	$sql = 'INSERT INTO joke SET
	joketext="' . $_POST['joketext'] . '",
	jokedate=CURDATE()';

	if (!mysqli_query($link, $sql))	{
		$error = 'Error adding submitted joke: ' . mysqli_error($link);
		include 'error.html.php';	
		exit();
	}

	header('Location: .');
	exit();
}


if (isset($_GET['deletejoke'])) {
	$id = mysqli_real_escape_string($link, $_POST['id']);
	$sql = "DELETE FROM joke WHERE id='$id'";

	if (!mysqli_query($link, $sql)) {
		$error = 'Error deleting joke: ' . mysqli_error($link);	
		include 'error.html.php';
		exit();
	}

	header('Location: .');
	exit();
}

$result = mysqli_query($link, 'SELECT id, joketext FROM joke');

if (!$result) {
	$error = 'Error fetching jokes: ' . mysqli_error($link);
	include 'error.html.php';
	exit ();
}

while ($row = mysqli_fetch_array($result)) {
	$jokes[] = array ('id' => $row['id'],'text' => $row['joketext']);
}

include 'jokes.html.php';

?>
