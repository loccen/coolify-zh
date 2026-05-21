<?php

// To prevent github actions from failing
function env($key = null, $default = null)
{
    return $default;
}

$version = include 'config/constants.php';
echo $version['coolify']['helper_version'] ?: 'unknown';
