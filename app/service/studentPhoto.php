<?php
/** Valida o conteúdo real; o nome e o MIME enviados pelo navegador não são confiáveis. */
function validateStudentPhoto(string $path): array
{
    $size = is_file($path) ? filesize($path) : false;
    if (!$size || $size > 2 * 1024 * 1024) {
        throw new RuntimeException('A foto deve ter no máximo 2 MB.', 422);
    }
    $info = @getimagesize($path);
    if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        throw new RuntimeException('Selecione uma foto JPEG ou PNG válida.', 422);
    }
    if ($info[0] > 6000 || $info[1] > 6000 || $info[0] * $info[1] > 12000000) {
        throw new RuntimeException('A foto deve ter até 6000 pixels por lado e 12 megapixels.', 422);
    }
    return $info;
}

function saveStudentPhoto(?array $upload, int $studentId): ?string
{
    if ($upload === null || ($upload['error'] ?? null) === UPLOAD_ERR_NO_FILE) return null;
    if (!isset($upload['error']) || $upload['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Não foi possível enviar a foto. Selecione um arquivo de até 2 MB e tente novamente.', 422);
    }
    $tmp = $upload['tmp_name'] ?? null;
    if (!is_string($tmp) || !is_uploaded_file($tmp) || $studentId < 1) {
        throw new RuntimeException('Envio de foto inválido.', 422);
    }
    $info = validateStudentPhoto($tmp);
    $image = $info[2] === IMAGETYPE_JPEG ? @imagecreatefromjpeg($tmp) : @imagecreatefrompng($tmp);
    if (!$image) throw new RuntimeException('Não foi possível ler a foto.', 422);
    $directory = 'anexo_alunos/aluno_' . $studentId;
    $absolute = dirname(__DIR__) . '/' . $directory;
    try {
        if (!is_dir($absolute) && !mkdir($absolute, 0755, true) && !is_dir($absolute)) {
            throw new RuntimeException('Não foi possível criar a pasta da foto.');
        }
        // Regrava somente a imagem, com nome aleatório e extensão controlada.
        $relative = $directory . '/foto_' . bin2hex(random_bytes(16)) . '.jpg';
        if (!imagejpeg($image, dirname(__DIR__) . '/' . $relative, 90)) {
            @unlink(dirname(__DIR__) . '/' . $relative);
            throw new RuntimeException('Não foi possível salvar a foto.');
        }
        return $relative;
    } finally {
        unset($image);
    }
}
