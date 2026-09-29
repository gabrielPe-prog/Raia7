<?php
require_once __DIR__ . '/turmaStatus.php';

function dadosCarteirinha(array $aluno): array
{
    $nome = trim((string) ($aluno['nome'] ?? ''));
    $partes = preg_split('/\s+/u', $nome, -1, PREG_SPLIT_NO_EMPTY);
    $iniciais = $partes ? mb_substr($partes[0], 0, 1) : 'R7';
    if (count($partes) > 1) {
        $iniciais .= mb_substr($partes[count($partes) - 1], 0, 1);
    }
    $horario = $aluno['horario_turma'] ?? $aluno['horario'] ?? '';
    $pendente = empty($aluno['id_turma']) || trim($horario) === '' || turmaSemDefinicao($horario);
    $cpf = (string) ($aluno['cpf'] ?? '');
    $digitos = preg_replace('/\D/', '', $cpf);
    if (strlen($digitos) === 11) {
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $digitos);
    }
    $foto = null;
    $root = realpath(__DIR__ . '/..');
    $path = realpath($root . '/' . (string) ($aluno['path_foto'] ?? ''));
    if ($path && str_starts_with($path, $root . '/anexo_alunos/') && is_file($path)) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        if (in_array($mime, ['image/jpeg', 'image/png'], true)) {
            $foto = implode('/', array_map('rawurlencode', explode('/', substr($path, strlen($root) + 1))));
        }
    }
    return [
        'nome' => $nome ?: 'Nome não informado', 'iniciais' => mb_strtoupper($iniciais),
        'matricula' => str_pad((string) ($aluno['id_aluno'] ?? ''), 5, '0', STR_PAD_LEFT),
        'cpf' => $cpf ?: 'Não informado', 'pendente' => $pendente,
        'horario' => $pendente ? 'Aguardando definição' : $horario,
        'piscina' => $pendente ? '—' : (($aluno['piscina'] ?? '') ?: 'Não informada'),
        'foto' => $foto,
    ];
}
