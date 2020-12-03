<?php
$files = scandir(__DIR__);
foreach($files as $file) {
    if ($file == "..") {
        continue;
    }
    if ($file == ".") {
        continue;
    }
    if ($file == "index.php") {
        continue;
    }

    if (is_dir((__DIR__)."/$file")) {
        echo "<a href='/weekly_plans/$file/index.php'>$file</a><br>";
    }
    else {
        $basename = basename(__DIR__);
        echo "<a href='/weekly_plans/$basename/$file'>$file</a><br>";
    }
}
?>