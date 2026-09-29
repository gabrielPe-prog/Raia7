<?php
/** Legacy placeholder groups mean the student is awaiting a real class. */
function turmaSemDefinicao(?string $horario): bool
{
    $nome = mb_strtolower(trim((string) $horario), 'UTF-8');
    $nome = preg_replace('/\s+/u', ' ', $nome);
    return in_array($nome, ['sem turma', 'sem turma definida'], true);
}
