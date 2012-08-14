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
del /q ..\..\_tmp\*.log
del /q ..\..\_tmp\*.tmp
del /q ..\..\_tmp\*.txt
del /q ..\..\_tmp\*.cmd

echo.
echo CURRENT DIR %cd%
echo cd ..\www\core
pushd ..\www\core

echo.
echo CURRENT DIR %cd%
echo _php-win -f start_stop_daemon.php 1
_php-win -f start_stop_daemon.php 1

popd

echo.
echo -----------------------------------------
echo.
