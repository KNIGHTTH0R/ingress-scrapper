<?php
require 'config/config.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <script src="//code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/js/bootstrap.js"></script>
    <script type="text/javascript" src="/js/chat.min.js"></script>
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Coda">
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/css/misc.css">
<?
echo "    <title>{$cfg['site']['title']} - Chat Logs</title>\n";
if (isset($_GET['key']))
{
    $key = $_GET['key'];
    if (isset($_GET['debug']))
        echo "   8:session started\n";
    echo "    <script>var apiKey = '{$key}';</script>\n";
} 
else 
{
    header("location: /?afterlogin={$_SERVER['PHP_SELF']}");
}
?>
  </head>
  <body>
    <p></p>
    <div class="btn-group">
      <button id="showPub" type="button" class="btn" onclick="toggleChannel('Pub');"><i class="sprite sprite-neutral"></i>Public</button>
      <button id="showRe" type="button" class="btn btn-primary" onclick="toggleChannel('Re');"><i class="sprite sprite-resistance"></i></button>
      <button id="showEn" type="button" class="btn btn-success" onclick="toggleChannel('En');"><i class="sprite sprite-aliens"></i></button>
    </div>
    <div class="btn-group">
      <button id="Calgary" type="button" class="btn" onclick="toggleRegion(1);">Calgary</button>
      <button id="Edmonton" type="button" class="btn" onclick="toggleRegion(2);">Edmonton</button>
    </div>
    <div class="btn-group">
        <a class="btn btn-mini" target="_blank" href="https://github.com/WritheM/ingress-scrapper/issues">Suggestions/Issues</a>
        <a class="btn btn-mini" href="/">Account</a>
        <a class="btn btn-mini" href="/?logout">Logout</a>
    </div>
    <p></p>
    <div id="loading" style="position:absolute;top:50px;right:50px;">
      <!-- Map Loading Spinner -->
      <div class="img_outerwheel">
        <div class="img_innerwheel">
        </div>
      </div>
      <div id="map_spinner_text">
        Loading Data...
      </div>
    </div>
    <div id="offline" style="position:absolute;top:50px;right:50px;display:none;">
      <!-- Map Loading Spinner -->
      <div class="img_outerwheel">
        <div class="img_innerwheel">
        </div>
      </div>
      <div id="map_spinner_text">
        Upgrading System... 
      </div>
    </div>

    
    <div id="chat" style="overflow-x: hidden;
    overflow-y: scroll;
    position: absolute;
width: 100%;
bottom: 50px;
left: 0;
top: 60px">
    </div>
        
<!-- END CONTENT -->


  <div id="footer" style="position: absolute;
                        width: 100%;
                        bottom: 0px;">
          <button id="status" type="button" class="btn btn-mini btn-inverse disabled">Loading...</button>

           &nbsp;&middot;&nbsp; Copyright &#169; 2013 &nbsp;&middot;&nbsp; <a href="http://writhem.com/">WritheM Web Solutions.</a>

  </div>

  <?
  echo $cfg['analytics'];
  ?>
</body>
</html>
