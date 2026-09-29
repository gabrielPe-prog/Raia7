<?php
require_once __DIR__ . '/../service/connection_create.php';
require_once __DIR__ . '/../service/turmaStatus.php';
$conn = conexao_pdo();
$turmas = [];
foreach ($conn->query('SELECT id_turma, horario, piscina FROM turmas ORDER BY horario, id_turma')->fetchAll(PDO::FETCH_ASSOC) as $turma) {
    if (turmaSemDefinicao($turma['horario'])) {
        continue;
    }
    $turmas[$turma['id_turma']] = ['horario' => $turma['horario'], 'piscina' => $turma['piscina'], 'alunos' => []];
}
$alunosSemTurma = [];
$totalAlunosTurmas = 0;
foreach ($conn->query('SELECT id_aluno, id_turma, nome, cpf, contato, data_nascimento FROM alunos ORDER BY nome, id_aluno')->fetchAll(PDO::FETCH_ASSOC) as $aluno) {
    $totalAlunosTurmas++;
    if (isset($turmas[(string) $aluno['id_turma']])) {
        $turmas[$aluno['id_turma']]['alunos'][] = $aluno;
    } else {
        $alunosSemTurma[] = $aluno;
    }
}
