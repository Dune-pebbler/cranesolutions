<?php
defined("ABSPATH") || die("-1");

foreach(glob(__DIR__ . "/*") as $directory){
    if( !is_dir($directory) ) continue;
    if( !file_exists( "{$directory}/config.php" ) ) continue;

    
    require_once("{$directory}/config.php");
}