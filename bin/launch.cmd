@echo off
echo -----------------------------------------
echo.
echo Running in %CD%
echo.
echo Apache24/bin/__httpd.exe -f /Apache24/conf/httpd.conf
echo.
echo -----------------------------------------
echo.
cd /D "%~dp0/Apache24/bin"
title /B %~dp0/Apache24/bin/__httpd.exe -f %~dp0/Apache24/conf/httpd.conf
%~dp0/Apache24/bin/__httpd.exe -f %~dp0/Apache24/conf/httpd.conf
cd /D "%~dp0