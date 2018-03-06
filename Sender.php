<?php
$ip = '127.0.0.1';
$port = 5000;
$data = json_encode(['test'=>'data','foo'=>'bar']);
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_connect($socket, $ip, $port);
socket_send($socket, $data, mb_strlen($data), MSG_EOF);