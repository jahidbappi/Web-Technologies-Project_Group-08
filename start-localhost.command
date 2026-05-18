#!/bin/bash
osascript -e 'do shell script "/Applications/XAMPP/xamppfiles/xampp startapache && /Applications/XAMPP/xamppfiles/xampp startmysql" with administrator privileges'
sleep 3
open "http://127.0.0.1/WebTechProject/task3/index.php"
