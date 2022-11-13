<?php

require("./index.php");
$expr = $argv[1];

echo eval_expr($expr) . PHP_EOL;