<?php

$ch = curl_init();
// 设置URL和相应的选项
curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:9501/users");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1); //设置为POST方式
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_POSTFIELDS, array('test' => str_repeat('a', 800000)));//POST数据
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
var_dump(curl_exec($ch));