<?php

$json = '{"name":"test","running_time":102,"release_date":"2015-10-02"}';

var_dump(json_decode($json));
var_dump(json_decode($json, true));

?>