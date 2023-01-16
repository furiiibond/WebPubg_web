<?php
$command = escapeshellcmd("source /mnt/hgfs/FIVERR/websitePubg/Desktop/venv/Scripts/activate && python3 /mnt/hgfs/FIVERR/websitePubg/Desktop/test.py toi");
$output = shell_exec($command . " 2>&1"); // 2>&1 is for error
echo $output;
?>
