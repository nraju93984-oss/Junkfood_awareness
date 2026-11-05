<?php
require __DIR__ . '/config.php';
out(['user' => require_login()]);
