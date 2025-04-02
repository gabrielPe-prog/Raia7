<?php
require_once 'controller/controllerPagamento.php';

$controller = new PagamentoController();
$controller->webhook();
?>