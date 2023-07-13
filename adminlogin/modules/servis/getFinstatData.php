<?php
header('Content-Type: application/json');

require '../../../class-autoloader.php';
$aResult = array();

if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

if( !isset($aResult['error']) ) {

    switch($_POST['functionname']) {
        case 'getDataByICO':
           if(count($_POST['arguments']) != 1) {
             var_dump(count($_POST['arguments']));
               $aResult['result'] = 'Error in arguments!';
           } else {
               $aResult['result'] = getDataByICO($_POST['arguments']);
           }
           break;

        default:
           $aResult['result'] = 'Not found function '.$_POST['functionname'].'!';
           break;
    }

}
echo json_encode($aResult);

function getDataByICO ($ico){
  $search = "https://www.finstat.sk/$ico";
  $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

  $options = array(
    CURLOPT_POST           =>false,        //set to GET
    CURLOPT_USERAGENT      => $user_agent, //set user agent
    //CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
    //CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
    CURLOPT_RETURNTRANSFER => true,     // return web page
    CURLOPT_HEADER         => false,    // don't return headers
    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
    CURLOPT_ENCODING       => "",       // handle all encodings
    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
    CURLOPT_TIMEOUT        => 120,      // timeout on response
    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    CURLOPT_URL 		   => $search,
   );
  $ch = curl_init();
  curl_setopt_array( $ch, $options);
  $buffer = curl_exec($ch);
  curl_close($ch);
  if (empty($buffer)){
      print "Nothing returned from url.<p>";
      return "IČO nebolo nájdetené";
  }
  else{
    $html = new DOMDocument();
    $html->loadHTML(mb_convert_encoding($buffer, 'HTML-ENTITIES', 'UTF-8'));
    $name = $html->getElementsByTagName("h1");
    foreach ($name as $n) {
      if(strpos($n->nodeValue, "Hľadajte osobu vo firmách")>-1){
        return "IČO nebolo nájdetené";
      }
      $data['name'] = (strpos($n->nodeValue, "(Historický názov:")>-1)?substr($n->nodeValue,0,strpos($n->nodeValue, "(Historický názov:")):$n->nodeValue;
    }

    while(substr($data['name'],0,1)=="\r" || substr($data['name'],0,1)=="\n" ||substr($data['name'],0,1)==" "){
      $data['name'] = substr($data['name'],1);
    }
    while(substr($data['name'],-1)=="\r" || substr($data['name'],-1)=="\n" || substr($data['name'],-1)==" "){
      $data['name'] = substr($data['name'],0,-1);
    }
    $nodes = $html->getElementsByTagName("li");
    foreach ($nodes as $node) {
      //var_dump($node->nodeValue);
      if(strpos($node->nodeValue, "IČO")>-1){
        $data['ico'] = substr($node->nodeValue, 5);
      }
      if(strpos($node->nodeValue, "DIČ")>-1){
        $data['dic'] = substr($node->nodeValue, 5);
      }
      if(strpos($node->nodeValue, "IČ DPH")>-1){
        if (strpos($node->nodeValue, ",")>-1){
          $end = strpos($node->nodeValue, ",");
        }
        $data['ic_dph'] = (isset($end))?substr($node->nodeValue, 8,$end-8):substr($node->nodeValue, 8);
      }
      if(strpos($node->nodeValue, "Sídlo ")>-1){
        $sidlo = substr($node->nodeValue, 7+strlen($data['name']));
        $sidlo = preg_split("/ /",$sidlo);
        $i = sizeof($sidlo)-1;
        $data['psc'] = $sidlo[$i-2]." ".$sidlo[$i-1];
        $data['mesto'] = $sidlo[$i];
        unset($sidlo[$i-2]);
        unset($sidlo[$i-1]);
        unset($sidlo[$i]);
        $data['adresa'] = implode($sidlo, " ");
      }
    }
    if(empty($data)) return "IČO nebolo nájdetené";
    else return $data;
  }
}
?>
