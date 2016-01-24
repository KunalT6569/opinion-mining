<?php
	header('Content-Type: text/html; charset=utf-8');
include_once("mfunctions.php");
if(isset($_GET['id'])){
$tab=$_GET['id'];
gen_report($tab);}
if (isset($_POST['st'])) {
sentisingle($_POST['st']);}

// close mysql connection  
mysqli_close($mysqli); 
?>
<br>
<h1>Thanks</h1>

<h2>how it works?</h2>
<p>Simply enter any english phrase and find out  (purely english)</p>
<form action="" method=post>
	<input name=st type=text placeholder="What a rubbish weather today?"><br>
	<input type="submit" value="find out polarity"></form>