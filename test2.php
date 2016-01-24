<pre><?php
//require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ .'/mfunctions.php';
header('Content-Type: text/html; charset=utf-8');
//session_start();

$requestedpageid=isset($_GET['tobe']) ? $_GET['tobe'] : "shakira";
/*use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;
$fb = new Facebook\Facebook([
  'app_id' => '1615429865383287',
  'app_secret' => '0b012e983bc9689d261315b017f97275',
  'default_graph_version' => 'v2.4',
  ]);

/*$fb->setDefaultAccessToken('1615429865383287|nJkkwVhBA9Yf_uUXhw18GYYgSJU');

try {
  // Returns a `Facebook\FacebookResponse` object
	

  $response = $fb->get('/me?fields=id', '1615429865383287|nJkkwVhBA9Yf_uUXhw18GYYgSJU');
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
$graphObject = $response->getGraphObject();
$user = $response->getGraphUser();
*/
//echo 'Name: ' . $user['name'];

$time_pre = microtime(true);
  //$request = $fb->get('/'.$requestedpageid.'?posts&limit=99','1615429865383287|nJkkwVhBA9Yf_uUXhw18GYYgSJU');

  //$response = $request->execute();
  //$graphObject = $request->getGraphObject();
  //$graphArray=object_to_array( $graphObject);
  //$meta=$graphObject->items['posts']->metaData['paging']['next'];
 //print_r($graphArray);
 $pageid=$requestedpageid;
 //var_dump($graphObject);exit;
  //echo '<table border=1>';
  echo '<h1><a href="report.php?id='.$pageid.'"> Click for reports</a></h1>';
  
 if(!istable($pageid)) createtable($pageid);
 else die("local data loaded");
  /*foreach($graphArray['posts'] as $post)
  {$date_formal = new DateTime($post['created_time']['date']);
  if(!isset($post['message'])){ $post['message']=''; $inclination=2;} else 
  $inclination=findsenti($post['message']);
  $qr="INSERT INTO `$pageid` (`post_id`,`text`,`time`,`report`) VALUES (\"".$post['id']."\",\"".$post['message']."\",\"".$date_formal->gettimestamp()."\",$inclination)";
  //echo $qr=mysqli_real_escape_string($mysqli,$qr);
  //echo'<br>';
  //exit;
  mysqli_query($mysqli,$qr);
	//echo '<tr><td>'.$post['id'].'</td><td>'.$post['message'].'</td><td>'.$inclination.'</td><td>'.$date_formal->gettimestamp().'</td></tr>';
  }
    //echo '</table>';
	
	nexturl($pageid,$meta);
	$count=getmaxid ($pageid);
	docomments($pageid,$count);

	//header('Location: ../report.php?id='.$pageid);
	*/
	adddata($pageid,51);
	addlv2data($pageid,100);
	
$time_post = microtime(true);
$exec_time = $time_post - $time_pre;
echo "time taken=".$exec_time;
  //$userNode = $response->getGraphUser();
//var_dump($response);
//echo 'Logged in as ' . $userNode->getName();
?>