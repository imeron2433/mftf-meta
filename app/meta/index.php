<?php
define('TESTS_BP', '/var/magento/magento2ce/dev/tests/acceptance');

require_once '../vendor/autoload.php';

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        include '../src/api/metadata/get.php';
        echo returnTestCounts();
        break;
    default :
        echo 'you have done nothing';
}
