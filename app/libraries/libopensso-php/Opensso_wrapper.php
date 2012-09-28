<?php
require_once 'OpenSSO.php';

class Opensso_wrapper extends OpenSSO {
    function Opensso_wrapper() {
        parse_str($_SERVER['QUERY_STRING'], $_GET);
        parent::OpenSSO();
    }
}
