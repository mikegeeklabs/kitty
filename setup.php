<?php
global $gpios ; 
#$gpios = array('5','6','13','19','26','16','20','21') ; #moved to settings.php for all
include("settings.php") ; 
 foreach($gpios as $gpio) { 
    print "Init: $gpio<br>\n" ; 
    system("/bin/echo $gpio > /sys/class/gpio/export") ; 
    system("/bin/echo out > /sys/class/gpio/gpio$gpio/direction") ; 
    system("/bin/echo 0 > /sys/class/gpio/gpio$gpio/value") ; 
 } ; 
 
