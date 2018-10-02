# BugzillaReports
BugzillaReports is an extension for the [Bugzilla](https://www.bugzilla.org/).

BugzillaReports includes advanced time tracking facilities, great for determining what tasks 
and projects you have been spending time on, helping to hone estimates, and complete time sheets. 
BugzillaReports provides full info about project current status, possible release date, developers 
current tasks.
 
### Setup

/www/bugzilla_base/_bugzilla_reports_settings.php - global variables must be defined.
 ```php
$g_hostname          = 'localhost';     // bugzilla mysql hostname 
$g_bugs_db_name      = 'bugs';          // bugzilla mysql database name
$g_username          = 'reporter';      // bugzilla mysql username 
$g_password          = 'password';      // bugzilla mysql user password
$g_bugzilla_link     = "http://localhost/bugzilla";  // bugzilla http link, used to generate <a href> 
                                                     // bug links, do check out generate_bug_link function   
``` 
### Run
Windows system: do launch launch.cmd and open http://127.0.0.1:89 in the browser.

Linux system copy www folder into the Apache server www folder. (Apache php module must be installed)

### Screenshots

![Alt text](/screenshots/bug_reports.jpg?raw=true "Optional Title")
![Alt text](/screenshots/product_report.jpg?raw=true "Optional Title")
 	
