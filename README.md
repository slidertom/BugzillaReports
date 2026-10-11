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
(It's also required Visual Studio 2012 redist package. 
Do download x86 version from: https://www.microsoft.com/en-us/download/details.aspx?id=30679#
)

Linux system copy www folder into the Apache server www folder. (Apache php module must be installed)

### Release Notes
http://127.0.0.1:89/_ReleaseNotes/index.php (menu: **Release Notes**) - collects release notes of the product milestone
from the Bugzilla bug comments.

To add a release note:
1. Set the bug **Target Milestone** to the release milestone.
2. Add a bug comment with the release note text (an existing comment can be used too).
3. Tag the comment with `RELEASENOTE` (case-insensitive, `ReleaseNote` works too).
   Comment tagging requires Bugzilla 5.0+ and the user must be a member of the `comment_taggers_group` group (Bugzilla parameter).

The whole comment text is used as the release note.
Bugs of any status (open and closed) are listed, private comments are not excluded.

Page functions:
- **Product** / **Milestones** select the product and the target milestone (only real milestones are listed).
- Table columns: Bug, Sev, Pri, Status, Release Note, Bug Summary, Note Reporter (tagged comment author).
  Click a column header to sort, shift + click to sort by multiple columns.
- **Filter** input hides table rows that do not contain the entered text (any column).
- **HTML** button opens the plain HTML list of release note texts (no bug ids, no summaries) in the new tab,
  ready to copy into the release document:
  http://127.0.0.1:89/_ReleaseNotes/ajax_get_html_release_notes.php?Product=3&Milestone=4.0+SP40

URL parameters (the selection is written back into the URL, so the link can be bookmarked or shared):

| Parameter | Value                 |
|-----------|-----------------------|
| `product` | Bugzilla product id   |
| `filter`  | target milestone name |

Example: http://127.0.0.1:89/_ReleaseNotes/index.php?product=3&filter=4.0+SP40

**Products Bugs** page **Release Notes** button opens this page for the currently selected product and milestone.

### Screenshots


![Alt text](/screenshots/bug_reports.jpg?raw=true "Optional Title")
![Alt text](/screenshots/product_report.jpg?raw=true "Optional Title")
 	
