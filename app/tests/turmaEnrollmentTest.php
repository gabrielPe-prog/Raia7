<?php
require_once __DIR__ . '/../service/turmaEnrollment.php';
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE turmas (id_turma INTEGER PRIMARY KEY, horario TEXT); CREATE TABLE alunos (id_aluno INTEGER PRIMARY KEY, id_turma INTEGER NULL)');
$db->exec("INSERT INTO turmas VALUES (1, '08:00'), (2, '09:00'), (3, 'Sem turma definida'); INSERT INTO alunos VALUES (10, NULL)");
function turmaAlunoTeste(PDO $db): string {
    return (string) $db->query('SELECT id_turma FROM alunos WHERE id_aluno = 10')->fetchColumn();
}
function verificarTurmaTeste(bool $ok, string $mensagem): void {
    if (!$ok) throw new RuntimeException($mensagem);
}
function recusarTurmaTeste(callable $acao): void {
    try { $acao(); } catch (InvalidArgumentException $e) { return; }
    throw new RuntimeException('A operação deveria ter sido recusada.');
}
matricularAlunoTurma($db, 10, 1, '');
verificarTurmaTeste(turmaAlunoTeste($db) === '1', 'Falha ao matricular aluno sem turma.');
matricularAlunoTurma($db, 10, 2, '1');
verificarTurmaTeste(turmaAlunoTeste($db) === '2', 'Falha ao transferir aluno.');
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 10, 2, '2'));
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 10, 1, '1'));
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 10, 3, '2'));
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 10, 999, '2'));
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 999, 1, ''));
recusarTurmaTeste(fn() => matricularAlunoTurma($db, 0, 1, ''));
verificarTurmaTeste(turmaAlunoTeste($db) === '2', 'Uma solicitação recusada alterou a turma.');
echo "OK: matrícula, transferência, turma repetida, conflito, turma inválida e aluno inválido.\n";
