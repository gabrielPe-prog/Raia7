<?php
function conexao_pdo(){

    $user = 'raia_adm';
    $pass = '1925310607';

        try {
            $conexao = new PDO('mysql:host=localhost;dbname=raia7', $user, $pass);
            return $conexao;
        }
        catch ( PDOException $e )
        {
            echo 'Erro ao conectar com o MySQL: ' . $e->getMessage();
        }
}
