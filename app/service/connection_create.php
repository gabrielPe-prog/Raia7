<?php
function conexao_pdo(){

    $user = 'root';
    $pass = 'AujAC#8511Q5';

        try {
            $conexao = new PDO('mysql:host=db;dbname=sistema_r7', $user, $pass);
            return $conexao;
        }
        catch ( PDOException $e )
        {
            echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
        }
}
