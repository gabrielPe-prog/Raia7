<?php
if (!isset($_SESSION)){
  session_start();
}

if (isset($_SESSION['updateSuccess'])) {
  unset($_SESSION['updateSuccess']);
}

date_default_timezone_set('America/Recife');

require_once "../service/conection_create.php";

$conn = conexao_pdo();

$SECRETARIA_ORGAO_ENTIDADE_update = $_POST['SECRETARIA_ORGAO_ENTIDADE_update'];
$N_CONTRATO_CONVENIO_update = $_POST['N_CONTRATO_CONVENIO_update'];
$N_PROCESSO_CONTRATO_update = $_POST['N_PROCESSO_CONTRATO_update'];
$CPL_RESPONSAVEL_update =  $_POST['CPL_RESPONSAVEL_update'];
$N_MODALIDADE_CONTRATO_update = $_POST['N_MODALIDADE_CONTRATO_update'];
$TIPO_MODALIDADE_update = $_POST['TIPO_MODALIDADE_update'];
$N_ARP_ORIGINARIA_update = $_POST['N_ARP_ORIGINARIA_update'];
$FORNECEDOR_REGISTRADO_CNPJ_CPF_update = $_POST['FORNECEDOR_REGISTRADO_CNPJ_CPF_update'];
$OBJETO_update = $_POST['OBJETO_update'];
$DATA_INICIO_CONTRATO_update = $_POST['DATA_INICIO_CONTRATO_update'];
$DATA_VENCIMENTO_CONTRATO_update = $_POST['DATA_VENCIMENTO_CONTRATO_update'];
$TERMO_ADITIVO_ATUAL_update = $_POST['TERMO_ADITIVO_ATUAL_update'];
$VALOR_GLOBAL_ATUALIZADO_update = $_POST['VALOR_GLOBAL_ATUALIZADO_update'];
$GESTOR_update = $_POST['GESTOR_update'];
$FISCAL_update = $_POST['FISCAL_update'];
$OBSERVACOES_update = $_POST['OBSERVACOES_update'];
$id_contrato = $_POST['id_contrato'];

$sql = "UPDATE contratos SET SECRETARIA_ORGAO_ENTIDADE = :SECRETARIA_ORGAO_ENTIDADE_update,
                             N_CONTRATO_CONVENIO = :N_CONTRATO_CONVENIO_update, 
                             N_PROCESSO_CONTRATO = :N_PROCESSO_CONTRATO_update,
                             CPL_RESPONSAVEL = :CPL_RESPONSAVEL_update,
                             N_MODALIDADE_CONTRATO = :N_MODALIDADE_CONTRATO_update,
                             TIPO_MODALIDADE = :TIPO_MODALIDADE_update,
                             N_ARP_ORIGINARIA = :N_ARP_ORIGINARIA_update,
                             FORNECEDOR_REGISTRADO_CNPJ_CPF = :FORNECEDOR_REGISTRADO_CNPJ_CPF_update,
                             OBJETO = :OBJETO_update,
                             DATA_INICIO_CONTRATO = :DATA_INICIO_CONTRATO_update,
                             DATA_VENCIMENTO_CONTRATO = :DATA_VENCIMENTO_CONTRATO_update,
                             TERMO_ADITIVO_ATUAL = :TERMO_ADITIVO_ATUAL_update,
                             VALOR_GLOBAL_ATUALIZADO = :VALOR_GLOBAL_ATUALIZADO_update,
                             GESTOR = :GESTOR_update,
                             FISCAL = :FISCAL_update,
                             OBSERVACOES = :OBSERVACOES_update
                             WHERE id_contrato = :id_contrato";

$stmt = $conn->prepare($sql);

$stmt->bindParam(':SECRETARIA_ORGAO_ENTIDADE_update', $SECRETARIA_ORGAO_ENTIDADE_update);
$stmt->bindParam(':N_CONTRATO_CONVENIO_update', $N_CONTRATO_CONVENIO_update);
$stmt->bindParam(':N_PROCESSO_CONTRATO_update', $N_PROCESSO_CONTRATO_update);
$stmt->bindParam(':CPL_RESPONSAVEL_update', $CPL_RESPONSAVEL_update);
$stmt->bindParam(':N_MODALIDADE_CONTRATO_update', $N_MODALIDADE_CONTRATO_update);
$stmt->bindParam(':TIPO_MODALIDADE_update', $TIPO_MODALIDADE_update);
$stmt->bindParam(':N_ARP_ORIGINARIA_update', $N_ARP_ORIGINARIA_update);
$stmt->bindParam(':FORNECEDOR_REGISTRADO_CNPJ_CPF_update', $FORNECEDOR_REGISTRADO_CNPJ_CPF_update);
$stmt->bindParam(':OBJETO_update', $OBJETO_update);
$stmt->bindParam(':DATA_INICIO_CONTRATO_update', $DATA_INICIO_CONTRATO_update);
$stmt->bindParam(':DATA_VENCIMENTO_CONTRATO_update', $DATA_VENCIMENTO_CONTRATO_update);
$stmt->bindParam(':TERMO_ADITIVO_ATUAL_update', $TERMO_ADITIVO_ATUAL_update);
$stmt->bindParam(':VALOR_GLOBAL_ATUALIZADO_update', $VALOR_GLOBAL_ATUALIZADO_update);
$stmt->bindParam(':GESTOR_update', $GESTOR_update);
$stmt->bindParam(':FISCAL_update', $FISCAL_update);
$stmt->bindParam(':OBSERVACOES_update', $OBSERVACOES_update);
$stmt->bindParam(':id_contrato', $id_contrato);

$retorno = $stmt->execute();

if ($retorno) {
    $_SESSION['updateSuccess'] = 1;
    header("location: ../contratos.php?success=true");
} else {
    $_SESSION['updateSuccess'] = 0;
    header("location: ../contratos.php?success=false");
}