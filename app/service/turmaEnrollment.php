<?php
require_once __DIR__ . '/turmaStatus.php';

function matricularAlunoTurma(PDO $db, int $alunoId, int $turmaId, string $turmaAnterior): void
{
    if ($alunoId < 1 || $turmaId < 1) {
        throw new InvalidArgumentException('Selecione um aluno e uma turma válidos.');
    }
    $stmt = $db->prepare('SELECT horario FROM turmas WHERE id_turma = ?');
    $stmt->execute([$turmaId]);
    $turma = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$turma || turmaSemDefinicao($turma['horario'])) {
        throw new InvalidArgumentException('Selecione uma turma cadastrada.');
    }
    $stmt = $db->prepare('SELECT id_turma FROM alunos WHERE id_aluno = ?');
    $stmt->execute([$alunoId]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$aluno) {
        throw new InvalidArgumentException('Aluno não encontrado.');
    }
    if ((string) $aluno['id_turma'] !== $turmaAnterior) {
        throw new InvalidArgumentException('A turma do aluno mudou. Recarregue a página e tente novamente.');
    }
    if ((string) $aluno['id_turma'] === (string) $turmaId) {
        throw new InvalidArgumentException('O aluno já está nesta turma.');
    }
    $stmt = $db->prepare('UPDATE alunos SET id_turma = ? WHERE id_aluno = ? AND (id_turma = ? OR (id_turma IS NULL AND ? IS NULL))');
    $stmt->execute([$turmaId, $alunoId, $aluno['id_turma'], $aluno['id_turma']]);
    if ($stmt->rowCount() !== 1) {
        throw new InvalidArgumentException('A turma do aluno mudou. Recarregue a página e tente novamente.');
    }
}
