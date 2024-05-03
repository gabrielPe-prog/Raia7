<?php
if (!isset($_SESSION)){
  session_start();
}
date_default_timezone_set('America/Recife');

require_once "../service/conection_create.php";

    $SECRETARIA_ORGAO_ENTIDADE = $_POST['SECRETARIA_ORGAO_ENTIDADE'];
    $N_CONTRATO_CONVENIO = $_POST['N_CONTRATO_CONVENIO'];
    $N_PROCESSO_CONTRATO = $_POST['N_PROCESSO_CONTRATO'];
    $CPL_RESPONSAVEL =  $_POST['CPL_RESPONSAVEL'];
    $N_MODALIDADE_CONTRATO = $_POST['N_MODALIDADE_CONTRATO'];
    $TIPO_MODALIDADE = $_POST['TIPO_MODALIDADE'];
    $N_ARP_ORIGINARIA = $_POST['N_ARP_ORIGINARIA'];
    $FORNECEDOR_REGISTRADO_CNPJ_CPF = $_POST['FORNECEDOR_REGISTRADO_CNPJ_CPF'];
    $OBJETO = $_POST['OBJETO'];
    $DATA_INICIO_CONTRATO = $_POST['DATA_INICIO_CONTRATO'];
    $DATA_VENCIMENTO_CONTRATO = $_POST['DATA_VENCIMENTO_CONTRATO'];
    $TERMO_ADITIVO_ATUAL = $_POST['TERMO_ADITIVO_ATUAL'];
    $VALOR_GLOBAL_ATUALIZADO = $_POST['VALOR_GLOBAL_ATUALIZADO'];
    $GESTOR = $_POST['GESTOR'];
    $FISCAL = $_POST['FISCAL'];
    $OBSERVACOES = $_POST['OBSERVACOES'];

    $conn = conexao_pdo(); 
    
    $sql = "INSERT INTO contratos(
                      SECRETARIA_ORGAO_ENTIDADE,
                      N_CONTRATO_CONVENIO,
                      N_PROCESSO_CONTRATO,
                      CPL_RESPONSAVEL,
                      N_MODALIDADE_CONTRATO,
                      TIPO_MODALIDADE,
                      N_ARP_ORIGINARIA,
                      FORNECEDOR_REGISTRADO_CNPJ_CPF,
                      OBJETO,
                      DATA_INICIO_CONTRATO,
                      DATA_VENCIMENTO_CONTRATO,
                      TERMO_ADITIVO_ATUAL,
                      VALOR_GLOBAL_ATUALIZADO,
                      GESTOR,
                      FISCAL,
                      OBSERVACOES) 
                      
                    VALUES (
                      :SECRETARIA_ORGAO_ENTIDADE,
                      :N_CONTRATO_CONVENIO,
                      :N_PROCESSO_CONTRATO,
                      :CPL_RESPONSAVEL,
                      :N_MODALIDADE_CONTRATO,
                      :TIPO_MODALIDADE,
                      :N_ARP_ORIGINARIA,
                      :FORNECEDOR_REGISTRADO_CNPJ_CPF,
                      :OBJETO,
                      :DATA_INICIO_CONTRATO,
                      :DATA_VENCIMENTO_CONTRATO,
                      :TERMO_ADITIVO_ATUAL,
                      :VALOR_GLOBAL_ATUALIZADO,
                      :GESTOR,
                      :FISCAL,
                      :OBSERVACOES)";

    $stm = $conn->prepare($sql);
    $stm->bindParam(':SECRETARIA_ORGAO_ENTIDADE', $SECRETARIA_ORGAO_ENTIDADE);
    $stm->bindParam(':N_CONTRATO_CONVENIO', $N_CONTRATO_CONVENIO);
    $stm->bindParam(':N_PROCESSO_CONTRATO', $N_PROCESSO_CONTRATO);
    $stm->bindParam(':CPL_RESPONSAVEL', $CPL_RESPONSAVEL);
    $stm->bindParam(':N_MODALIDADE_CONTRATO', $N_MODALIDADE_CONTRATO);
    $stm->bindParam(':TIPO_MODALIDADE', $TIPO_MODALIDADE);
    $stm->bindParam(':N_ARP_ORIGINARIA', $N_ARP_ORIGINARIA);
    $stm->bindParam(':FORNECEDOR_REGISTRADO_CNPJ_CPF', $FORNECEDOR_REGISTRADO_CNPJ_CPF);
    $stm->bindParam(':OBJETO', $OBJETO);
    $stm->bindParam(':DATA_INICIO_CONTRATO', $DATA_INICIO_CONTRATO);
    $stm->bindParam(':DATA_VENCIMENTO_CONTRATO', $DATA_VENCIMENTO_CONTRATO);
    $stm->bindParam(':TERMO_ADITIVO_ATUAL', $TERMO_ADITIVO_ATUAL);
    $stm->bindParam(':VALOR_GLOBAL_ATUALIZADO', $VALOR_GLOBAL_ATUALIZADO);
    $stm->bindParam(':GESTOR', $GESTOR);
    $stm->bindParam(':FISCAL', $FISCAL);
    $stm->bindParam(':OBSERVACOES', $OBSERVACOES);
    
    $retorno = $stm->execute();

    if (isset($retorno)){
        echo "<script>swal('Cadastro Realizado');</script>";
      header('location: ../contratos.php');
    }
    else{
        echo "<script>swal('Não foi possível cadastrar');</script>";
      header('location: ../contratos.php');
    }