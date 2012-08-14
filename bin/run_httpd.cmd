@echo off

pushd PHP
PATH=%cd%;%PATH%
popd

set PATH
echo.
set BLD
set BUILDERCFG
echo.

md ..\..\_tmp

if not exist  .\Apache2.2\conf\httpd.conf  copy  .\Apache2.2\conf\httpd.conf_  .\Apache2.2\conf\httpd.conf
if not exist  .\..\www\conf\conf.php       copy  .\..\www\conf\conf.php_       .\..\www\conf\conf.php


echo php_errors.log>..\www\php_errors.log
echo access.log>..\www\access.log
echo httpd.log>..\..\_tmp\httpd.log

echo.
echo start /B _cmd_httpd /c Apache2.2\bin\__httpd.exe
start /B _cmd_httpd /c Apache2.2\bin\__httpd.exe

echo.
echo -----------------------------------------
echo.
