<?php

switch ($_SERVER['HTTP_HOST']) {
    case 'production.tamviettech.vn':
        $exports = 'production';
        break;
    default:
        $exports = 'development';
}