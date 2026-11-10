<?php
require_once __DIR__ . '/../service/studentPhoto.php';
function photoCheck(bool $ok): void { if (!$ok) throw new RuntimeException('Falha no teste de foto.'); }
function photoReject(callable $run): void {
    try { $run(); } catch (RuntimeException $e) { photoCheck($e->getCode() === 422); return; }
    throw new RuntimeException('Arquivo deveria ter sido recusado.');
}
photoCheck(saveStudentPhoto(null, 1) === null);
photoCheck(saveStudentPhoto(['error'=>UPLOAD_ERR_NO_FILE], 1) === null);
photoReject(fn() => saveStudentPhoto(['error'=>UPLOAD_ERR_INI_SIZE], 1));
photoReject(fn() => saveStudentPhoto(['error'=>UPLOAD_ERR_OK,'tmp_name'=>'/arquivo-inexistente'], 1));
$tmp = tempnam(sys_get_temp_dir(), 'r7-photo-');
try {
    file_put_contents($tmp, '<?php echo "não é uma foto";');
    photoReject(fn() => validateStudentPhoto($tmp));
    file_put_contents($tmp, str_repeat('a', 2 * 1024 * 1024 + 1));
    photoReject(fn() => validateStudentPhoto($tmp));
    foreach (['jpeg', 'png'] as $format) {
        $image = imagecreatetruecolor(20, 30);
        if ($format === 'jpeg') imagejpeg($image, $tmp); else imagepng($image, $tmp);
        unset($image);
        clearstatcache(true, $tmp);
        $info = validateStudentPhoto($tmp);
        photoCheck($info[0] === 20 && $info[1] === 30);
    }
} finally { unlink($tmp); }
echo "PASS: foto opcional, erros de envio, conteúdo inválido, limite de tamanho, JPEG e PNG.\n";
