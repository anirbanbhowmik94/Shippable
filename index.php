<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
<title>GitHub issues search</title>
<!-- CSS -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
<link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700' rel='stylesheet' type='text/css'>
</head>
<!-- Body starts here -->
<body>
<!-- Navigation Bar -->
<nav class="light-blue lighten-1" role="navigation">
<div class="nav-wrapper container"><a id="logo-container" href="https://github.com" class="brand-logo">GitHub</a>
<ul class="right hide-on-med-and-down">
<li><a href="https://github.com/anirbanbhowmik94/Shippable" target="_blank">View Source</a></li>
</ul>
</div>
</nav>
<!-- div for Highcharts graphical data display --> 
<div id="container"></div>
<div style="width:80%; margin-left:10%;">
<form action="" method="POST">
<input type="text" name="url" placeholder="GitHub Repository URL" size="60">
<button class="btn waves-effect waves-light large" type="submit" name="submit"><i class="material-icons">search</i></button>
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<!-- PHP Script starts here -->
<?php
include_once "function.php";//Importing supporting PHP file
$issues=0;
$pulls=0;
$pullsday=0;
$pullsweek=0;
$openissuescount=0;
$lastdaycount=0;
$sevendaycount=0;
$flag=0;
$pg=1;
if(isset($_POST['submit'])){
$url = $_POST['url'];//Getting URL
$array =  explode('/',$url);
//Checking for correct URL
if(strcmp($array[0],"https:")||strcmp($array[1],"")||strcmp($array[2],"github.com")||empty($array[3])||empty($array[4]))
{
die("</br>Please check the URL and try again. If the problem persists, kindly retry after a while.<br>");
}
//Required URL for fetching data for Total open issues
$url = "https://api.github.com/repos/".$array[3]."/".$array[4];
$result = getResults($url);
$issues = $result["open_issues_count"];
while($flag!=1){
$url = "https://api.github.com/repos/".$array[3]."/".$array[4]."/pulls?page=".$pg."&state=open&per_page=100";
$result = getResults($url);
if(count($result)<100)
{
$pulls=$pulls+count($result);
$flag=1;
}
else
{
$pulls=$pulls+100;
$pg++;
}
}
$openissuescount=$issues-$pulls;
//Since open issues consist of both issues and pull requests
//Required Time for fetching data for Number of issues opened during the last 24 hours
$timelast24hr = date('Y-m-d\TH:i:s.Z\Z', strtotime("-1 day", time()));
$url = "https://api.github.com/repos/".$array[3]."/".$array[4]."/issues?state=open&per_page=100&since=".$timelast24hr;     
$result = getResults($url);
if(count($result)<100)
{
for($i=0;$i<count($result);$i++)
{
$created=date('Y-m-d\TH:i:s.Z\Z', strtotime($result[$i]["created_at"]));
if($created>$timelast24hr){
$lastdaycount+=1;
if(isset($result[$i]["pull_request"]))
$pullsday+=1;
}
}
}
//for more than 100 issues
else
{
$j=1;
while(count($result)!=0)
{
$url = "https://api.github.com/repos/".$array[3]."/".$array[4]."/issues?state=open&per_page=100&page=".$j."&since=".$timelast24hr;
$result = getResults($url);
for($i=0;$i<count($result);$i++)
{
$created=date('Y-m-d\TH:i:s.Z\Z', strtotime($result[$i]["created_at"]));
if($created>$timelast24hr)
{
$lastdaycount+=1;
if(isset($result[$i]["pull_request"]))
$pullsday+=1;
}
}
$j++;
}
}
$sevendayago = date('Y-m-d\TH:i:s.Z\Z', strtotime("-7 day", time()));
$url = "https://api.github.com/repos/".$array[3]."/".$array[4]."/issues?state=open&per_page=100&since=".$sevendayago;     
$result = getResults($url);
if(count($result)<100)
{
for($i=0;$i<count($result);$i++)
{
$created=date('Y-m-d\TH:i:s.Z\Z', strtotime($result[$i]["created_at"]));
if($created>$sevendayago){
$sevendaycount+=1;
if(isset($result[$i]["pull_request"]))
$pullsweek+=1;
}
}
}
//for more than 100 issues
else
{
$j=1;
while(count($result)!=0)
{
$url = "https://api.github.com/repos/".$array[3]."/".$array[4]."/issues?state=open&per_page=100&page=".$j."&since=".$sevendayago;
$result = getResults($url);
for($i=0;$i<count($result);$i++)
{
$created=date('Y-m-d\TH:i:s.Z\Z', strtotime($result[$i]["created_at"]));
if($created>$sevendayago){
$sevendaycount+=1;
if(isset($result[$i]["pull_request"]))
$pullsweek+=1;
}
}
$j++;
}
}
}
?>

<table class="hoverable">
<thead>
<tr>
<th data-field="id">Category</th>
<th data-field="name">Number of Issues</th>
</tr>
</thead>
<tbody>
<tr>
<td>Total number of open issues</td>
<td><?php echo $openissuescount; ?></td>
</tr>
<tr>
<td>Number of open issues that were opened in the last 24 hours</td>
<td><?php echo $lastdaycount-$pullsday;?></td>
</tr>
<tr>
<td>Number of open issues that were opened more than 24 hours ago but less than 7 days ago</td>
<td><?php echo $sevendaycount-$lastdaycount-$pullsweek+$pullsday;?></td>
</tr>
<tr>
<td>Number of open issues that were opened more than 7 days ago</td>
<td><?php echo $openissuescount-$sevendaycount+$pullsweek;?></td>
</tr>
</tbody>
</table>
</div>
<!-- Footer starts here -->
<footer class="page-footer blue">
<div class="container">
<div class="row">
</div>
</div>
<div class="footer-copyright center">
<div class="container">2015 <a class="white-text text-darken-3" href="">Anirban Bhowmik</a>
</div>
</div>
</footer>
<!-- Footer ends here -->
<!-- Highcharts Script begins here -->
<script>
$(function () { 
    var total= <?php echo $openissuescount;?>;
    var lst24= <?php echo $lastdaycount-$pullsday; ?>;
    var lst24to7= <?php echo $sevendaycount-$lastdaycount-$pullsweek+$pullsday;?>;
    var before7= <?php echo $openissuescount-$sevendaycount+$pullsweek;?>;
    var me
    $('#container').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Analysis Page'
        },
        xAxis: {
            categories: ['']
        },
        yAxis: {
            title: {
                text: 'Number of open issues'
            }
        },
        series: [{
            name: 'Total number of open issues',
            data: [total]
        }, {
            name: 'Number of open issues that were opened in the last 24 hours',
            data: [lst24]
        }, {
            name: 'Number of open issues that were opened more than 24 hours ago but less than 7 days ago',
            data: [lst24to7]
        }, {
            name: 'Number of open issues that were opened more than 7 days ago',
            data: [before7]
        }]
    });
});
</script>
<!-- End of body -->
</body>
</html>
