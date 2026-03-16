<?php
$content = file_get_contents('.env');
$content = preg_replace('/^SESSION_ENCRYPT=.*$/m', 'SESSION_ENCRYPT=true', $content);
file_put_contents('.env', $content);
echo "Updated SESSION_ENCRYPT=true\n";

