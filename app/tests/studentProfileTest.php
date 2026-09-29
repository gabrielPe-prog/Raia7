<?php
require_once __DIR__ . '/../service/studentProfile.php';
function ensure(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
function expectError(callable $run, int $code): void {
    try { $run(); } catch (RuntimeException $error) { ensure($error->getCode() === $code, 'Wrong error code'); return; }
    throw new RuntimeException('Expected rejection');
}
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE alunos (id_aluno INTEGER PRIMARY KEY, cpf TEXT, nome TEXT, escola TEXT, serie_escola TEXT, contato TEXT, cep TEXT, endereco TEXT, data_nascimento TEXT, obs_saude TEXT, id_turma INTEGER)');
$db->exec('CREATE TABLE user (id_user INTEGER PRIMARY KEY, cpf TEXT, nome TEXT)');
$db->exec("INSERT INTO alunos (id_aluno, cpf, nome, id_turma) VALUES (1, '11122233344', 'Original', 7), (2, '55566677788', 'Outro aluno', 8)");
$db->exec("INSERT INTO user VALUES (1, '11122233344', 'Original'), (2, '55566677788', 'Outro aluno')");
$session = ['logged_in'=>true, 'id_aluno'=>1, 'id_user'=>1, 'cpf'=>'11122233344'];
$input = ['nome'=>' Nome atualizado ', 'escola'=>'Escola', 'serie_escola'=>'3º ano', 'contato'=>'(81) 99999-9999', 'cep'=>'50000-000', 'endereco'=>'Rua das Águas', 'data_nascimento'=>'2000-02-29', 'obs_saude'=>'Nenhuma', 'id_aluno'=>2, 'cpf'=>'123.456.789-01', 'id_turma'=>8];
$data = validateStudentProfile($input);
ensure(!isset($data['id_turma']) && !isset($data['id_aluno']), 'Only allowed fields');
ensure($data['cpf'] === '12345678901', 'CPF normalized');
updateStudentProfile($db, $session, $data);
$session['cpf'] = $data['cpf'];
updateStudentProfile($db, $session, $data);
ensure($db->query('SELECT nome FROM alunos WHERE id_aluno=1')->fetchColumn() === 'Nome atualizado', 'Own profile saved and unchanged save accepted');
ensure($db->query('SELECT nome FROM user WHERE id_user=1')->fetchColumn() === 'Nome atualizado', 'Login name synchronized');
ensure($db->query('SELECT nome FROM alunos WHERE id_aluno=2')->fetchColumn() === 'Outro aluno', 'Other profile unchanged');
ensure((int)$db->query('SELECT id_turma FROM alunos WHERE id_aluno=1')->fetchColumn() === 7, 'Class unchanged');
ensure($db->query('SELECT cpf FROM alunos WHERE id_aluno=1')->fetchColumn() === '12345678901', 'Student CPF updated');
ensure($db->query('SELECT cpf FROM user WHERE id_user=1')->fetchColumn() === '12345678901', 'Login CPF synchronized');
expectError(fn()=>updateStudentProfile($db, $session, array_replace($data, ['cpf'=>'55566677788'])), 409);
ensure($db->query('SELECT cpf FROM alunos WHERE id_aluno=1')->fetchColumn() === '12345678901', 'Duplicate leaves original intact');
expectError(fn()=>updateStudentProfile($db, [], $data), 401);
expectError(fn()=>updateStudentProfile($db, array_replace($session, ['id_aluno'=>2]), $data), 404);
foreach ([['cpf'=>'123'], ['cpf'=>['bad']], ['nome'=>''], ['nome'=>['array']], ['cep'=>'123'], ['contato'=>'123'], ['data_nascimento'=>'2023-02-29'], ['data_nascimento'=>'2999-01-01'], ['obs_saude'=>str_repeat('a',1001)]] as $invalid) {
    expectError(fn()=>validateStudentProfile(array_replace($input, $invalid)), 422);
}
$db->exec("CREATE TRIGGER reject_name BEFORE UPDATE ON user BEGIN SELECT RAISE(ABORT, 'Test rollback'); END");
try { updateStudentProfile($db, $session, array_replace($data, ['nome'=>'Should roll back', 'cpf'=>'98765432100'])); } catch (PDOException $error) {}
ensure($db->query('SELECT nome FROM alunos WHERE id_aluno=1')->fetchColumn() === 'Nome atualizado', 'Atomic update rollback');
ensure($db->query('SELECT cpf FROM alunos WHERE id_aluno=1')->fetchColumn() === '12345678901', 'CPF rollback');
echo "PASS: validation, own-profile isolation, protected fields, name sync, unchanged save and rollback.\n";
