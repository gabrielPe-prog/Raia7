<?php 

if (!isset($_SESSION)){
    session_start();
}
date_default_timezone_set('America/Recife');

$conn = conexao_pdo();

$sql = "SELECT * FROM contratos ORDER BY N_CONTRATO_CONVENIO DESC ";
$stm = $conn->prepare($sql);
$stm->execute();
$contratos = $stm->fetchAll(PDO::FETCH_ASSOC);

foreach ($contratos as &$contrato) {
    $hoje = date('Y-m-d');
    $data_vencimento = $contrato['DATA_VENCIMENTO_CONTRATO'];
    
    $data_vencimento_dt = DateTime::createFromFormat('d/m/Y', $data_vencimento);
    
    if ($data_vencimento_dt instanceof DateTime) {
        $data_vencimento = $data_vencimento_dt->format('Y-m-d');
        
        $hoje_dt = new DateTime($hoje);
        $data_vencimento_dt = new DateTime($data_vencimento);
        
        $diferenca_dias = $hoje_dt->diff($data_vencimento_dt)->days;
        
        if ($hoje_dt > $data_vencimento_dt) {
            $contrato['status'] = 1;
        } elseif ($diferenca_dias <= 30) {
            $contrato['status'] = 2;
        } elseif ($diferenca_dias <= 60) {
            $contrato['status'] = 3;
        } elseif ($diferenca_dias <= 90) {
            $contrato['status'] = 4;
        } else {
            $contrato['status'] = 0;
        }
    } else {
        $contrato['status'] = -1;
    }
}
unset($contrato);