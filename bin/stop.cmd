@echo off
set current_dir=%~dp0
call %~dp0/Apache24/stop_console_apache.bat 
cd %current_dir%
