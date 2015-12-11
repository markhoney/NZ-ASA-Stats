<?php

error_reporting(E_ALL);
//error_reporting(E_ERROR | E_PARSE);
ini_set("display_errors", "On");

require_once 'config.php';

$GLOBALS['nokey'] = "#####";

$GLOBALS['base'] = 'http://' . $_SERVER['SERVER_NAME'] . '/';

$GLOBALS['sbh'] = ['M. Honeychurch', 'M. Hanna', 'L. Taylor', 'D. Ryan', 'C. Gold', 'S. McAuliffe', 'R. Seddon-Smith', 'M. Edmonds', 'C. Atkinson', 'S. Clark', 'M. Willey', 'T. Atkin', 'L. Oldfield', 'K. Honeychurch', 'M. Taylor', 'C. Morgan', 'N. Grange', 'P. Muir', 'A. Gilbey', 'M. Coffey', 'M. Taylor', 'R. Shillito', 'C. Suen', 'L. Marron', 'K. Perrott', 'T. Linney', 'B. Lennox', 'S. Sinclair', 'K. Hester'];

$GLOBALS['badgedescriptions'] = ["success" => "Your first successful complaint", "warming" => "You have made 5 successful complaints", "addict" => "You have made 10 successful complaints", "unstoppable" => "You have made 25 successful complaints", "monster" => "You have made 50 successful complaints", "centurion" => "You have made 100 successful complaints", "godlike" => "You have made 1,000 successful complaints", "eighty" => "Over 80% of your complaints are successful", "king" => "You have the most successful complaints", "top10" => "You are in the top 10 of most successful complainants", "two" => "You have complained along with someone else", "three" => "You have complained with two or more others", "bummer" => "You've had an unsuccessful complaint", "close" => "One of your successful complaints was overturned on appeal", "denied" => "You tried to appeal an unsuccessful complaint, and failed", "insistent" => "You appealed an unsuccessful complaint and won", "untouchable" => "You defended a successful complaint against an appeal", "thorn" => "You have complained 3 times about one company", "pita" => "You have complained 5 times about one company", "archnemesis" => "You have complained 10 times about one company", "who" => "You submitted the first ever complaint against a company", "three" => "You have submitted complaints in 3 consecutive years", "half" => "You have submitted complaints in 5 consecutive years", "newyear" => "You submitted the first complaint of the year", "christmas" => "You submitted the last complaint of the year", "skeptical" => "You managed to include the word 'skeptical' (or 'sceptical') in a complaint", "inconceivable" => "You managed to include the word 'inconceivable' in a complaint", "charlatan" => "You managed to include the word 'charlatan' in a complaint", "quackery" => "You managed to include the word 'quackery' in a complaint", "schadenfreude" => "You managed to include the word 'schadenfreude' in a complaint", "cartoon" => "You managed to include an XKCD or SMBC cartoon in a complaint", "fishbarrel" => "You've used Fishbarrel to submit a complaint", "essay" => "You have a complaint document that is over 10,000 words in length", "clauses" => "You complained about an advert that breached 5 rules", "moreclauses" => "You complained about an advert that breached 10 rules", "codes" => "You've used 5 different ASA codes to complain", "products" => "You've used the Therapeutic Products Advertising Code", "services" => "You've used the Therapeutic Services Advertising Code", "ethics" => "You've used the Code of Ethics", "weight" => "You've used the Code for Advertising of Weight Management", "children" => "You've used the Code for Advertising to Children", "comparative" => "You've used the Code for Comparative Advertising", "website" => "You've complained about an Advertiser Website", "television" => "You've complained about a Television advert", "outdoor" => "You've complained about an Outdoor advert", "newspaper" => "You've complained about a Newspaper advert", "magazine" => "You've complained about a Magazine advert", "email" => "You've complained about an Email advert", "dm" => "You've complained about a Direct Marketing advert", "radio" => "You've complained about a Radio advert", "yellow" => "You've complained about a Yellow Pages advert", "multimedia" => "You've complained about adverts in 5 different media"];
$GLOBALS['sql']['count'] = "SELECT COUNT(*) AS complaints ";
$GLOBALS['sql']['tables']['complainants'] = "FROM complaints_complainants, complainants, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['companies'] = "FROM complaints_companies, companies, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['media'] = "FROM complaints_media, media, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['products'] = "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id LEFT JOIN products ON products.id = complaints.products_id ";
$GLOBALS['sql']['tables']['years'] = "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
//$GLOBALS['sql']['tables']['codes'] = "FROM clauses, codes, complaints_clauses, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['codes'] = "FROM decisions, (SELECT complaints.*, codes.name FROM clauses, codes, complaints_clauses, complaints WHERE clauses.codes_id = codes.id AND clauses.id = complaints_clauses.clauses_id AND complaints_clauses.complaints_id = complaints.id GROUP BY complaints_clauses.complaints_id, codes.name) AS complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['code'] = "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['clauses'] = "FROM clauses, codes, complaints_clauses, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['link']['complainants'] = "WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['companies'] = "WHERE complaints_companies.companies_id = companies.id AND complaints_companies.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['media'] = "WHERE complaints_media.media_id = media.id AND complaints_media.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['products'] = "WHERE complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['years'] = "WHERE complaints.decisions_id = decisions.id ";
//$GLOBALS['sql']['link']['codes'] = "WHERE clauses.codes_id = codes.id AND clauses.id = complaints_clauses.clauses_id AND complaints_clauses.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['codes'] = "WHERE complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['code'] = "WHERE complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['clauses'] = "WHERE clauses.codes_id = codes.id AND clauses.id = complaints_clauses.clauses_id AND complaints_clauses.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['extra']['codes'] = "(SELECT DISTINCT complaints_id FROM complaints_clauses WHERE clauses_id IN (SELECT id FROM clauses WHERE codes_id = (SELECT id FROM codes WHERE name = ?)))";
$GLOBALS['sql']['successes'] = "((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1))";
$GLOBALS['sql']['stats']['total'] = "COUNT(*) AS total ";
$GLOBALS['sql']['stats']['win'] = "((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) AS win ";
$GLOBALS['sql']['stats']['draw'] = "(decisions.success IS NULL) AS draw ";
$GLOBALS['sql']['stats']['loss'] = "(1 - ((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) - (decisions.success IS NULL)) AS loss ";
#$GLOBALS['sql']['stats']['percent'] = "(win * 100 / (win + loss)) AS percent ";
$GLOBALS['sql']['filter'] = "AND " . $GLOBALS['sql']['successes'] . " AND complainants.name = ? ";
$GLOBALS['sql']['stats']['all'] = 'SELECT ' . $GLOBALS['sql']['stats']['total'] . ", SUM" . $GLOBALS['sql']['stats']['win'] . ", SUM" . $GLOBALS['sql']['stats']['draw'] . " ";
$GLOBALS['sql']['multiples'] = "(SELECT GROUP_CONCAT(name SEPARATOR '|') FROM codes WHERE id IN (SELECT DISTINCT codes_id FROM clauses WHERE id IN (SELECT clauses_id FROM complaints_clauses WHERE complaints_id = complaints.id))) AS codes, (SELECT GROUP_CONCAT(CONCAT(codes.name, ', ', clauses.name) SEPARATOR '|') FROM clauses LEFT JOIN codes ON clauses.codes_id = codes.id WHERE clauses.id IN (SELECT clauses_id FROM complaints_clauses WHERE complaints_id = complaints.id)) AS clauses, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM media WHERE media.id IN (SELECT media_id FROM complaints_media WHERE complaints_id = complaints.id)) AS media, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM complainants WHERE complainants.id IN (SELECT complainants_id FROM complaints_complainants WHERE complaints_id = complaints.id)) AS complainants, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM companies WHERE companies.id IN (SELECT companies_id FROM complaints_companies WHERE complaints_id = complaints.id)) AS companies ";
$GLOBALS['sql']['complaint'] = "complaints.id as id, CONCAT(SUBSTR(complaints.id, 1, 2), '/', SUBSTR(complaints.id, 3)) AS idslash, year, advert, IFNULL(meetingdate, '[None]') AS meetingdate, CONCAT('http://old.asa.co.nz/display.php?ascb_number=', complaints.id) AS url, decisions.name AS decision, decisions.arbiter, decisions.ruling, decisions.success, appeals.success as appealsuccess, appeals.id as appealid, CONCAT(SUBSTR(appeals.id, 1, 2), '/', SUBSTR(appeals.id, 3)) AS appealidslash, IFNULL((SELECT name FROM products WHERE id = products_id), '[None]') AS product, CONCAT('http://old.asa.co.nz/decision_file.php?ascbnumber=', complaints.id) AS docurl, docwords";
$GLOBALS['sql']['list']['complaints']['complainant'] = "(SELECT complaints_id FROM complaints_complainants WHERE complainant_id = (SELECT id FROM complainants WHERE name = ?))";
$GLOBALS['sql']['list']['complaints']['company'] = "(SELECT complaints_id FROM complaints_companies WHERE companies_id = (SELECT id FROM companies WHERE name = ?))";
$GLOBALS['sql']['list']['complaints']['medium'] = "(SELECT complaints_id FROM complaints_media WHERE media_id = (SELECT id FROM media WHERE name = ?))";
//$GLOBALS['sql']['list']['complaints']['clause'] = "(SELECT complaints_id FROM complaints_clauses WHERE clause_id = (SELECT id FROM clauses WHERE name = ?))";

$GLOBALS['typedata'] = ["complaints" => ["complaint", "id"], "complainants" => ["complainant", "name"], "companies" => ["company", "name"], "media" => ["medium", "name"], "products" => ["product", "name"], "codes" => ["code", "name"], "clauses" => ["clause", "name"], "badges" => ["badge", "name"], "years" => ["year", "name"], "appeals" => ["appeal", "name"]];
$GLOBALS['listdata'] =  ["links" => ["link", "name"], "external" => ["link", "name"], "internal" => ["link", "name"]];

$GLOBALS['results'] = ["win" => "success", "draw" => "warning", "loss" => "danger"];

function printheader($title, $form = False) {
?>
<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php print $title; ?></title>
  <meta property="og:locale" content="en_US" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?php print $title; ?>" />
  <meta property="og:site_name" content="Society for Science Based Healthcare ASA Statistics" />
  <meta property="og:image" content="http://asa.sbh.nz/images/sbhasa.jpg" />
  <link rel="shortcut icon" href="http://sbh.nz/wp-content/uploads/favicon.ico" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.23.3/css/theme.bootstrap.min.css">
  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>-->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.23.3/js/jquery.tablesorter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.23.3/js/jquery.tablesorter.widgets.min.js"></script>
  <script>
   $(function(){
    $(".table.sorter").tablesorter({theme : "bootstrap", headerTemplate : '{content} {icon}', widgets : [ "uitheme"], widgetOptions : {filter_reset : ".reset", filter_cssFilter: "form-control"}});
   });
   $(function(){
    $(".table.filter").tablesorter({theme : "bootstrap", headerTemplate : '{content} {icon}', widgets : [ "uitheme", "filter"], widgetOptions : {filter_reset : ".reset", filter_cssFilter: "form-control"}});
   });
  </script>
  <style>
   body {background-color: #EAEAEA; padding: 30px 0;}
   @media (min-width: 1200px) {.container {width: 1268px;}}
   #content {background-color: #FFFFFF; box-shadow: rgba(100, 100, 100, 0.298039) 0px 2px 6px 0px;}
   .success {color: #009900;}
   .failure {color: #990000;}
   .badges img {width: 96px; height: 96x;}
   #badgelist img {width: 96px; height: 96x;}
   pre {white-space: pre-wrap; word-break: normal; text-align: justify; padding: 20px;}
   #footer {max-width: 800px; margin: 0 auto; font-size: 10px; text-align: center;}
   #Frame1, #Frame2, #Frame3, #Frame4, #Frame5, .sd-abs-pos {display: none;}
   #title {margin-left: 10px; margin-top: 6px;}
   #title h1 {font-family: 'Lato', sans-serif; font-size: 48px; background: url(http://sbh.nz/wp-content/uploads/header.png) no-repeat; min-height: 65px; padding-left: 35px;}
   #title h2 {font-family: 'Lato', sans-serif; margin-top: -20px; margin-left: 35px;}   
   #title h1 a, #title h2 a {color: #000000;}
   #title h1 a:hover, #title h2 a:hover {text-decoration: none; color: #0072BF;}
  </style>
  <script>
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
   })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
   ga('create', 'UA-52102538-2', 'auto');
   ga('send', 'pageview');
  </script>
 </head>
 <body>
  <div id="content" class="container">
   <div class="row">
    <div class="col-md-12" id="title">
     <h1><a href="http://sbh.nz/">The Society for Science Based Healthcare</a></h1>
     <h2><a href="http://asa.sbh.nz/">NZ ASA Complaint Statistics</a></h2>
     <form class="pull-right" action="/">
      <input type="text" name="search">
      <input type="submit" value="Search">
     </form>
    </div>
   </div>
   <hr />
<?php
}

function printfooter() {
?>
   <div id="footer">
    <p id="disclaimer">The data used in this site has been taken from the New Zealand Advertising Standards Authority's <a href="http://old.asa.co.nz/database.php">Complaints Database</a>. Some adjustments have been made to improve consistency, such as standardising company and complainant names. We do not guarantee the accuracy of this data. If you find any errors, please let us know by emailing us at <a href="mailto:sbh@sbh.nz">sbh@sbh.nz</a>.</p>
    <?php print '<p>View this page as <a href="' . $GLOBALS['url'] . '.json">JSON</a> / <a href="' . $GLOBALS['url'] . '.xml">XML</a></p>'; ?>
   </div>
  </div>
 </body>
</html>
<?php
}

function getData($query, $vars = []) {
 if (__FILE__ == "/var/www/html/asa/test.php") {
  print '<pre>';
  print $query . "\n";
  print_r($vars);
  print '</pre>';
  //exit();
 }
 $stmt = $GLOBALS['db']->prepare($query);
 $stmt->execute($vars);
 if (__FILE__ == "/var/www/html/asa/test.php") {
  print '<pre>';
  print $stmt->debugDumpParams();
  print '</pre>';
 }
 if ($stmt->rowCount() > 0) {
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }
}

function _make_url_clickable_cb($matches) {
 $ret = '';
 $url = $matches[2];
 
 if (empty($url))
  return $matches[0];
 // removed trailing [.,;:] from URL
 if (in_array(substr($url, -1), ['.', ',', ';', ':']) === true) {
  $ret = substr($url, -1);
  $url = substr($url, 0, strlen($url)-1);
 }
 return $matches[1] . "<a href=\"$url\" target=\"_blank\">$url</a>" . $ret;
}
 
function _make_web_ftp_clickable_cb($matches) {
 $ret = '';
 $dest = $matches[2];
 $dest = 'http://' . $dest;
 
 if (empty($dest))
  return $matches[0];
 // removed trailing [,;:] from URL
 if (in_array(substr($dest, -1), ['.', ',', ';', ':']) === true) {
  $ret = substr($dest, -1);
  $dest = substr($dest, 0, strlen($dest)-1);
 }
 return $matches[1] . "<a href=\"$dest\" target=\"_blank\">$dest</a>" . $ret;
}
 
function _make_email_clickable_cb($matches) {
 $email = $matches[2] . '@' . $matches[3];
 return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
}
 
function make_clickable($ret) {
 $ret = ' ' . $ret;
 // in testing, using arrays here was found to be faster
 $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
 $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
 $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);
 // this one is not in an array because we need it to run last, for cleanup of accidental links within links
 $ret = preg_replace("#(<a([^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
 $ret = trim($ret);
 return $ret;
}

function badges($complainant) {
 $totals = getData("SELECT " . $GLOBALS['sql']['stats']['total'] . ", SUM" . $GLOBALS['sql']['stats']['win'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . "AND complainants.name = ?", [$complainant])[0];
 if ($totals['total'] >= 1) {
  $badges['success'] = ($totals['win'] >= 1 ? 1 : 0);
  $badges['warming'] = ($totals['win'] >= 5 ? 1 : 0);
  $badges['addict'] = ($totals['win'] >= 10 ? 1 : 0);
  $badges['unstoppable'] = ($totals['win'] >= 25 ? 1 : 0);
  $badges['monster'] = ($totals['win'] >= 50 ? 1 : 0);
  $badges['centurion'] = ($totals['win'] >= 100 ? 1 : 0);
  $badges['godlike'] = ($totals['win'] >= 1000 ? 1 : 0);
  $badges['eighty'] = ($totals['win'] / $totals['total'] >= 0.8 ? 1 : 0);
  $badges['bummer'] = ($totals['total'] - $totals['win'] >= 1 ? 1 : 0);
  $top10 = getData("SELECT complainants.name, SUM" . $GLOBALS['sql']['stats']['win'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . "AND complainants.name NOT IN ('Other', 'Others') GROUP BY complainants.name ORDER BY win DESC LIMIT 10");
  if ($top10) {
   $badges['king'] = ($top10[0]['name'] == $complainant ? 1 : 0);
   foreach ($top10 as $complainer) {
    if ($complainer['name'] == $complainant) {
     $badges['top10'] = 1;
    }
   }
  }
  $query = "SELECT " . $GLOBALS['sql']['stats']['total'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'];
  $badges['close'] = (getData($query . "AND appeals.success = 1 " . $GLOBALS['sql']['filter'], [$complainant])[0]['total'] >= 1 ? 1 : 0);
  $badges['denied'] = (getData($query . "AND decisions.success = 0 AND appeals.success = 0 AND complainants.name = ?", [$complainant])[0]['total'] >= 1 ? 1 : 0);
  $badges['insistent'] = (getData($query . "AND decisions.success = 0 AND appeals.success = 1 AND complainants.name = ?", [$complainant])[0]['total'] >= 1 ? 1 : 0);
  $badges['untouchable'] = (getData($query . "AND appeals.success = 0 " . $GLOBALS['sql']['filter'], [$complainant])[0]['total'] >= 1 ? 1 : 0);
  $idquery = "(SELECT complaints.id " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND ((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) = 1 AND complainants.name = ?)";
  $words = ['skeptical' => ['skeptical', 'sceptical'], 'inconceivable' => ['inconceivable'], 'charlatan' => ['charlatan'], 'quackery' => ['quackery'], 'schadenfreude' => ['schadenfreude'], 'cartoon' => ['http://xkcd.com/', 'http://www.smbc-comics.com/'], 'fishbarrel' => ['Claim found at http']];
  foreach ($words as $word => $values) {
   $badges[$word] = (getData("SELECT COUNT(*) as win FROM docs WHERE complaints_id IN " . $idquery . " AND contents REGEXP ?", [$complainant, implode("|", $values)])[0]['win'] >= 1 ? 1 : 0);
  }
  $badges['essay'] = (getData($query . "AND docwords >= 10000 " . $GLOBALS['sql']['filter'], [$complainant])[0]['total'] >= 1 ? 1 : 0);
  $clauses = getData("SELECT COUNT(*) AS clauses " . $GLOBALS['sql']['tables']['complainants'] .", complaints_clauses, clauses " . $GLOBALS['sql']['link']['complainants'] . "AND complaints_clauses.complaints_id = complaints.id AND clauses.id = complaints_clauses.clauses_id " . $GLOBALS['sql']['filter'] . "GROUP BY complaints.id ORDER BY clauses DESC LIMIT 1", [$complainant])[0]['clauses'];
  $badges['clauses'] = ($clauses >= 5 ? 1 : 0);
  $badges['moreclauses'] = ($clauses >= 10 ? 1 : 0);
  $codes = ["Therapeutic Products Advertising Code" => "products", "Therapeutic Services Advertising Code" => "services", "Code of Ethics" => "ethics", "Code for Advertising of Weight Management" => "weight", "Code for Advertising to Children" => "children", "Code for Comparative Advertising" => "comparative"];
  $codecount = 0;
  $codelist = getData("SELECT DISTINCT codes.name AS code FROM complaints_complainants, complainants, complaints, decisions, complaints_clauses, clauses, codes WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id AND complaints_clauses.complaints_id = complaints.id AND clauses.id = complaints_clauses.clauses_id AND codes.id = clauses.codes_id AND decisions.success = 1 AND complainants.name = ?", [$complainant]);
  if ($codelist) {
   foreach($codelist as $code) {
    if (array_key_exists($code['code'], $codes)) {
     $badges[$codes[$code['code']]] = 1;
    }
    $codecount++;
   }
  }
  $badges['codes'] = ($codecount >= 5 ? 1 : 0);
  $media = ["Advertiser Website" => "website", "Television" => "television", "Outdoor" => "outdoor", "Newspaper" => "newspaper", "Magazine" => "magazine", "Email" => "email", "DM - Unaddressed" => "dm", "Radio" => "radio", "Yellow Pages" => "yellow"];
  $mediacount = 0;
  if ($medialist = getData("SELECT DISTINCT media.name AS medium FROM complaints_complainants, complainants, complaints, decisions, complaints_media, media WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id AND complaints_media.complaints_id = complaints.id AND media.id = complaints_media.media_id AND decisions.success = 1 AND complainants.name = ?", [$complainant])) {
   foreach($medialist as $medium) {
    if (array_key_exists($medium['medium'], $media)) {
     $badges[$media[$medium['medium']]] = 1;
    }
    $mediacount++;
   }
  }
  $badges['multimedia'] = ($mediacount >= 5 ? 1 : 0);
  return array_keys(array_filter($badges));
 }
}

function rankingTable(&$data, $type, $schema = "", $sort = True, $detailed = True, $link = True, $rank = True) {
 $table = '<table class="table' . ($sort ? " sorter" : "") . '">' . "\n";
 $table .= "<thead><tr>" . ($rank ? "<th>Rank</th>" : "") . "<th>Name</th>" . ($detailed ? '<th style="white-space: nowrap">Success %</th><th>Success</th><th>Fail</th><th>Other</th><th>Total</th>' : "<th>Complaints</th>") . "</tr></thead><tbody>\n";
 $position = 1;
 foreach ($data as $name => $item) {
  $table .= '<tr>' . ($rank ? '<td>' . $position . '.</td>' : "") . '<td' . ($schema ? ' itemscope itemtype="' . $schema . '"' : '') . '>' . ($link ? '<a href="/' . $type . '/' . urlencode($name) . '"' . ($schema ? ' itemprop="url"><span itemprop="name">' . $name . '</span>' : '>' . $name) . '</a>' : $name) . '</td>' . ($detailed ? '<td>' . $item['percent'] . "%</td><td>" . $item['win'] . "</td><td>" . $item['loss'] . "</td><td>" . $item['draw'] . "</td>" : "") . "<td>" . $item['total'] . "</td></tr>" . "\n";
  $position++;
 }
 return $table . "</tbody></table>" . "\n";
}

function linkList($category, $list) {
 foreach ($list as &$item) {
  $item = '<a href="/' . $category . '/' . urlencode($item) . '">' . $item . '</a>';
 }
 return $list;
}

function complaintsTable(&$complaints, $sort = "sorter", $complainant = False, $company = False) {
 if (is_array($complaints)) {
  $columns = ["ID", "Advert"];
  $table = '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">Complaints</h3></div>';
  $table .= '<table class="table ' . $sort . '">' . "\n";
  $table .= '<thead><tr><th>ID</th>' . ($complainant ? '<th>Complainant</th>' : '') . ($company ? '<th>Company</th>' : '') . '<th>Advert</th><th>Product</th><th style="white-space: nowrap">Meeting Date</th></tr></thead><tbody>' . "\n";
  foreach ($complaints as $id => $complaint) {
   $table .= '<tr class="' . $GLOBALS['results'][$complaint['result']] . '"><td><a href="/complaint/' . $id . '">' . $complaint['idslash'] . '</a></td>' . ($complainant ? '<td>' . implode(", ", linkList('complainant', $complaint['complainants'])) . '</td>' : '') . ($company ? '<td>' . implode(", ", linkList('company', $complaint['companies'])) . '</td>' : '') . '<td><a href="/complaint/' . $id . '">' . $complaint['advert'] . '</a></td><td>' . $complaint['product'] . '</td><td>' . $complaint['meetingdate'] . '</td></tr>' . "\n";
  }
  return $table . "</tbody></table></div>" . "\n";
 }
}

function getComplaints($filter, $array = [], $win = False, $upheld = False) {
 if ($complaints = getData("SELECT complaints.id as id, CONCAT(SUBSTR(complaints.id, 1, 2), '/', SUBSTR(complaints.id, 3)) AS idslash, advert, IFNULL(meetingdate, '[None]') as meetingdate, IFNULL(products.name, '[None]') as product, " . $GLOBALS['sql']['stats']['win'] . ", " . $GLOBALS['sql']['stats']['draw'] . " FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id LEFT JOIN products ON products.id = complaints.products_id WHERE complaints.decisions_id = decisions.id " . ($win ? " AND decisions.success = 1 AND IFNULL(appeals.success, 0) = 0 " : "") . ($upheld ? " AND decisions.ruling LIKE '%Upheld%' " : "") . " AND complaints.id IN " . $filter . " ORDER BY complaints.meetingdate DESC, complaints.id DESC;", $array)) {
  foreach ($complaints as &$complaint) {
   complaintResult($complaint);
   complaintLinks($complaint);
  }
  return codekey($complaints, "id");
 }
}

function getComplaint($id) {
 if ($complaint = getData("SELECT " . $GLOBALS['sql']['complaint'] . ', ' . $GLOBALS['sql']['stats']['win'] . ', ' . $GLOBALS['sql']['stats']['draw'] . ', ' . $GLOBALS['sql']['multiples'] . "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id WHERE complaints.decisions_id = decisions.id AND complaints.id = ?", [$id])) {
  $complaint = $complaint[0];
  foreach (["codes", "clauses", "media", "complainants", "companies"] as $multi) {
   $complaint[$multi] = explode("|", $complaint[$multi]);
  }
  complaintResult($complaint);
  complaintLinks($complaint);
  return $complaint;
 }
}

function getComplaintsDetails($filter, $array = [], $win = False, $upheld = False) {
 if ($complaints = getData("SELECT " . $GLOBALS['sql']['complaint'] . ', ' . $GLOBALS['sql']['stats']['win'] . ', ' . $GLOBALS['sql']['stats']['draw'] . ', ' . $GLOBALS['sql']['multiples'] . "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id WHERE complaints.decisions_id = decisions.id " . ($win ? " AND decisions.success = 1 AND IFNULL(appeals.success, 0) = 0 " : "") . ($upheld ? " AND decisions.ruling LIKE '%Upheld%' " : "") . " AND complaints.id IN " . $filter . " ORDER BY complaints.meetingdate DESC, complaints.id DESC;", $array)) {
  foreach ($complaints as &$complaint) {
   foreach (["codes", "clauses", "media", "complainants", "companies"] as $multi) {
    $complaint[$multi] = explode("|", $complaint[$multi]);
   }
   complaintResult($complaint);
   complaintLinks($complaint);
   $complaint = array_filter($complaint);
  }
  return codekey($complaints, "id");
 }
}


function complaintResult(&$complaint) {
 if ($complaint["win"]) {
  $complaint["result"] = "win";
 } elseif ($complaint['draw']) {
  $complaint["result"] = "draw";
 }
 else {
  $complaint["result"] = "loss";
 }
 unset($complaint['win']);
 unset($complaint['draw']);
}

function sbhData($limit = 0) {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", complainants.name " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "') GROUP BY complainants.name ORDER BY win DESC, total ASC, complainants.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";");
 calcStats($data);
 return codekey($data, "name");
}

function companiesData($limit = 0) {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", companies.name " . $GLOBALS['sql']['tables']['companies'] . $GLOBALS['sql']['link']['companies'] . " AND companies.name != 'Advertiser unknown' GROUP BY companies.name ORDER BY win DESC, total ASC, companies.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";");
 calcStats($data);
 return codekey($data, "name");
}

function complainantsData($limit = 0) {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", complainants.name " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name NOT IN ('Other', 'Others') GROUP BY complainants.name ORDER BY win DESC, total ASC, complainants.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";");
 calcStats($data);
 return codekey($data, "name");
}

function yearsData() {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", year as name " . $GLOBALS['sql']['tables']['years'] . $GLOBALS['sql']['link']['years'] . " GROUP BY year ORDER BY year DESC;");
 calcStats($data);
 return codekey($data, "name", True);
}

function codesData() {
 //$data = getData($GLOBALS['sql']['stats']['all'] . ", codes.name " . $GLOBALS['sql']['tables']['codes'] . $GLOBALS['sql']['link']['codes'] . " GROUP BY codes.name ORDER BY total DESC;");
 $data = getData($GLOBALS['sql']['stats']['all'] . ", complaints.name " . $GLOBALS['sql']['tables']['codes'] . $GLOBALS['sql']['link']['codes'] . " GROUP BY complaints.name ORDER BY total DESC;");
 calcStats($data);
 return codekey($data, "name");
}

function clausesData() {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", CONCAT(codes.name, ', ', clauses.name) AS name " . $GLOBALS['sql']['tables']['clauses'] . $GLOBALS['sql']['link']['clauses'] . "  GROUP BY clauses.id ORDER BY total DESC;");
 calcStats($data);
 return codekey($data, "name");
}

function mediaData() {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", media.name " . $GLOBALS['sql']['tables']['media'] . $GLOBALS['sql']['link']['media'] . " GROUP BY media.name ORDER BY total DESC;");
 calcStats($data);
 return codekey($data, "name");
}

function productsData() {
 $data = getData($GLOBALS['sql']['stats']['all'] . ", IFNULL(products.name, '[None]') as name " . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " GROUP BY products.name ORDER BY total DESC;");
 calcStats($data);
 return codekey($data, "name");
}

function calcStats(&$stats) {
 foreach ($stats as &$stat) {
  calcStat($stat);
 }
}

function calcStat(&$stat) {
 $stat['total'] = (int)$stat['total'];
 $stat['win'] = (int)$stat['win'];
 $stat['draw'] = (int)$stat['draw'];
 $stat['loss'] = $stat['total'] - $stat['win'] - $stat['draw'];
 $stat['percent'] = ($stat['win'] == 0 ? 0 : round(($stat['win'] * 100) / ($stat['win'] + $stat['loss']), 0));
}

function statsList(&$stats) {
 if ($stats['total'] > 0) {
  $table = '<div class="row"><div class="col-md-3">';
  $table .= '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Statistics</h3></div><div class="panel-body">';
  $table .= '<dl class="dl-horizontal"><dt>Complaints</dt><dd>' . $stats['total'] . "</dd>" . "\n";
  $table .= "<dt>Successful</dt><dd>" . $stats['win'] . "</dd>" . "\n";
  $table .= "<dt>Failed</dt><dd>" . $stats['loss'] . "</dd>" . "\n";
  $table .= "<dt>Other</dt><dd>" . $stats['draw'] . "</dd>" . "\n";
  $table .= "<dt>Success Percent</dt><dd>" . $stats['percent'] . "%</dd></dl>" . "\n";
  $table .= "</div></div>" . "\n";
  $table .= "</div></div>" . "\n";
  return $table;
 }
}

function notFound($type) {
 http_response_code(404);
 printheader($type . ' Not Found');
 print "<h1>" . $type . " Not Found</h1>" . "\n";
}

function remove_prefix($text, $prefix) {
 if(0 === strpos($text, $prefix))
  $text = substr($text, strlen($prefix)).'';
 return $text;
}

function xml($output, &$xml) {
 foreach($output as $key => $value) {
  if (is_array($value)) {
   if ($xml->getName() == htmlspecialchars($key)) {
    $subnode = $xml;
   }
   else {
    $subnode = $xml->addChild(htmlspecialchars($key));
   }
   if (array_key_exists($key, $GLOBALS['typedata'])) {
    foreach ($value as $k => $v) {
     if (is_array($v)) {
      $subsubnode = $subnode->addChild($GLOBALS['typedata'][$key][0]);
      $subsubnode->addAttribute($GLOBALS['typedata'][$key][1], htmlspecialchars($k));
      xml($v, $subsubnode);
     } else {
      $subsubnode = $subnode->addChild($GLOBALS['typedata'][$key][0], $v);
      $subsubnode->addAttribute($GLOBALS['typedata'][$key][1], htmlspecialchars($k));
     }
    }
   } else {
    xml($value, $subnode);
   }
  } else {
   if ($value) {
    $xml->addChild(htmlspecialchars($key), htmlspecialchars($value));
   }
  }
 }
}

function api($output, $type = "data", $multi = "") {
 if ($GLOBALS['filetype'] == 'json') {
  if (isset($_GET["callback"])) {
   header('Content-Type: application/javascript');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET');
   print $_GET['callback'] . '(' . json_encode($output) . ');';
  } else {
   header("Content-Type: application/json", true);
   print json_encode($output);
  }
 } elseif ($GLOBALS['filetype'] == 'xml') {
  header("Content-type: application/xml");
  $xml = new SimpleXMLElement('<?xml version="1.0"?><' . $type . '></' . $type . '>');
  xml($output, $xml);
  print $xml->asXML();
  /*
  $dom = new DOMDocument('1.0');
  $dom->preserveWhiteSpace = false;
  $dom->formatOutput = true;
  $dom->loadXML($xml->asXML());
  print $dom->saveXML();
  */
 }
}

function getEncoding() {
 $url = remove_prefix(strtok($_SERVER["REQUEST_URI"], '?'), "/test.php");
 foreach(['json' => 'application/json', 'xml' => 'application/xml', 'html' => 'text/html', 'htm' => '', 'php' => ''] as $ext => $accept) {
  if (substr($url, - strlen('.' . $ext)) === '.' . $ext) {
   $url = substr($url, 0, - strlen('.' . $ext));
   if (!$accept) {
    $ext = 'html';
   }
   return [$url, $ext];
  } elseif ($_SERVER['HTTP_ACCEPT'] === $accept) {
   return [$url, $ext];
  }
 }
 return [$url, 'html'];
}

function getQuery($queries) {
 foreach($queries as $key) {
  if (array_key_exists($key, $GLOBALS['get'])) return ($GLOBALS['get'][$key] ? urldecode($GLOBALS['get'][$key]) : "");
 }
}

function codekey($in, $key, $int = False) {
 $out = [];
 foreach ($in as $element) {
  if ($int) {
   $out[(int)$element[$key]] = $element;
   unset($out[(int)$element[$key]][$key]);
  } else {
   $out[$element[$key]] = $element;
   unset($out[$element[$key]][$key]);
  }
 }
 return $out;
}

function addLinks($type, $name = "") {
 $base = $GLOBALS['base'] . $type;
 if ($name) $base .= "/" . $name;
 return ["html" => $base, "json" => $base . ".json", "xml" => $base . ".xml"];
}

function complaintLinks(&$complaint) {
 $complaint["links"]["sbh"] = $GLOBALS['base'] .'complaint/' . $complaint['id'];
 $complaint["links"]["asa"] = 'http://old.asa.co.nz/display.php?ascb_number=' . $complaint['id'];
 $complaint["links"]["doc"] = 'http://old.asa.co.nz/decision_file.php?ascbnumber=' . $complaint['id'];
}

function splitURL($url) {
 $get = [];
 if ($_GET) $get = $_GET;
 $path = explode("/", trim(strtok($url, '?'), "/"));
 if (!empty($path)) {
  for ($i = 0, $lim = sizeof($path); $i < $lim; $i += 2) {
   $get[$path[$i]] = (isset($path[$i + 1]) ? $path[$i + 1] : "");
  }
  foreach (["", "index", "test"] as $key) {
   unset($get[$key]);
  }
 }
 return $get;
}

list($GLOBALS['url'], $GLOBALS['filetype']) = getEncoding();
$GLOBALS['get'] = splitURL($GLOBALS['url']);

function checkURL() {
 foreach($GLOBALS['typedata'] as $plural => $data) {
  if (($term = getQuery([$plural, $data[0]])) !== null) {
   $plural($plural, $term);
  }
 }
}

if (!empty($GLOBALS['get'])) {
 if (($term = getQuery(['search'])) !== null) {
  if (!$term) {
   if ($GLOBALS['filetype'] == 'html') {
    printheader('ASA Search');
    print '<form class="pull-right"><input type="text" name="search"><input type="submit" value="Search"></form>';
   } else {
    $output["links"] = addLinks("search");
    api($output, 'search');
   }
  } else {
   if (array_key_exists('upheld', $_GET)) {
    $output["complaints"] = getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", ['%' . $term . '%'], True, True);
   } elseif (array_key_exists('success', $_GET)) {
    $output["complaints"] = getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", ['%' . $term . '%'], True);  
   } else {
    $output["complaints"] = getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", ['%' . $term . '%']);
   }
   if ($output["complaints"]) {
    if ($GLOBALS['filetype'] == 'html') {
     printheader('ASA Search: ' . $term);
     print "<h1>Search Results for " . $term . "</h1>" . "\n" . complaintsTable($output["complaints"], "filter");
    } else {
     $output["links"] = addLinks("search", $term);
     api($output, 'company');
    }
   } else {
    notfound($term);
   }
  }
 } elseif (($term = getQuery(['company', 'companies'])) !== null) {
  if (!$term) {
   $data = companiesData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('ASA Company Rankings');
    print '<h1>ASA Companies 2008-2015</h1>' . rankingTable($data, "company", "http://schema.org/Organization", False);
   } else {
    api($data, 'companies');
   }
  } else {
   if ($output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['companies'] . $GLOBALS['sql']['link']['companies'] . " AND companies.name = ?", [$term])[0]) {
    calcStat($output);
    $output["complaints"] = getComplaintsDetails("(SELECT complaints_id FROM complaints_companies WHERE companies_id = (SELECT id FROM companies WHERE name = ?))", [$term]);
    if ($GLOBALS['filetype'] == 'html') {
     printheader('Company: ' . $term);
     print "<h1>Company: " . $term . "</h1>" . "\n" . statsList($output) . complaintsTable($output["complaints"], "sorter", True);
    } else {
     api($output, 'company');
    }
   } else {
    notfound('Company');
   }
  }
 } elseif (($term = getQuery(['complainant', 'complainants'])) !== null) {
  if (!$term) {
   $data = complainantsData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('ASA Complainant Rankings');
    print '<h1>ASA Complainants 2008-2015</h1>' . rankingTable($data, "complainant", "http://schema.org/Person", False);
   } else {
    api($data, 'complainants');
   }
  } else {
   //$stats = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name = ?", [$term]);
   //if ($stats) {
   if ($output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name = ?", [$term])[0]) {
    calcStat($output);
    $output["complaints"] = getComplaintsDetails("(SELECT complaints_id FROM complaints_complainants WHERE complainants_id = (SELECT id FROM complainants WHERE name = ?))", [$term]);
    $output["badges"] = badges($term);
    if ( $GLOBALS['filetype'] == 'html') {
     printheader('Complainant: ' . $term);
     print "<h1>Complainant: " . $term . "</h1>" . "\n";
     if (in_array($term, $GLOBALS['sbh'])) print '<h3><a href="/sbh">Member of SBH</a></h3>';
     print statsList($output);
     print '<div class="panel panel-info badges"><div class="panel-heading"><h3 class="panel-title">Badges</h3></div><div class="panel-body">';
     if ($output["badges"]) {
      foreach ($output["badges"] as $badge) {
       //print "<li>" . $badge . "</li>" . "\n";
       print '<img src="/images/' . $badge . '.png" alt="' . ucwords($badge) . '" title="' . $GLOBALS['badgedescriptions'][$badge] . '" />' . "\n";
      }
     }
     print "</div></div>" . "\n";
     print complaintsTable($output["complaints"], "sorter", False, True);
    } else {
     api($output, 'complainant');
    }
   } else {
    notfound('Complainant');
   }
  }
 } elseif (($term = getQuery(['product', 'products'])) !== null) {
  if (!$term) {
   $data = productsData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('Complaints by Product');
    print "<h1>Complaints by Product</h1>" . "\n" . rankingTable($data, "product", "", False);
   } else {
    api($data, 'products');
   }
  } else {
   if ($term == '[None]') {
    $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " AND products.name IS NULL")[0];
   } else {  
    $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " AND products.name = ?", [$term])[0];
   }
   if ($output) {
    calcStat($output);
    if ($term == '[None]') {
     $output["complaints"] = getComplaints("(SELECT id FROM complaints WHERE products_id IS NULL)");
    } else {  
     $output["complaints"] = getComplaints("(SELECT id FROM complaints WHERE products_id = (SELECT id FROM products WHERE name = ?))", [$term]);
    }
    if ($GLOBALS['filetype'] == 'html') {
     printheader('Product: ' . $term);
     print "<h1>Product: " . $term . "</h1>" . "\n";
     print statsList($stats) . complaintsTable($output["complaints"], "filter");
    } else {
     api($output, 'product');
    }
   } else {
    notfound('Product');
   }
  }
 } elseif (($term = getQuery(['medium', 'media'])) !== null) {
  if (!$term) {
   $data = mediaData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('Complaints by Media');
    print "<h1>Complaints by Media</h1>" . "\n" . rankingTable($data, "media");
   } else {
    api($data, 'media');
   }
  } else {
   if ($output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['media'] . $GLOBALS['sql']['link']['media'] . " AND media.name = ?", [$term])[0]) {
    calcStat($output);
    $output["complaints"] = getComplaints("(SELECT complaints_id FROM complaints_media WHERE media_id = (SELECT id FROM media WHERE name = ?))", [$term]);
    if ($GLOBALS['filetype'] == 'html') {
     printheader('Media: ' . $term);
     print "<h1>Media: " . $term . "</h1>" . "\n";
     print statsList($stats) . complaintsTable($output["complaints"], "filter");
    } else {
     api($output, 'media');
    }
   } else {
    notfound('Media');
   }
  }
 } elseif (($term = getQuery(['code', 'codes'])) !== null) {
  if (!$term) {
   $data = codesData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('Complaints by Code');
    print "<h1>Complaints by Code</h1>" . "\n" . rankingTable($data, "code");
   } else {
    api($data, 'code');
   }
  } else {
   //$output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['codes'] . $GLOBALS['sql']['link']['codes'] . " AND codes.name = ?", [$term])[0];
   $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['code'] . $GLOBALS['sql']['link']['code'] . " AND complaints.id IN " . $GLOBALS['sql']['extra']['codes'], [$term])[0];
   if ($output) {
    calcStat($output);
    $output["complaints"] = getComplaints($GLOBALS['sql']['extra']['codes'], [$term]);
    if ($GLOBALS['filetype'] == 'html') {
     printheader('Code: ' . $term);
     print "<h1>Code: " . $term . "</h1>" . "\n";
     print statsList($output) . complaintsTable($output["complaints"], "filter");
    } else {
     api($output, 'code');
    }
   } else {
    notfound('Code');
   }
  }
 } elseif (($term = getQuery(['clause', 'clauses'])) !== null) {
  if (!$term) {
   $data = clausesData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('Complaints by Clause');
    print "<h1>Complaints by Clause</h1>" . "\n" . rankingTable($data, "clause");
   } else {
    api($data, 'clause');
   }
  } else {
   $clause = explode(", ", $term);
   $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['clauses'] . $GLOBALS['sql']['link']['clauses'] . " AND codes.name = ? AND clauses.name = ?", $clause)[0];
   if ($output) {
    calcStat($output);
    $output["complaints"] = getComplaints("(SELECT DISTINCT complaints_id FROM complaints_clauses WHERE clauses_id = (SELECT id FROM clauses WHERE codes_id = (SELECT id FROM codes WHERE name = ?) AND name = ?))", $clause);
    if ($GLOBALS['filetype'] == 'html') {
     printheader('Clause: ' . $term);
     print "<h1>Clause: " . $term . "</h1>" . "\n";
     print statsList($output) . complaintsTable($output["complaints"], "filter");
    } else {
     api($output, 'clause');
    }
   } else {
    notfound('Clause');
   }
  }
 } elseif (($term = getQuery(['year', 'years'])) !== null) {
  if (!$term) {
   $data = yearsData();
   if ($GLOBALS['filetype'] == 'html') {
    printheader('Complaints by Year');
    print "<h1>Complaints by Year</h1>" . "\n" . rankingTable($data, "year", "", False, True, True, False);
   } else {
    api($data, 'years');
   }
  } else {
   $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['years'] . $GLOBALS['sql']['link']['years'] . " AND year = ?", [$term])[0];
   if ($output) {
    calcStat($output);
    $output["complaints"] = getComplaints("(SELECT id FROM complaints WHERE year = ?)", [$term]);
    if ( $GLOBALS['filetype'] == 'html') {
     printheader('Year: ' . $term);
     print "<h1>Year: " . $term . "</h1>" . "\n";
     print statsList($output) . complaintsTable($output["complaints"], "filter");
    } else {
     api($output, 'year');
    }
   } else {
    notfound('Year');
   }
  }
 } elseif (($term = getQuery(['sbh'])) !== null) {
  $output = getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "')")[0];
  calcStat($output);
  if (!$term) {
   $output["complainants"] = sbhData();
   $output["complaints"] = getComplaintsDetails("(SELECT id FROM complaints WHERE meetingdate > (NOW() - INTERVAL 6 MONTH) AND meetingdate < (NOW() + INTERVAL 1 DAY) AND id IN (SELECT complaints_id FROM complaints_complainants WHERE complainants_id IN (SELECT id FROM complainants WHERE complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "'))) ORDER BY meetingdate DESC)");
   $output["links"]["internal"] = ['Therapeutic Products (Code)' => '/code/Therapeutic+Products+Advertising+Code', 'Therapeutic Services (Code)' => '/code/Therapeutic+Services+Advertising+Code', 'Therapeutic (Product)' => '/product/Therapeutic', 'Homeopathy (Search)' => '/search/homeopath', 'Acupuncture (Search)' => '/search/acupunctur', 'Chiropractic (Search)' => '/search/chiropract', 'Cancer (Search)' => '/search/cancer', 'Arthritis (Search)' => '/search/arthritis', 'BioMag (Company)' => '/company/Woolrest+Biomag', 'Bioptron (Company)' => '/company/Bioptron', 'Appeals' => '/appeals'];
   $output["links"]["external"] = ['ASA Website' => 'http://www.asa.co.nz/', 'Online Complaint Form' => 'http://www.asa.co.nz/complaints/make-a-complaint/', 'Therapeutic Products Code' => 'http://www.asa.co.nz/codes/codes/therapeutic-products-advertising-code/', 'Therapeutic Services Code' => 'http://www.asa.co.nz/codes/codes/therapeutic-services-advertising-code/', 'Ethics Code' => 'http://www.asa.co.nz/codes/codes/advertising-code-of-ethics/', 'Appeals Process' => 'http://www.asa.co.nz/decisions/the-appeals-process/', 'Recent Decisions' => 'http://old.asa.co.nz/decisions_to_media.php', 'Medicines Act 1981' => 'http://www.legislation.govt.nz/act/public/1981/0118/latest/whole.html#DLM53790', 'TAPS "Weasel" Words' => 'http://www.anza.co.nz/Section?Action=View&Section_id=45'];
   if ( $GLOBALS['filetype'] == 'html') {
    printheader('ASA SBH Complainant Rankings');
    print '<h1>SBH Related Complaints</h1>';
    print statsList($output);
    print '<div class="row"><div class="col-md-9">';
    print '<h2>Society Members</h2>';
    print rankingTable($output["complainants"], "complainant", "http://schema.org/Person", False);
    print '</div><div class="col-md-3">';
    print '<h2>Useful Pages</h2><ul>';
    foreach ($output["links"]["internal"] as $topic => $url) {
     print '<li><a href="' . $url . '">' . $topic . '</a></li>';
    }
    print '</ul><h2>External Links</h2><ul>';
    foreach ($output["links"]["external"] as $topic => $url) {
     print '<li><a href="' . $url . '" target="_blank">' . $topic . '</a></li>';
    }
    print '</ul>';
    print '</div></div>';
    print '<h2>Latest <a href="/sbh/all">Member Complaints</a></h2>';
    print complaintsTable($output["complaints"], "sorter", True, True);
   } else {
    api($output, 'sbh');
   }
  } else {
   

   $output["complaints"] = getComplaintsDetails("(SELECT id FROM complaints WHERE id IN (SELECT complaints_id FROM complaints_complainants WHERE complainants_id IN (SELECT id FROM complainants WHERE complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "'))) ORDER BY meetingdate DESC)");
   if ($GLOBALS['filetype'] == 'html') {
    printheader('SBH Member Complaints');
    print "<h2>SBH Member Complaints</h2>";
    print statsList($output);
    print complaintsTable($output["complaints"], "filter", True, True);
   } else {
    api($output, 'sbh');
   }
  }
 } elseif (($term = getQuery(['badge', 'badges'])) !== null) {
  $data = $GLOBALS['badgedescriptions'];
  if ($GLOBALS['filetype'] == 'html') {
   printheader('List of Badges');
   print "<h1>List of Badges</h1>" . "\n";
   print '<div id="badgelist">';
   foreach ($GLOBALS['badgedescriptions'] as $badge => $description) {
    print '<img src="/images/' . $badge . '.png" alt="' . ucwords($badge) . '" />' . $description . "<br />\n";
   }
   print '</div>';
  } else {
   api($data, 'badges');
  }
 } elseif (($term = getQuery(['appeal', 'appeals'])) !== null) {
  $data = ["unsuccessfuloverturned" => getComplaintsDetails("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 0 AND appeals.success = 1)")];
  $data["successfuldefended"] = getComplaintsDetails("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 1 AND appeals.success = 0)");
  $data["successfuloverturned"] = getComplaintsDetails("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 1 AND appeals.success = 1)");
  $data["unsuccessfuldefended"] = getComplaintsDetails("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 0 AND appeals.success = 0)");
  if ($GLOBALS['filetype'] == 'html') {
   print "<h1>Appeals</h1>" . "\n";
   print "<h2>Unsuccessful Complaints Overturned</h2>" . "\n";
   print complaintsTable($data["unsuccessfuloverturned"], "sorter", True, True);
   print "<h2>Successful Complaints Defended</h2>" . "\n";
   print complaintsTable($data["successfuldefended"], "sorter", True, True);
   print "<h2>Successful Complaints Overturned</h2>" . "\n";
   print complaintsTable($data["successfuloverturned"], "sorter", True, True);
   print "<h2>Unsuccessful Complaints Defended</h2>" . "\n";
   print complaintsTable($data["unsuccessfuldefended"], "sorter", True, True);
  } else {
   api($data, 'appeals');
  }
 } elseif (($term = getQuery(['complaint', 'complaints'])) !== null) {
  if (!$term or (strpos($term, ",") !== FALSE) or $term == 'details') {
   if (!$term) {
    $data["complaints"] = getComplaints("(SELECT complaints.id FROM complaints)");
   } else {
    if ($term == 'details') {
     $data["complaints"] = getComplaintsDetails("(SELECT complaints.id FROM complaints)");
    } else {
     $term = "'" . implode("','", explode(",", $term)) . "'";
     //$term = "'" . implode("','", explode(",", $term)) . "'";
     //$data["complaints"] = getComplaints("(?)", [$term]);
     $data["complaints"] = getComplaintsDetails("(" . $term . ")");
    }
   }
   //calcStat($data);
   if ($GLOBALS['filetype'] == 'html') {
    printheader('ASA Complaints');
    //print statsList($data);
    print complaintsTable($data["complaints"], "filter");
   } else {
    api($data, 'complaints');
   }
  } else {
   $complaint = getComplaint($term);
   if ($complaint) {
    if ($GLOBALS['filetype'] == 'html') {
     printheader('ASA Complaint ' . $complaint['idslash']);
     print "<h1>Complaint: " . $complaint['idslash'] . "</h1>" . "\n";
     print "<h2>" . $complaint['advert'] . "</h2>" . "\n";
     print '<div class="panel panel-' . ($complaint['success'] ? 'success' : 'danger') . '"><div class="panel-heading"><h3 class="panel-title">Details</h3></div><div class="panel-body">';
     print '<dl class="dl-horizontal">';
     foreach ($complaint['complainants'] as &$complainant) {
      $complainant = '<a href="/complainant/' . urlencode($complainant) . '">' . $complainant . '</a>';
     }
     print '<dt>Complainants</dt><dd>' . implode('<br />', $complaint['complainants']) . '</dd>';
     foreach ($complaint['companies'] as &$company) {
      $company = '<a href="/company/' . urlencode($company) . '">' . $company . '</a>';
     }
     print '<dt>Companies</dt><dd>' . implode('<br />', $complaint['companies']) . '</dd>';
     print '<dt>Year</dt><dd><a href="/year/' . $complaint['year'] . '">' . $complaint['year'] . '</a></dd>';
     foreach ($complaint['media'] as &$medium) {
      $medium = '<a href="/media/' . urlencode($medium) . '">' . $medium . '</a>';
     }
     foreach ($complaint['clauses'] as &$clause) {
      $clause = '<a href="/clause/' . urlencode($clause) . '">' . $clause . '</a>';
     }
     print '<dt>Media</dt><dd>' . implode('<br />', $complaint['media']) . '</dd>';
     print '<dt>Product</dt><dd><a href="/product/' . urlencode($complaint['product']) . '">' . $complaint['product'] . '</a></dd>';
     //print '<dt>Codes</dt><dd>' . implode('<br />', $complaint['codes']) . '</dd>';
     print '<dt>Clauses</dt><dd>' . implode('<br />', $complaint['clauses']) . '</dd>';
     print '<dt>Decision</dt><dd class="' . ($complaint['success'] ? 'success' : 'failure') . '">' . $complaint['decision'] . '</dd>';
     if ($complaint['appealidslash']) {
      print '<dt>Appeal</dt><dd class="' . ($complaint['appealsuccess'] ? 'success' : 'failure') . '">' . $complaint['appealidslash'] . '</dd>';
     }
     print '<dt>ASA Links</dt><dd><a href="http://old.asa.co.nz/display.php?ascb_number=' . $complaint['id'] . '" target="_blank">Website Listing</a><br /><a href="http://old.asa.co.nz/decision_file.php?ascbnumber=' . $complaint['id'] . '" target="_blank">Decision Document</a></dd>';
     print '</dl>';
     print '</div></div>';
     preg_match('/<body(?:.*?)>(.*?)<\/body>/s', file_get_contents('/mnt/media/Unsorted/ASA/files/' . $complaint['year'] . '/html/' . $complaint['id'] . '.html'), $matches);
     if (!$matches) {
      preg_match('/<body(?:.*?)>(.*)/s', file_get_contents('/mnt/media/Unsorted/ASA/files/' . $complaint['year'] . '/html/' . $complaint['id'] . '.html'), $matches);
     }
     if ($matches) {
      $html = make_clickable(iconv('UTF-8', 'ASCII//TRANSLIT', $matches[1]));
      print '<div class="panel panel-info"><div class="panel-heading"><h3 class="panel-title">Document</h3></div><div class="panel-body">' . $html . '</div></div>';
     }
     else
     {
      print "<pre>";
      print_r($complaint['doc']);
      print "</pre>";
     }
    } else {
     api($complaint, 'complaint');
    }
   } else {
    notFound('Complaint');
   }
  }
 }
} else {
 $top = 20;
 $data["complainants"] = complainantsData($top);
 $data["companies"] = companiesData($top);
 $data["products"] = productsData();
 $data["years"] = yearsData();
 $data["media"] = mediaData();
 $data["codes"] = codesData();
 $data["complaints"] = getComplaintsDetails("(SELECT id FROM complaints WHERE meetingdate > (NOW() - INTERVAL 2 MONTH) AND meetingdate < (NOW() + INTERVAL 1 DAY) ORDER BY meetingdate DESC)");
 if ($GLOBALS['filetype'] == 'html') {
  printheader('ASA Complaints');
  print '<div class="row"><div class="col-md-6">';
  print "<h2>Top " . $top . ' <a href="/complainant">Complainants</a></h2>';
  print rankingTable($data["complainants"], "complainant", "http://schema.org/Person", False);
  print '</div><div class="col-md-6">';
  print "<h2>Bottom " . $top . ' <a href="/company">Companies</a></h2>';
  print rankingTable($data["companies"], "company", "http://schema.org/Organization", False);
  print '</div></div>';
  print '<div class="row"><div class="col-md-6">';
  print '<h2>Complaints by <a href="/product">Product</a></h2>';
  print rankingTable($data["products"], "product", "", False, False);
  print '</div><div class="col-md-6">';
  print '<h2>Complaints by <a href="/year">Year</a></h2>';
  print rankingTable($data["years"], "year", "", False, False, True, False);
  print '<h2>Complaints by <a href="/media">Media</a></h2>';
  print rankingTable($data["media"], "media", "", False, False);
  print '<h2>Complaints by <a href="/code">Code</a></h2>';
  print rankingTable($data["codes"], "code", "", False, False);
  print '<h3>See also Complaints by <a href="/clause">Clause</a>...</h3>';
  print '</div></div>';
  print "<h2>Latest Complaints</h2>";
  print complaintsTable($data["complaints"], "sorter", True, True);
 } else {
  api($data, 'complaint');
 }
}
if ($GLOBALS['filetype'] == 'html') {
 printfooter();
}

# Add anchors!

?>