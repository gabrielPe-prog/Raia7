<?php 
    // Define o header como sendo de imagem
    header("Content-type: image/jpeg");
    
    // Cria a imagem a partir de uma imagem jpeg
    $imagem = imagecreatefromjpeg("assets/carteirinha/carteirinha_servidor.jpg");
    
    // Definições
    $preto = imagecolorallocate($imagem, 0,0,0); # Cor preta
    $nome = "Ewerton"; # Texto a ser escrito
    $cpf = "12620002460"; # Texto a ser escrito
    $data_nascimento = "31/07/2002"; # Texto a ser escrito
    $matricula = "222333"; # Texto a ser escrito
    $setor = "SEPLAG"; # Texto a ser escrito
    $fonte = "assets/carteirinha/trebuc.ttf"; # Fonte utilizada
    
    // Escreve na imagem
    imagettftext($imagem, 24, 0, 50,50,$preto,$fonte,$nome);
    imagettftext($imagem, 24, 0, 80,80,$preto,$fonte,$cpf);
    imagettftext($imagem, 24, 0, 110,110,$preto,$fonte,$data_nascimento);
    imagettftext($imagem, 24, 0, 140,140,$preto,$fonte,$matricula);
    imagettftext($imagem, 24, 0, 170,170,$preto,$fonte,$setor);
    $certificado = "assets/carteirinha/geradas/".$nome.".jpg";
    // Gera a imagem na tela
    imagejpeg($imagem, $certificado);    

    // Destroi a imagem para liberar memória
    imagedestroy($imagem);

    // 
    header('location: perfilServidor.php');
    
 ;