# Nictom-IT01_PHP
This is a class that allows you to easily print tickets with generic thermal printers in Point of Sale Systems installed on windows 11

# Dependencies:
- PHPQRCODE from  *https://phpqrcode.sourceforge.net/*

# Resume:
Nictom IT01 is a 58mm thermal printer with no oficial drivers, that supposely is compatible with Epson ESCPOS driver, but in my
case, i couldn't find and install that same driver in my pc (Windows 11). So to print in this machine and others that are identical
but from different manufacturers, you should follow the next steps:

1. Be sure that the paper is correctly feeded to the printer
2. Conect it by USB to your PC
3. Go to scanners and printers and click on "add new device"
4. Wait some seconds and click on "add a new device manually"
5. Select "add a local or network printer with manual configuration"
6. Next, you should select a port like "USB00X (unknown printer)". If it doesn't exists, then select any of the "USB00X" port and if the next steps dont work, change the port for other like "USB00X" in printer properties > ports
7. Select Generic > Generic / Text Only driver
8. Then you can select "use the installed driver" or "replace the installed driver" (both works on my experience)
9. And the final step is activate the sharing of the printer and giving it a memorable name (you will need it in php)
10. After All that, when creating a new instance of Ticket in PHP, you will need to give the printer's shared name as the first parameter of the constructor method (it's case sensitive, so be sure of introducing it right)