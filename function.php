<?php
function getResults($url)
{
$initialize = curl_init();//Initialization
curl_setopt($initialize, CURLOPT_URL,$url);//Setting the URL
curl_setopt($initialize, CURLOPT_USERAGENT, "anirbanbhowmik94");//Setting username
curl_setopt($initialize, CURLOPT_HTTPHEADER, array( 'Accept: application/json'));//Fetching Json data
curl_setopt($initialize, CURLOPT_RETURNTRANSFER, true);//Returning response
$result=curl_exec($initialize);//Executing
curl_close($initialize);//Closing
$new_result=json_decode($result,true);
return $new_result;//Returning array
}
?>
