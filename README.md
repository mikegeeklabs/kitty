

This provides a basic webserver using PHP that allows GPIO control. 
As of initial push: no input detainting and no auth. This is a useful toy. 
These might get added later. 

The original purpose of this is a demo project to control solenoids
that allow remote control of a CaT scanner, hence the name: kitty. 
The basic HTML includes the parameter types for button action. 
The final version won't be public.. 
but at this level, it's a useful nice demo script.

On a fresh Raspberry Pi. With or without desktop:

apt-get install php7.0-cli

php ./setup.php
./run.sh

point web browser at Pi's IP address on port 8000  

settings.php is an array that defined which GPIO pins are being used. 

monitor.php is to be run as a deamon to make sure a solenoid is not active 
for more than 20 seconds (they get hot). This might have duty cycle math at
some point. 

buttons can be alloff,momentary or ptt (push to talk).


