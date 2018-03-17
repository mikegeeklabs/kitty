<?php
global $gpios ; 
include("settings.php") ; #and one file for all config
#MAX TIME FOR A GPIO PIN / SOLENOID to be ON is $seconds X $limit 
$seconds = 2 ; #seconds per scan. 
$limit = 10 ; #if this many scans in a row, a GPIO pin is on, monitor will turn it off. 
#Example: 2 seconds * 10 is a 20 second timer. 
$i = 0 ; #just in incrementor
foreach($gpios as $gpio) { # sets a counter for each GPIO pin to 0. 
     $C[$i] = 0 ; 
     $i++ ; 
} ;      
$loop = true ; #I like defining this, habit. Most people just put while() ; 
while($loop) { 
 $i = 0 ; #reset at top of every loop
 #print "." ; 
 foreach($gpios as $gpio) { 
  $fh = fopen("/sys/class/gpio/gpio$gpio/value", "rb"); #read each gpio by the array list 
  list($B[$i]) = fscanf($fh, "%s");
  fclose($fh) ; 
  if($B[$i] == '1') { 
     $C[$i] = $C[$i] = $C[$i] + 1 ;  #increments counter if on
  #   print "$i = 1 Count: $C[$i]\n" ; 
  } else { 
     $C[$i] = 0 ; #resets counter if not on. 
  } ; 
  if($C[$i] > $limit) { 
   $gpio = $gpios[$i] ; 
   print "Turning Off $i $gpios[$i] $gpio\n" ; 
   file_put_contents("/sys/class/gpio/gpio$gpio/value", 0);
  } ; 
  $i++ ; 
 } ; 
 sleep($seconds) ; #set at top of file 
} ; 
?>
