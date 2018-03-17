<?php
#GeekLabs Simple GPIO control webserver with API. 
#Web interface 
$mode = dt($_REQUEST['mode']) ; 
$action = dt($_REQUEST['action']) ; 
global $gpios ; 
#$gpios = array('5','6','13','19','26','16','20','21') ; 
#defined the GPIO's used for buttons. 0...7
include("settings.php") ; #and one file for all config


if(empty($mode)) { 
 $style="width:150px;color:#000000;border:1px solid #006600;font-size:large;" ; 
 $style2="width:360px;color:#000000;border:1px solid #006600;background:#dddd00;font-size:large;" ; 
print <<<EOF
<script>
function apirelay(button,action) {
    var what = '' ;      
    var xmlhttp;
    try{
        // Opera 8.0+, Firefox, Safari
        xmlhttp = new XMLHttpRequest();
    } catch (e){
        // Internet Explorer Browsers
        try{
            xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e){
                // Something went wrong
                alert("Your browser does not support this functionality");
                return false;
            }
        }
    }  
    xmlhttp.onreadystatechange = function(){
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200){
            if (xmlhttp.responseText.length > 1) {
               var statusarray = JSON.parse(xmlhttp.responseText);
               for (var b in statusarray) {
                  //alert(b + ' = ' + statusarray[b]) ; 
                  if(statusarray[b] == '1') { 
                      document.getElementById(b).style.backgroundColor = '#66FF66';
                  } else { 
                      document.getElementById(b).style.backgroundColor = '#EEEEEE';
                  } ; 
                }
            }
        }
    }  
    var queryString = "mode=api&button=" + button + "&action=" + action + "&end=near" ; 
    xmlhttp.open("GET", "?" + queryString, true);
    xmlhttp.send();
} ;   
</script>
<body style="font-family:verdana,sans-serif;color:#000000;font-size;large;">
<b>Test Virtual Scan Button Pusher</B></b>
<table width=400><tr><td colspan=3>
<button id=B99 name=B99 onClick="apirelay('99','alloff')" style="$style2">ALL OFF</button>
</td><tr>
<tr><td>
<button id=B0 name=B0 onClick="apirelay('0','momentary')" style="$style">Move to Scan</button>
</td><td width=25></td><td>
<button id=B1 name=B1 onClick="apirelay('1','momentary')" style="$style">Stop Move</button> 
</td></tr>
<tr><td>
<button id=B2 name=B2 onClick="apirelay('2','momentary')" style="$style">Start Scan</button> 
</td><td></td><td>
<button id=B3 name=B3 onClick="apirelay('3','momentary')" style="$style">Pause Scan</button> 
</td></tr>
<tr><td>
<button id=B4 name=B4 onClick="apirelay('4','momentary')" style="$style">Stop Scan</button> 
</td><td></td><td>
<button id=B5 name=B5 onClick="apirelay('5','momentary')" style="$style">PC</button> 
</td></tr>
<tr><td>
<button id=B6 name=B6 onClick="apirelay('6','momentary')" style="$style">Unused</button> 
</td><td></td><td>
<button id=B7 name=B7 onClick="apirelay('7','ptt')" style="$style">Push to Talk</button> 
</td></tr>
</table>
EOF;
} ; 
#useful for testing
#<A HREF="?mode=api&test=yes" TARGET="API">API Test</A>
#<A HREF="?mode=api&action=ptt&button=7" TARGET="API">API PTT Test</A>
#<A HREF="?mode=api&action=momentary&button=0" TARGET="API">API Momentary</A>

if($mode == 'api') { 
if(!empty($_REQUEST['action'])) { 
 $action = dt($_REQUEST['action']) ; 
 $button = intval($_REQUEST['button']) ; 
 $gpio = $gpios["$button"] ; 
  if($action == 'alloff') { 
   foreach($gpios as $gpio) {
    file_put_contents("/sys/class/gpio/gpio$gpio/value", 0);
   } ; 
  } ; 
  $i = 0 ; 
  foreach($gpios as $gpio) { 
    $fh = fopen("/sys/class/gpio/gpio$gpio/value", "rb"); #read each gpio by the array list 
    list($B[$i]) = fscanf($fh, "%s");
    fclose($fh) ; 
    $i++ ; 
   } ; 
  $gpio = $gpios["$button"] ; 
  if($action == 'momentary') { 
   file_put_contents("/sys/class/gpio/gpio$gpio/value", 1);
   usleep(200000) ; #200000 = .2 sec
   file_put_contents("/sys/class/gpio/gpio$gpio/value", 0);
  } ; 
  if($action == 'ptt') { 
   if($B[$button] == '0') { 
    file_put_contents("/sys/class/gpio/gpio$gpio/value", 1);
   } else { 
    file_put_contents("/sys/class/gpio/gpio$gpio/value", 0);
   } ; 
  } ;  
} ; 
$i = 0 ; 
foreach($gpios as $gpio) { 
 $fh = fopen("/sys/class/gpio/gpio$gpio/value", "rb"); #read each gpio by the array list 
 list($B[$i]) = fscanf($fh, "%s");
 fclose($fh) ; 
 $i++ ; 
} ; 
$return['B0'] = $B[0] ;  
$return['B1'] = $B[1] ;  
$return['B2'] = $B[2] ;  
$return['B3'] = $B[3] ;  
$return['B4'] = $B[4] ;  
$return['B5'] = $B[5] ;  
$return['B6'] = $B[6] ;  
$return['B7'] = $B[7] ;  
$reply = json_encode($return) ; 
print "$reply" ; 
} ; 
function dt($string) {
   #very generic basic detaint...
   if (empty($string)) {
        return '' ; 
    } else {
        $string = trim($string) ; 
        $strip = array('/^ /','/\s+$/', '/\$/', '/\n/', '/\r/', '/\n/', '/\,/','/\'/','/\"/', '/\:/','/\@/','/\%/','/0x/');
        $string = preg_replace($strip, '', $string);
        return $string ;
    };
} ; 



?>
