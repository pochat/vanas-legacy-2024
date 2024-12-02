<?php
$out = null;
$rc= 0;
#$output = shell_exec('/etc/rc.d/init.d/nodejs restart');
#$output = shell_exec('/usr/local/bin/node /var/www/html/vanas_node/server.js > /var/www/html/vanas_node/nodelog.log &');
exec('/usr/local/bin/node /var/www/html/vanas_node/server.js > /var/www/html/vanas_node/nodelog.log &', $output, $rc);
print_r($out);
echo 'rc = '.$rc."\n";
#echo "<pre>$output</pre>";
?>
