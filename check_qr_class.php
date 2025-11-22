<?php
require 'vendor/autoload.php';

$classes = [
    'SimpleSoftwareIO\\QrCode\\BaconQrCodeGenerator',
    'SimpleSoftwareIO\\QrCode\\Generator', 
    'SimpleSoftwareIO\\QrCode\\Facades\\QrCode',
];

foreach($classes as $c) {
    echo $c . ': ' . (class_exists($c) ? 'Ada' : 'Tidak') . PHP_EOL;
}
