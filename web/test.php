<?php

$time = microtime(true);

$con = new PDO("mysql:dbname=ukmtid;host=127.0.0.1", 'root', 'dev');

$connect_time = microtime(true);

$result = $con->query('SHOW TABLES');

$query_time = microtime(true);

var_dump($result->fetchAll(PDO::FETCH_ASSOC));

$time_con = ($connect_time - $time) * 1000;
$time_query = ($query_time - $connect_time) * 1000;

echo "Connection took $time_con ms\n";
echo "Query took $time_query ms\n";

phpinfo();