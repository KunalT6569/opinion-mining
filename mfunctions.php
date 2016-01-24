<?php
$mysqli = new mysqli("localhost", "root", "", "minergod");
$access_token="1615429865383287|nJkkwVhBA9Yf_uUXhw18GYYgSJU";
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
require 'Mine/Autoloader.php';
Mine\Autoloader::register();
$sentiment = new \Mine\Sentiment();	

function sentisingle($string)
{
	global $sentiment;
	$scores = $sentiment->score($string);
	$class = $sentiment->categorise($string);

	// output:
	echo "<br>String: $string\n";
	echo "Dominant: $class, scores: ";
	print_r($scores);
}
function findsenti($string){
	global $sentiment;
	//$scores = $sentiment->score($string);
	$class = $sentiment->categorise($string);
switch ($class)
{
	case 'pos' : return 3; break;
	case 'neg' : return 1; break;
	default : return 2; 
}
}

function gen_report($table)

{
	global $mysqli;
$r=mysqli_query($mysqli,"select report from `$table` where 1");
$c=0;$count=0;$p=0;$n=0;$neg=0;
while( $row= mysqli_fetch_row($r))
{
	$c+=$row[0];
	switch ($row[0])
	{
		case 1: $neg++;break;
		case 2: $n++;break;
		default: $p++;
	}
	$count++;

}

echo $c.'<br>----- = '.$c/$count.'<br>'.$count.'<br>';

echo 'positive: '.$p.'<br>neutral: '.$n.'<br>negative'.$neg;

echo '<br>total set:'.$count.'<br>%age positive<b>Weighted</b>:'.$p*100/($p+$neg);
}
function istable($table) {
	global $mysqli;
	mysqli_real_query($mysqli,"SELECT 1 FROM `$table` LIMIT 1 ");
  mysqli_use_result($mysqli);
if (mysqli_errno($mysqli) == 1146)
 return false; else return true;
}

function nexturl($pageid,$d)
{
	$dat=file_get_contents($d);
	$data=json_decode($dat,true,20,JSON_BIGINT_AS_STRING);
	//print_r($data);
	global $mysqli;
	foreach($data['data'] as $post)
  {$date_formal = strtotime($post['created_time']);
  
  if(!isset($post['message'])){ $post['message']=''; $inclination=2;} else 
  $inclination=findsenti($post['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`time`,`report`) VALUES (\"".$post['id']."\",\"".$post['message']."\",\"".$date_formal."\",$inclination)";
  
  mysqli_query($mysqli,$qr);
  }
	
}


function object_to_array($data)
{
    if (is_array($data) || is_object($data))
    {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}

function createtable($table){
	global $mysqli;
$sql = "CREATE TABLE `$table` (
`id` int(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `post_id` varchar(30) UNIQUE KEY NOT NULL,
  `text` varchar(2499) NOT NULL,
  `parent` varchar(30) DEFAULT NULL,
  `time` varchar(12) NOT NULL,
  `report` int(1) NOT NULL
)";
mysqli_query($mysqli, $sql);
}
function getmaxid($table)
{
	global $mysqli;
	$result = mysqli_query($mysqli, "SELECT * FROM `$table` ORDER BY `id` DESC LIMIT 1");
$row = mysqli_fetch_array($result);
return $row['id'];
}
function docomments($pageid,$count)
{
	global $access_token,$mysqli;
	$result=mysqli_query($mysqli,"select post_id from `$pageid`");
	$postlist;
while ($row = mysqli_fetch_array($result)){
$postlist[] = $row;}
	foreach ($postlist as $postidarr){
	$postid=$postidarr[0];
	$url = "https://graph.facebook.com/".$postid."/comments?access_token=".$access_token;
	$raw=file_get_contents($url);
	$data=json_decode($raw,true,20,JSON_BIGINT_AS_STRING);
	//echo formalizenext($data['paging']['next']);exit;
	foreach($data['data'] as $comment)
  {$date_formal = strtotime($comment['created_time']);
  
  if(!isset($comment['message'])){ $comment['message']=''; $inclination=2;} else 
  $inclination=findsenti($comment['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`parent`,`time`,`report`) VALUES (\"".$comment['id']."\",\"".$comment['message']."\",\"".$postid."\",\"".$date_formal."\",$inclination)";
  
  mysqli_query($mysqli,$qr);
	}
	$newpath=formalizenext($data['paging']['next']);
	$raw4=file_get_contents($newpath);
	$data4=json_decode($raw4,true,20,JSON_BIGINT_AS_STRING);
	//echo formalizenext($data['paging']['next']);exit;
	foreach($data4['data'] as $comment)
  {$date_formal = strtotime($comment['created_time']);
  
  if(!isset($comment['message'])){ $comment['message']=''; $inclination=2;} else 
  $inclination=findsenti($comment['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`parent`,`time`,`report`) VALUES (\"".$comment['id']."\",\"".$comment['message']."\",\"".$postid."\",\"".$date_formal."\",$inclination)";
  
  mysqli_query($mysqli,$qr);
	}
	}
}

function adddata($pageid,$limit)
{
	global $access_token,$mysqli;
	$type="posts";
	
	$url = "https://graph.facebook.com/".$pageid."/".$type."?access_token=".$access_token.'&limit='.$limit;
	$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_CAINFO,dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fb_ca_chain_bundle.crt');
$raw=curl_exec($curl);
//echo curl_error($curl);
curl_close($curl);
	//$raw=file_get_contents($url);
	$data=json_decode($raw,true,20,JSON_BIGINT_AS_STRING);
	//print_r($data);
	//echo formalizenext($data['paging']['next']);exit;
	foreach($data['data'] as $comment)
  {
  $date_formal = strtotime($comment['created_time']);
  
  if(!isset($comment['message'])){ $comment['message']=''; $inclination=2;} else 
  $inclination=findsenti($comment['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`parent`,`time`,`report`) VALUES (\"".$comment['id']."\",\"".$comment['message']."\",\"\",\"".$date_formal."\",$inclination)";
    mysqli_query($mysqli,$qr);
	}
}

function addlv2data($pageid,$limit)
{
	global $access_token,$mysqli;
	$type="comments";
	$result=mysqli_query($mysqli,"select post_id from `$pageid`");
	$postlist;
while ($row = mysqli_fetch_array($result)){
$postlist[] = $row;}
	foreach ($postlist as $postidarr){
	$postid=$postidarr[0];
	$url = "https://graph.facebook.com/".$postid."/".$type."?access_token=".$access_token.'&limit='.$limit;
	$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_CAINFO,dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fb_ca_chain_bundle.crt');
$raw=curl_exec($curl);
//echo curl_error($curl);
curl_close($curl);
	$data=json_decode($raw,true,20,JSON_BIGINT_AS_STRING);
	//echo formalizenext($data['paging']['next']);exit;
	foreach($data['data'] as $comment)
  {$date_formal = strtotime($comment['created_time']);
  if(!isset($comment['message'])){ $comment['message']=''; $inclination=2;} else 
  $inclination=findsenti($comment['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`parent`,`time`,`report`) VALUES (\"".$comment['id']."\",\"".$comment['message']."\",\"".$postid."\",\"".$date_formal."\",$inclination)";
    mysqli_query($mysqli,$qr);
	}
	}
}

function formalizenext($str)
{
	$re = "/&limit=[0-9].&/"; 
	$rep = "&limit=85&"; 
	
	return preg_replace($re, $rep,$str);
}
?>