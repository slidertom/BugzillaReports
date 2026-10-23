@echo off

pushd PHP84
PATH=%cd%;%PATH%
popd

set PATH
echo.
set BLD
set BUILDERCFG
echo.

md ..\..\_tmp

if not exist  .\Apache24\conf\httpd.conf   copy  .\Apache24\conf\httpd.conf_   .\Apache24\conf\httpd.conf
if not exist  .\..\www\conf\conf.php       copy  .\..\www\conf\conf.php_       .\..\www\conf\conf.php

if exist  .\Apache24\httpd.pid  call .\Apache24\stop_console_apache.bat


echo php_errors.log>..\www\php_errors.log
echo access.log>..\www\access.log
echo httpd.log>..\..\_tmp\httpd.log

echo.
echo start /B _cmd_httpd /c Apache24\bin\__httpd.exe
start /B _cmd_httpd /c Apache24\bin\__httpd.exe

echo.
echo -----------------------------------------
echo.
