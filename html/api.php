<?php

error_reporting(E_ALL);
ini_set("display_errors", "On");

$GLOBALS['db'] = new PDO('mysql:host=localhost;dbname=ASA2;charset=utf8', 'asa', 'DFZUQeK99SQ33j4X');

$GLOBALS['sbh'] = array('M. Honeychurch', 'M. Hanna', 'L. Taylor', 'D. Ryan', 'C. Gold', 'S. McAuliffe', 'R. Seddon-Smith', 'M. Edmonds', 'C. Atkinson', 'S. Clark', 'M. Willey', 'T. Atkin', 'L. Oldfield', 'K. Honeychurch', 'M. Taylor', 'C. Morgan', 'N. Grange', 'P. Muir', 'A. Gilbey', 'M. Coffey', 'M. Taylor', 'R. Shillito'); # , 'L. Marron'

$GLOBALS['badgedescriptions'] = array("success" => "Your first successful complaint", "warming" => "You have made 5 successful complaints", "addict" => "You have made 10 successful complaints", "unstoppable" => "You have made 25 successful complaints", "monster" => "You have made 50 successful complaints", "centurion" => "You have made 100 successful complaints", "godlike" => "You have made 1,000 successful complaints", "80" => "Over 80% of your complaints are successful", "king" => "You have the most successful complaints", "top10" => "You are in the top 10 of most successful complainants", "two" => "You have complained along with someone else", "three" => "You have complained with two or more others", "bummer" => "You've had an unsuccessful complaint", "close" => "One of your successful complaints was overturned on appeal", "denied" => "You tried to appeal an unsuccessful complaint, and failed", "insistent" => "You appealed an unsuccessful complaint and won", "untouchable" => "You defended a successful complaint against an appeal", "thorn" => "You have complained 3 times about one company", "pita" => "You have complained 5 times about one company", "archnemesis" => "You have complained 10 times about one company", "who" => "You submitted the first ever complaint against a company", "three" => "You have submitted complaints in 3 consecutive years", "half" => "You have submitted complaints in 5 consecutive years", "newyear" => "You submitted the first complaint of the year", "christmas" => "You submitted the last complaint of the year", "skeptical" => "You managed to include the word 'skeptical' (or 'sceptical') in a complaint", "inconceivable" => "You managed to include the word 'inconceivable' in a complaint", "charlatan" => "You managed to include the word 'charlatan' in a complaint", "quackery" => "You managed to include the word 'quackery' in a complaint", "schadenfreude" => "You managed to include the word 'schadenfreude' in a complaint", "cartoon" => "You managed to include an XKCD or SMBC cartoon in a complaint", "fishbarrel" => "You've used Fishbarrel to submit a complaint", "essay" => "You have a complaint document that is over 10,000 words in length", "clauses" => "You complained about an advert that breached 5 rules", "moreclauses" => "You complained about an advert that breached 10 rules", "codes" => "You've used 5 different ASA codes to complain", "products" => "You've used the Therapeutic Products Advertising Code", "services" => "You've used the Therapeutic Services Advertising Code", "ethics" => "You've used the Code of Ethics", "weight" => "You've used the Code for Advertising of Weight Management", "children" => "You've used the Code for Advertising to Children", "comparative" => "You've used the Code for Comparative Advertising", "website" => "You've complained about an Advertiser Website", "television" => "You've complained about a Television advert", "outdoor" => "You've complained about an Outdoor advert", "newspaper" => "You've complained about a Newspaper advert", "magazine" => "You've complained about a Magazine advert", "email" => "You've complained about an Email advert", "dm" => "You've complained about a Direct Marketing advert", "radio" => "You've complained about a Radio advert", "yellow" => "You've complained about a Yellow Pages advert", "multimedia" => "You've complained about adverts in 5 different media");
$GLOBALS['sql']['count'] = "SELECT COUNT(*) AS complaints ";
$GLOBALS['sql']['tables']['complainants'] = "FROM complaints_complainants, complainants, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['companies'] = "FROM complaints_companies, companies, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['media'] = "FROM complaints_media, media, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['products'] = "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id LEFT JOIN products ON products.id = complaints.products_id ";
$GLOBALS['sql']['tables']['years'] = "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['codes'] = "FROM clauses, codes, complaints_clauses, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['tables']['clauses'] = "FROM clauses, codes, complaints_clauses, decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id ";
$GLOBALS['sql']['link']['complainants'] = "WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['companies'] = "WHERE complaints_companies.companies_id = companies.id AND complaints_companies.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['media'] = "WHERE complaints_media.media_id = media.id AND complaints_media.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['products'] = "WHERE complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['years'] = "WHERE complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['codes'] = "WHERE clauses.codes_id = codes.id AND clauses.id = complaints_clauses.clauses_id AND complaints_clauses.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['link']['clauses'] = "WHERE clauses.codes_id = codes.id AND clauses.id = complaints_clauses.clauses_id AND complaints_clauses.complaints_id = complaints.id AND complaints.decisions_id = decisions.id ";
$GLOBALS['sql']['successes'] = "((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1))";
$GLOBALS['sql']['stats']['total'] = "COUNT(*) AS total ";
$GLOBALS['sql']['stats']['win'] = "((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) AS win ";
$GLOBALS['sql']['stats']['draw'] = "(decisions.success IS NULL) AS draw ";
$GLOBALS['sql']['stats']['loss'] = "(1 - ((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) - (decisions.success IS NULL)) AS loss ";
#$GLOBALS['sql']['stats']['percent'] = "(win * 100 / (win + loss)) AS percent ";
$GLOBALS['sql']['filter'] = "AND " . $GLOBALS['sql']['successes'] . " AND complainants.name = ? ";
$GLOBALS['sql']['stats']['all'] = 'SELECT ' . $GLOBALS['sql']['stats']['total'] . ", SUM" . $GLOBALS['sql']['stats']['win'] . ", SUM" . $GLOBALS['sql']['stats']['draw'] . " ";
$GLOBALS['sql']['multiples'] = "(SELECT GROUP_CONCAT(name SEPARATOR '|') FROM codes WHERE id IN (SELECT clauses_id FROM complaints_clauses WHERE complaints_id = complaints.id)) AS codes, (SELECT GROUP_CONCAT(CONCAT(codes.name, ', ', clauses.name) SEPARATOR '|') FROM clauses LEFT JOIN codes ON clauses.codes_id = codes.id WHERE clauses.id IN (SELECT clauses_id FROM complaints_clauses WHERE complaints_id = complaints.id)) AS clauses, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM media WHERE media.id IN (SELECT media_id FROM complaints_media WHERE complaints_id = complaints.id)) AS media, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM complainants WHERE complainants.id IN (SELECT complainants_id FROM complaints_complainants WHERE complaints_id = complaints.id)) AS complainants, (SELECT GROUP_CONCAT(name SEPARATOR '|') FROM companies WHERE companies.id IN (SELECT companies_id FROM complaints_companies WHERE complaints_id = complaints.id)) AS companies ";
$GLOBALS['sql']['complaint'] = "year, advert, CONCAT('http://old.asa.co.nz/display.php?ascb_number=', complaints.id) AS url, decisions.name AS decision, decisions.arbiter, decisions.ruling, decisions.success, appeals.success as appealsuccess, appeals.id as appealid, CONCAT(SUBSTR(appeals.id, 1, 2), '/', SUBSTR(appeals.id, 3)) AS appealidslash, (SELECT IFNULL(name, '[None]') AS name FROM products WHERE id = products_id) AS product, CONCAT('http://old.asa.co.nz/decision_file.php?ascbnumber=', complaints.id) AS docurl, docwords";
$GLOBALS['sql']['list']['complaints']['complainant'] = "(SELECT complaints_id FROM complaints_complainants WHERE complainant_id = (SELECT id FROM complainants WHERE name = ?))";
$GLOBALS['sql']['list']['complaints']['company'] = "(SELECT complaints_id FROM complaints_companies WHERE companies_id = (SELECT id FROM companies WHERE name = ?))";
$GLOBALS['sql']['list']['complaints']['medium'] = "(SELECT complaints_id FROM complaints_media WHERE media_id = (SELECT id FROM media WHERE name = ?))";
//$GLOBALS['sql']['list']['complaints']['clause'] = "(SELECT complaints_id FROM complaints_clauses WHERE clause_id = (SELECT id FROM clauses WHERE name = ?))";


$GLOBALS['multilist'] = ["codes", "clauses", "media", "complainants", "companies"];

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
  <meta property="og:image" content="http://sbh.org.nz/wp-content/uploads/s5square.png" />
  <link rel="shortcut icon" href="http://sbh.org.nz/wp-content/uploads/favicon.ico" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
  <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <style>
   body {background-color: #EAEAEA; padding: 30px 0;}
   @media (min-width: 1200px) {.container {width: 1268px;}}
   #content {background-color: #FFFFFF; box-shadow: rgba(100, 100, 100, 0.298039) 0px 2px 6px 0px;}
   .success {color: #009900;}
   .failure {color: #990000;}
   .badges img {width: 96px; height: 96x;}
   #badgelist img {width: 96px; height: 96x;}
   pre {white-space: pre-wrap; word-break: normal; text-align: justify; padding: 20px;}
   #disclaimer {max-width: 800px; margin: 0 auto; font-size: 10px; text-align: center;}
   #Frame1, #Frame2, #Frame3, #Frame4, #Frame5, .sd-abs-pos {display: none;}
   #title {margin-left: 10px; margin-top: 6px;}
   #title h1 {font-family: 'Lato', sans-serif; font-size: 48px; background: url(http://sbh.org.nz/wp-content/uploads/header.png) no-repeat; min-height: 65px; padding-left: 35px;}
   #title h2 {font-family: 'Lato', sans-serif; margin-top: -20px; margin-left: 35px;}   
   #title h1 a, #title h2 a {color: #000000;}
   #title h1 a:hover, #title h2 a:hover {text-decoration: none; color: #0072BF;}
  </style>
 </head>
 <body>
  <div id="content" class="container">
   <div class="row">
    <div class="col-md-12" id="title">
     <h1><a href="http://sbh.nz/">The Society for Science Based Healthcare</a></h1>
     <h2><a href="http://asa.sbh.nz/">ASA Complaint Statistics</a></h2>
     <form class="pull-right" action="">
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
   <p id="disclaimer">The data used in this site has been taken from the <a href="http://old.asa.co.nz/database.php">ASA's Complaints Database</a>. Some adjustments have been made to improve consistency, such as standardising company and complainant names. We do not guarantee the accuracy of this data. If you find any errors, please let us know by emailing us at <a href="mailto:sbh@sbh.nz">sbh@sbh.nz</a>.</p>
  </div>
 </body>
</html>
<?php
}



function getData($query, $vars = array()) {
 $stmt = $GLOBALS['db']->prepare($query);
 $stmt->execute($vars);
 if (__FILE__ == "/var/www/html/asa/test.php") {
  print '<pre>';
  print $query . "\n";
  print_r($vars);
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
 if (in_array(substr($url, -1), array('.', ',', ';', ':')) === true) {
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
 if (in_array(substr($dest, -1), array('.', ',', ';', ':')) === true) {
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
 $totals = getData("SELECT " . $GLOBALS['sql']['stats']['total'] . ", SUM" . $GLOBALS['sql']['stats']['win'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . "AND complainants.name = ?", array($complainant))[0];
 if ($totals['total'] >= 1) {
  $badges['success'] = ($totals['win'] >= 1 ? 1 : 0);
  $badges['warming'] = ($totals['win'] >= 5 ? 1 : 0);
  $badges['addict'] = ($totals['win'] >= 10 ? 1 : 0);
  $badges['unstoppable'] = ($totals['win'] >= 25 ? 1 : 0);
  $badges['monster'] = ($totals['win'] >= 50 ? 1 : 0);
  $badges['centurion'] = ($totals['win'] >= 100 ? 1 : 0);
  $badges['godlike'] = ($totals['win'] >= 1000 ? 1 : 0);
  $badges['80'] = ($totals['win'] / $totals['total'] >= 0.8 ? 1 : 0);
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
  $badges['close'] = (getData($query . "AND appeals.success = 1 " . $GLOBALS['sql']['filter'], array($complainant))[0]['total'] >= 1 ? 1 : 0);
  $badges['denied'] = (getData($query . "AND decisions.success = 0 AND appeals.success = 0 AND complainants.name = ?", array($complainant))[0]['total'] >= 1 ? 1 : 0);
  $badges['insistent'] = (getData($query . "AND decisions.success = 0 AND appeals.success = 1 AND complainants.name = ?", array($complainant))[0]['total'] >= 1 ? 1 : 0);
  $badges['untouchable'] = (getData($query . "AND appeals.success = 0 " . $GLOBALS['sql']['filter'], array($complainant))[0]['total'] >= 1 ? 1 : 0);
  $idquery = "(SELECT complaints.id " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND ((decisions.success = 1 AND decisions.ruling LIKE '%Settled%') OR (decisions.success = 1 AND IFNULL(appeals.success, 0) = 0) OR (decisions.success = 0 AND IFNULL(appeals.success, 0) = 1)) = 1 AND complainants.name = ?)";
  $words = array('skeptical' => array('skeptical', 'sceptical'), 'inconceivable' => array('inconceivable'), 'charlatan' => array('charlatan'), 'quackery' => array('quackery'), 'schadenfreude' => array('schadenfreude'), 'cartoon' => array('http://xkcd.com/', 'http://www.smbc-comics.com/'), 'fishbarrel' => array('Claim found at http'));
  foreach ($words as $word => $values) {
   $badges[$word] = (getData("SELECT COUNT(*) as win FROM docs WHERE complaints_id IN " . $idquery . " AND contents REGEXP ?", array($complainant, implode("|", $values)))[0]['win'] >= 1 ? 1 : 0);
  }
  $badges['essay'] = (getData($query . "AND docwords >= 10000 " . $GLOBALS['sql']['filter'], array($complainant))[0]['total'] >= 1 ? 1 : 0);
  $clauses = getData("SELECT COUNT(*) AS clauses " . $GLOBALS['sql']['tables']['complainants'] .", complaints_clauses, clauses " . $GLOBALS['sql']['link']['complainants'] . "AND complaints_clauses.complaints_id = complaints.id AND clauses.id = complaints_clauses.clauses_id " . $GLOBALS['sql']['filter'] . "GROUP BY complaints.id ORDER BY clauses DESC LIMIT 1", array($complainant))[0]['clauses'];
  $badges['clauses'] = ($clauses >= 5 ? 1 : 0);
  $badges['moreclauses'] = ($clauses >= 10 ? 1 : 0);
  $codes = array("Therapeutic Products Advertising Code" => "products", "Therapeutic Services Advertising Code" => "services", "Code of Ethics" => "ethics", "Code for Advertising of Weight Management" => "weight", "Code for Advertising to Children" => "children", "Code for Comparative Advertising" => "comparative");
  $codecount = 0;
  $codelist = getData("SELECT DISTINCT codes.name AS code FROM complaints_complainants, complainants, complaints, decisions, complaints_clauses, clauses, codes WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id AND complaints_clauses.complaints_id = complaints.id AND clauses.id = complaints_clauses.clauses_id AND codes.id = clauses.codes_id AND decisions.success = 1 AND complainants.name = ?", array($complainant));
  if ($codelist) {
   foreach($codelist as $code) {
    if (array_key_exists($code['code'], $codes)) {
     $badges[$codes[$code['code']]] = 1;
    }
    $codecount++;
   }
  }
  $badges['codes'] = ($codecount >= 5 ? 1 : 0);
  $media = array("Advertiser Website" => "website", "Television" => "television", "Outdoor" => "outdoor", "Newspaper" => "newspaper", "Magazine" => "magazine", "Email" => "email", "DM - Unaddressed" => "dm", "Radio" => "radio", "Yellow Pages" => "yellow");
  $mediacount = 0;
  if ($medialist = getData("SELECT DISTINCT media.name AS medium FROM complaints_complainants, complainants, complaints, decisions, complaints_media, media WHERE complaints_complainants.complainants_id = complainants.id AND complaints_complainants.complaints_id = complaints.id AND complaints.decisions_id = decisions.id AND complaints_media.complaints_id = complaints.id AND media.id = complaints_media.media_id AND decisions.success = 1 AND complainants.name = ?", array($complainant))) {
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

function getComplaints($filter, $array = array(), $win = False, $upheld = False) {
 if ($complaintlist = getData("SELECT complaints.id as id, CONCAT(SUBSTR(complaints.id, 1, 2), '/', SUBSTR(complaints.id, 3)) AS idslash, advert, meetingdate, IFNULL(products.name, '[None]') as product, " . $GLOBALS['sql']['stats']['win'] . ", " . $GLOBALS['sql']['stats']['draw'] . " FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id LEFT JOIN products ON products.id = complaints.products_id WHERE complaints.decisions_id = decisions.id " . ($win ? " AND decisions.success = 1 AND IFNULL(appeals.success, 0) = 0 " : "") . ($upheld ? " AND decisions.ruling LIKE '%Upheld%' " : "") . " AND complaints.id IN " . $filter . " ORDER BY complaints.meetingdate DESC, complaints.id DESC;", $array)) {
  foreach ($complaintlist as $complaint) {
   $complaints[$complaint["id"]] = $complaint;
   unset($complaints[$complaint["id"]]["id"]);
  }
  return $complaints;
 }
}

function getComplaint($id) {
 if ($complaint = getData("SELECT complaints.id as id, CONCAT(SUBSTR(complaints.id, 1, 2), '/', SUBSTR(complaints.id, 3)) AS idslash, " . $GLOBALS['sql']['stats']['win'] . ", " . $GLOBALS['sql']['stats']['draw'] . ', ' . $GLOBALS['sql']['complaint'] . ", " . $GLOBALS['sql']['multiples'] . "FROM decisions, complaints LEFT JOIN appeals ON complaints.id = appeals.complaints_id WHERE complaints.decisions_id = decisions.id AND complaints.id = ?", array($id))) {
  $complaint = $complaint[0];
  foreach ($GLOBALS['multilist'] as $multi) {
   $complaint[$multi] = explode("|", $complaint[$multi]);
  }
  return $complaint;
 }
}

function complainantsTable($limit = 0, $alpha = False) {
 rankingTable(calcStats(getData($GLOBALS['sql']['stats']['all'] . ", complainants.name " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name NOT IN ('Other', 'Others') GROUP BY complainants.name ORDER BY " . ($alpha ? "" : "win DESC, total ASC, ") . "complainants.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";")), "complainant");
}

function companiesTable($limit = 0, $alpha = False) {
 rankingTable(calcStats(getData($GLOBALS['sql']['stats']['all'] . ", companies.name " . $GLOBALS['sql']['tables']['companies'] . $GLOBALS['sql']['link']['companies'] . " AND companies.name != 'Advertiser unknown' GROUP BY companies.name ORDER BY " . ($alpha ? "" : "win DESC, total ASC, ") . "companies.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";")), "company");
}

function sbhTable($limit = 0, $alpha = False) {
 rankingTable(calcStats(getData($GLOBALS['sql']['stats']['all'] . ", complainants.name " . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "') GROUP BY complainants.name ORDER BY " . ($alpha ? "" : "win DESC, total ASC, ") . "complainants.name ASC" . ($limit ? " LIMIT " . $limit : "") . ";")), "complainant");
}

function complaintsTable($complaints) {
 if (is_array($complaints)) {
  $columns = array("ID", "Advert");
  print '<div class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">Complaints</h3></div>';
  print '<table class="table">' . "\n";
  print "<tr><th>ID</th><th>Advert</th><th>Product</th><th>Meeting Date</th></tr>" . "\n";
  foreach ($complaints as $id => $complaint) {
   print '<tr' . ($complaint['win'] ? ' class="success"' : ($complaint['draw'] ? ' class="warning"' : ' class="danger"')) . '><td><a href="?complaint=' . $id . '">' . $complaint['idslash'] . '</a></td><td><a href="?complaint=' . $id . '">' . $complaint['advert'] . '</a></td><td>' . $complaint['product'] . '</td><td>' . $complaint['meetingdate'] . '</td></tr>' . "\n";
  }
  print "</table></div>" . "\n";
 }
}

function yearsData() {
 return calcStats(getData($GLOBALS['sql']['stats']['all'] . ", year as name " . $GLOBALS['sql']['tables']['years'] . $GLOBALS['sql']['link']['years'] . " GROUP BY year ORDER BY year DESC;"));
}

function codesData() {
 return calcStats(getData($GLOBALS['sql']['stats']['all'] . ", codes.name " . $GLOBALS['sql']['tables']['codes'] . $GLOBALS['sql']['link']['codes'] . "  GROUP BY codes.name ORDER BY total DESC;"));
}

function clausesData() {
 return calcStats(getData($GLOBALS['sql']['stats']['all'] . ", CONCAT(codes.name, ', ', clauses.name) AS name " . $GLOBALS['sql']['tables']['clauses'] . $GLOBALS['sql']['link']['clauses'] . "  GROUP BY clauses.id ORDER BY total DESC;"));
}

function mediaData() {
 return calcStats(getData($GLOBALS['sql']['stats']['all'] . ", media.name " . $GLOBALS['sql']['tables']['media'] . $GLOBALS['sql']['link']['media'] . " GROUP BY media.name ORDER BY total DESC;"));
}

function productsData() {
 return calcStats(getData($GLOBALS['sql']['stats']['all'] . ", IFNULL(products.name, '[None]') as name " . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " GROUP BY products.name ORDER BY total DESC;"));
}

function calcStats($stats) {
 foreach ($stats as &$stat) {
  $stat['loss'] = $stat['total'] - $stat['win'] - $stat['draw'];
  $stat['percent'] = ($stat['win'] == 0 ? 0 : round(($stat['win'] * 100) / ($stat['win'] + $stat['loss']), 0));
 }
 return $stats;
}

function rankingTable($data, $type, $detailed = True, $link = True, $rank = True) {
 print '<table class="table">' . "\n";
 print "<tr>" . ($rank ? "<th>Rank</th>" : "") . "<th>Name</th>" . ($detailed ? "<th>Success</th><th>Success %</th><th>Failed</th><th>Other</th><th>Total</th></tr>" : "<th>Complaints</th>") . "\n";
 $position = 1;
 foreach ($data as $item) {
  print '<tr>' . ($rank ? '<td>' . $position . '.</td>' : "") . '<td>' . ($link ? '<a href="?' . $type . '=' . urlencode($item['name']) . '">' . $item['name'] . '</a>' : $item['name']) . '</td>' . ($detailed ? '<td>' . $item['win'] . "</td><td>" . $item['percent'] . "%</td><td>" . $item['loss'] . "</td><td>" . $item['draw'] . "</td>" : "") . "<td>" . $item['total'] . "</td></tr>" . "\n";
  $position++;
 }
 print "</table>" . "\n";
}

function statsList($data) {
 $stats = calcStats($data)[0];
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
 print $type . "<h1> Not Found</h1>" . "\n";
}

if ($_GET) {
 if (array_key_exists('charts', $_GET)) {
  
 } elif (array_key_exists('search', $_GET)) {
  printheader('ASA Search: ' . $_GET['search']);
  print "<h1>Search Results for " . $_GET['search'] . "</h1>" . "\n";
  if (array_key_exists('upheld', $_GET)) {
   complaintsTable(getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", array('%' . urldecode($_GET['search']) . '%'), True, True));  
  } elseif (array_key_exists('success', $_GET)) {
   complaintsTable(getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", array('%' . urldecode($_GET['search']) . '%'), True));  
  } else {
   complaintsTable(getComplaints("(SELECT complaints_id FROM docs WHERE contents LIKE ?)", array('%' . urldecode($_GET['search']) . '%')));
  }
 } elseif (array_key_exists('companies', $_GET)) {
  if (array_key_exists('alpha', $_GET)) {
   printheader('ASA Companies');
   print '<h1>ASA Companies</h1>';
   companiesTable(0, True);
  } else {
   printheader('ASA Company Rankings');
   print '<h1>ASA Companies 2008-2015</h1>';
   companiesTable();
  }
 } elseif (array_key_exists('complainants', $_GET)) {
  if (array_key_exists('alpha', $_GET)) {
   printheader('ASA Complainants');
   print '<h1>ASA Complainants</h1>';
   complainantsTable(0, True);
  } else {
   printheader('ASA Complainant Rankings');
   print '<h1>ASA Complainants 2008-2015</h1>';
   complainantsTable();
  }
 } elseif (array_key_exists('sbh', $_GET)) {
  printheader('ASA SBH Complainant Rankings');
  if (array_key_exists('all', $_GET)) {
   print "<h2>SBH Member Complaints</h2>";
   complaintsTable(getComplaints("(SELECT id FROM complaints WHERE id IN (SELECT complaints_id FROM complaints_complainants WHERE complainants_id IN (SELECT id FROM complainants WHERE complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "'))) ORDER BY meetingdate DESC)"));
  } else {
   print '<h1>SBH Related Complaints</h1>';
   print statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "')"));
   print '<div class="row"><div class="col-md-9">';
   print '<h2>Society Members</h2>';
   sbhTable();
   print '</div><div class="col-md-3">';
   print '<h2>Useful Pages</h2><ul>';
   $topics = array('Therapeutic Products (Code)' => '?code=Therapeutic+Products+Advertising+Code', 'Therapeutic Services (Code)' => '?code=Therapeutic+Services+Advertising+Code', 'Therapeutic (Product)' => '?product=Therapeutic', 'Homeopathy (Search)' => '?search=homeopath', 'Acupuncture (Search)' => '?search=acupunctur', 'Chiropractic (Search)' => '?search=chiropract', 'Cancer (Search)' => '?search=cancer', 'Arthritis (Search)' => '?search=arthritis', 'BioMag (Company)' => '?company=Woolrest+Biomag', 'Bioptron (Company)' => '?company=Bioptron', 'Appeals' => '?appeals');
   foreach ($topics as $topic => $url) {
    print '<li><a href="' . $url . '">' . $topic . '</a></li>';
   }
   print '</ul>';
   print '<h2>External Links</h2><ul>';
   $topics = array('ASA Website' => 'http://www.asa.co.nz/', 'Online Complaint Form' => 'http://www.asa.co.nz/complaints/make-a-complaint/', 'Therapeutic Products Code' => 'http://www.asa.co.nz/codes/codes/therapeutic-products-advertising-code/', 'Therapeutic Services Code' => 'http://www.asa.co.nz/codes/codes/therapeutic-services-advertising-code/', 'Ethics Code' => 'http://www.asa.co.nz/codes/codes/advertising-code-of-ethics/', 'Appeals Process' => 'http://www.asa.co.nz/decisions/the-appeals-process/', 'Recent Decisions' => 'http://old.asa.co.nz/decisions_to_media.php', 'Medicines Act 1981' => 'http://www.legislation.govt.nz/act/public/1981/0118/latest/whole.html#DLM53790', 'TAPS "Weasel" Words' => 'http://www.anza.co.nz/Section?Action=View&Section_id=45');
   foreach ($topics as $topic => $url) {
    print '<li><a href="' . $url . '" target="_blank">' . $topic . '</a></li>';
   }
   print '</ul>';
   print '</div></div>';
   print '<h2>Latest <a href="?sbh&all">Member Complaints</a></h2>';
   complaintsTable(getComplaints("(SELECT id FROM complaints WHERE meetingdate > (NOW() - INTERVAL 6 MONTH) AND meetingdate < (NOW() + INTERVAL 1 DAY) AND id IN (SELECT complaints_id FROM complaints_complainants WHERE complainants_id IN (SELECT id FROM complainants WHERE complainants.name IN ('" . implode("', '", $GLOBALS['sbh'])  . "'))) ORDER BY meetingdate DESC)"));
  }
 } elseif (array_key_exists('complainant', $_GET)) {
  $complainant = urldecode($_GET['complainant']);
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['complainants'] . $GLOBALS['sql']['link']['complainants'] . " AND complainants.name = ?", array($complainant)));
  if ($stats) {
   printheader('Complainant: ' . $complainant);
   print "<h1>Complainant: " . $complainant . "</h1>" . "\n";
   print $stats;
   print '<div class="panel panel-info badges"><div class="panel-heading"><h3 class="panel-title">Badges</h3></div><div class="panel-body">';
   $badges = badges($complainant);
   if ($badges) {
    foreach ($badges as $badge) {
     //print "<li>" . $badge . "</li>" . "\n";
     print '<img src="badges/' . $badge . '.png" alt="' . ucwords($badge) . '" title="' . $GLOBALS['badgedescriptions'][$badge] . '" />' . "\n";
    }
   }
   print "</div></div>" . "\n";
   complaintsTable(getComplaints("(SELECT complaints_id FROM complaints_complainants WHERE complainants_id = (SELECT id FROM complainants WHERE name = ?))", array($complainant)));
  } else {
   notFound('Complainant');
  }
 } elseif (array_key_exists('badges', $_GET)) {
  printheader('List of Badges');
  print "<h1>List of Badges</h1>" . "\n";
  print '<div id="badgelist">';
  foreach ($GLOBALS['badgedescriptions'] as $badge => $description) {
   print '<img src="badges/' . $badge . '.png" alt="' . ucwords($badge) . '" />' . $description . "<br />\n";
  }
  print '</div>';
 } elseif (array_key_exists('products', $_GET)) {
  printheader('Complaints by Product');
  print "<h1>Complaints by Product</h1>" . "\n";
  rankingTable(productsData(), "product");
 } elseif (array_key_exists('medias', $_GET)) {
  printheader('Complaints by Media');
  print "<h1>Complaints by Media</h1>" . "\n";
  rankingTable(mediaData(), "media");
 } elseif (array_key_exists('codes', $_GET)) {
  printheader('Complaints by Code');
  print "<h1>Complaints by Code</h1>" . "\n";
  rankingTable(codesData(), "code");
 } elseif (array_key_exists('clauses', $_GET)) {
  printheader('Complaints by Clause');
  print "<h1>Complaints by Clause</h1>" . "\n";
  rankingTable(clausesData(), "clause");
 } elseif (array_key_exists('years', $_GET)) {
  printheader('Complaints by Year');
  print "<h1>Complaints by Year</h1>" . "\n";
  rankingTable(yearsData(), "year", True, True, False);
 } elseif (array_key_exists('company', $_GET)) {
  $company = urldecode($_GET['company']);
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['companies'] . $GLOBALS['sql']['link']['companies'] . " AND companies.name = ?", array($company)));
  if ($stats) {
   printheader('Company: ' . $company);
   print "<h1>Company: " . $company . "</h1>" . "\n";
   print $stats;
   complaintsTable(getComplaints("(SELECT complaints_id FROM complaints_companies WHERE companies_id = (SELECT id FROM companies WHERE name = ?))", array($company)));
  } else {
   notFound('Company');
  }
 } elseif (array_key_exists('appeals', $_GET)) {
  printheader('Appeals');
  print "<h1>Appeals</h1>" . "\n";
  print "<h2>Unsuccessful Complaints Overturned</h2>" . "\n";
  complaintsTable(getComplaints("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 0 AND appeals.success = 1)"));
  print "<h2>Successful Complaints Defended</h2>" . "\n";
  complaintsTable(getComplaints("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 1 AND appeals.success = 0)"));
  print "<h2>Successful Complaints Overturned</h2>" . "\n";
  complaintsTable(getComplaints("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 1 AND appeals.success = 1)"));
  print "<h2>Unsuccessful Complaints Defended</h2>" . "\n";
  complaintsTable(getComplaints("(SELECT complaints.id FROM complaints, decisions, appeals WHERE complaints.decisions_id = decisions.id AND complaints.id = appeals.complaints_id AND decisions.success = 0 AND appeals.success = 0)"));
 } elseif (array_key_exists('media', $_GET)) {
  $media = urldecode($_GET['media']);
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['media'] . $GLOBALS['sql']['link']['media'] . " AND media.name = ?", array($media)));
  if ($stats) {
   printheader('Media: ' . $media);
   print "<h1>Media: " . $media . "</h1>" . "\n";
   print $stats;
   complaintsTable(getComplaints("(SELECT complaints_id FROM complaints_media WHERE media_id = (SELECT id FROM media WHERE name = ?))", array($media)));
  } else {
   notFound('Media');
  }
 } elseif (array_key_exists('code', $_GET)) {
  $code = urldecode($_GET['code']);
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['codes'] . $GLOBALS['sql']['link']['codes'] . " AND codes.name = ?", array($code)));
  if ($stats) {
   printheader('Code: ' . $code);
   print "<h1>Code: " . $code . "</h1>" . "\n";
   print $stats;
   complaintsTable(getComplaints("(SELECT DISTINCT complaints_id FROM complaints_clauses WHERE clauses_id IN (SELECT id FROM clauses WHERE codes_id = (SELECT id FROM codes WHERE name = ?)))", array($code)));
  } else {
   notFound('Code');
  }
 } elseif (array_key_exists('clause', $_GET)) {
  $fullclause = urldecode($_GET['clause']);
  $clause = explode(", ", $fullclause);
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['clauses'] . $GLOBALS['sql']['link']['clauses'] . " AND codes.name = ? AND clauses.name = ?", $clause));
  if ($stats) {
   printheader('Clause: ' . $fullclause);
   print "<h1>Clause: " . $fullclause . "</h1>" . "\n";
   print $stats;
   complaintsTable(getComplaints("(SELECT DISTINCT complaints_id FROM complaints_clauses WHERE clauses_id = (SELECT id FROM clauses WHERE codes_id = (SELECT id FROM codes WHERE name = ?) AND name = ?))", $clause));
  } else {
   notFound('Clause');
  }
 } elseif (array_key_exists('product', $_GET)) {
  $product = urldecode($_GET['product']);
  if ($product == '[None]') {
   $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " AND products.name IS NULL"));
  } else {  
   $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['products'] . $GLOBALS['sql']['link']['products'] . " AND products.name = ?", array($product)));
  }
  if ($stats) {
   printheader('Product: ' . $product);
   print "<h1>Product: " . $product . "</h1>" . "\n";
   print $stats;
   if ($product == '[None]') {
    complaintsTable(getComplaints("(SELECT id FROM complaints WHERE products_id IS NULL)"));
   } else {  
    complaintsTable(getComplaints("(SELECT id FROM complaints WHERE products_id = (SELECT id FROM products WHERE name = ?))", array($product)));
   }
  } else {
   notFound('Product');
  }
 } elseif (array_key_exists('year', $_GET)) {
  $stats = statsList(getData($GLOBALS['sql']['stats']['all'] . $GLOBALS['sql']['tables']['years'] . $GLOBALS['sql']['link']['years'] . " AND year = ?", array($_GET['year'])))[0];
  if ($stats) {
   printheader('Year: ' . $_GET['year']);
   print "<h1>Year: " . $_GET['year'] . "</h1>" . "\n";
   print $stats;
   complaintsTable(getComplaints("(SELECT id FROM complaints WHERE year = ?)", array($_GET['year'])));
  } else {
   notFound('Year');
  }
 } elseif (array_key_exists('complaint', $_GET)) {
  $complaint = getComplaint($_GET['complaint']);
  if ($complaint) {
   printheader('ASA Complaint ' . $complaint['idslash']);
   print "<h1>Complaint: " . $complaint['idslash'] . "</h1>" . "\n";
   print "<h2>" . $complaint['advert'] . "</h2>" . "\n";
   print '<div class="panel panel-' . ($complaint['success'] ? 'success' : 'danger') . '"><div class="panel-heading"><h3 class="panel-title">Details</h3></div><div class="panel-body">';
   print '<dl class="dl-horizontal">';
   foreach ($complaint['complainants'] as &$complainant) {
    $complainant = '<a href="?complainant=' . urlencode($complainant) . '">' . $complainant . '</a>';
   }
   print '<dt>Complainants</dt><dd>' . implode('<br />', $complaint['complainants']) . '</dd>';
   foreach ($complaint['companies'] as &$company) {
    $company = '<a href="?company=' . urlencode($company) . '">' . $company . '</a>';
   }
   print '<dt>Companies</dt><dd>' . implode('<br />', $complaint['companies']) . '</dd>';
   print '<dt>Year</dt><dd><a href="?year=' . $complaint['year'] . '">' . $complaint['year'] . '</a></dd>';
   foreach ($complaint['media'] as &$medium) {
    $medium = '<a href="?media=' . urlencode($medium) . '">' . $medium . '</a>';
   }
   foreach ($complaint['clauses'] as &$clause) {
    $clause = '<a href="?clause=' . urlencode($clause) . '">' . $clause . '</a>';
   }
   print '<dt>Media</dt><dd>' . implode('<br />', $complaint['media']) . '</dd>';
   print '<dt>Product</dt><dd><a href="?product=' . urlencode($complaint['product']) . '">' . $complaint['product'] . '</a></dd>';
   //print '<dt>Codes</dt><dd>' . implode('<br />', $complaint['codes']) . '</dd>';
   print '<dt>Clauses</dt><dd>' . implode('<br />', $complaint['clauses']) . '</dd>';
   print '<dt>Decision</dt><dd class="' . ($complaint['success'] ? 'success' : 'failure') . '">' . $complaint['decision'] . '</dd>';
   if ($complaint['appealidslash']) {
    print '<dt>Appeal</dt><dd class="' . ($complaint['appealsuccess'] ? 'success' : 'failure') . '">' . $complaint['appealidslash'] . '</dd>';
   }
   print '<dt>ASA Links</dt><dd><a href="http://old.asa.co.nz/display.php?ascb_number=' . $complaint['id'] . '">Website Listing</a><br /><a href="http://old.asa.co.nz/decision_file.php?ascbnumber=' . $complaint['id'] . '">Decision Document</a></dd>';
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
    //print_r($complaint);
    print_r($complaint['doc']);
    print "</pre>";
   }
  } else {
   notFound('Complaint');
  }
 }
} else {
 printheader('ASA Complaints');
 $top = 20;
 print '<div class="row"><div class="col-md-6">';
 print "<h2>Top " . $top . ' <a href="?complainants">Complainants</a></h2>';
 complainantsTable($top);
 print '</div><div class="col-md-6">';
 print "<h2>Bottom " . $top . ' <a href="?companies">Companies</a></h2>';
 companiesTable($top);
 print '</div></div>';

 print '<div class="row"><div class="col-md-6">';
 print '<h2>Complaints by <a href="?products">Product</a></h2>';
 rankingTable(productsData(), "product", False);
 print '</div><div class="col-md-6">';
 print '<h2>Complaints by <a href="?years">Year</a></h2>';
 rankingTable(yearsData(), "year", False, True, False);
 print '<h2>Complaints by <a href="?medias">Media</a></h2>';
 rankingTable(mediaData(), "media", False);
 print '<h2>Complaints by <a href="?codes">Code</a></h2>';
 rankingTable(codesData(), "code", False);
 print '<h3>See also Complaints by <a href="?clauses">Clause</a>...</h3>';
 print '</div></div>';

 print "<h2>Latest Complaints</h2>";
 complaintsTable(getComplaints("(SELECT id FROM complaints WHERE meetingdate > (NOW() - INTERVAL 2 MONTH) AND meetingdate < (NOW() + INTERVAL 1 DAY) ORDER BY meetingdate DESC)"));
}
printfooter();
?>