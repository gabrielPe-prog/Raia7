<?php
/** Run: php app/tests/reportExportTest.php [directory-for-sample-pdfs] */
require_once __DIR__ . '/../service/reportData.php';
require_once __DIR__ . '/../service/reportPdf.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
function rejects(callable $callback, int $code): void
{
    try {
        $callback();
    } catch (RuntimeException $error) {
        check($error->getCode() === $code, 'Unexpected error code: ' . $error->getCode());
        return;
    }
    throw new RuntimeException('Request should have been rejected: ' . $code);
}
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec('CREATE TABLE alunos (id_aluno INTEGER PRIMARY KEY, id_turma INTEGER, nome TEXT, cpf TEXT, contato TEXT, data_nascimento TEXT, created_at TEXT, escola TEXT, serie_escola TEXT, endereco TEXT, cep TEXT, obs_saude TEXT, path_foto TEXT)');
$db->exec('CREATE TABLE turmas (id_turma INTEGER PRIMARY KEY, horario TEXT, piscina TEXT)');
$db->exec('CREATE TABLE pagamentos (id INTEGER PRIMARY KEY, aluno_id INTEGER, descricao TEXT, valor DECIMAL, data_pagamento TEXT, created_at TEXT)');
$db->exec("INSERT INTO turmas VALUES (1, '08:00 às 09:00', 'Adultos'), (2, '09:00 às 10:00', 'Infantil')");
$insertStudent = $db->prepare('INSERT INTO alunos VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
$insertStudent->execute([1, 1, 'João da Silva & Família', '00000000001', '(81) 90000-0001', '2000-02-29', '2026-09-01', 'Escola Exemplo', '3º ano', 'Rua das Águas, 123 — Recife', '50000-000', str_repeat('Observação de teste com acentuação. ', 25), '']);
$insertStudent->execute([2, null, 'Outro aluno', '00000000002', '', '1990-01-01', '2024-01-01', '', '', '', '', '<script>alert("XSS")</script>', '']);
$insertPayment = $db->prepare('INSERT INTO pagamentos VALUES (?, ?, ?, ?, ?, ?)');
foreach ([
    [1, 1, 'Antes do período', '99.99', '2024-01-31', '2024-01-31'],
    [2, 1, 'Natação mensal', '123.45', '2024-02-01', '2024-02-01'],
    [3, 1, 'Aula extra & reposição', '10.10', '2024-02-29', '2024-02-29'],
    [4, 1, 'Depois do período', '99.99', '2024-03-01', '2024-03-01'],
    [5, 2, 'Pagamento de outro aluno', '900.55', '2024-02-15', '2024-02-15'],
    [6, 1, 'Sem data', '1.00', null, '2024-02-01'],
] as $payment) {
    $insertPayment->execute($payment);
}
$admin = ['logged_in' => true, 'nivel' => 1, 'id_aluno' => 1, 'cpf' => '00000000001'];
$student = array_replace($admin, ['nivel' => 2]);
$input = ['tela' => 'infoPagAlunos', 'mes' => '2', 'ano' => '2024'];
$request = reportRequest($input, $student);
$cardRequest = reportRequest(['tela' => 'carteirinha'], $student);
check($cardRequest['period'] === null && !isset($cardRequest['month']), 'Membership card requires no period');
check(count(buildReport($db, $cardRequest, $student)['students']) === 1, 'Membership card uses authenticated current student');
rejects(fn() => reportRequest(['tela' => 'carteirinha'], []), 401);
rejects(fn() => reportRequest(['tela' => 'financeiro'], $admin), 400);

check($request['start'] === '2024-02-01' && $request['end'] === '2024-03-01', 'Leap-year boundaries');
$personal = buildReport($db, $request, $student);
check(array_column($personal['payments'], 'id') === [2, 3], 'Month filtering and owner isolation');
check($personal['totalCents'] === 13355, 'Exact cent total');
$tampered = reportRequest($input + ['id_aluno' => 2, 'cpf' => '00000000002'], $student);
check(count(buildReport($db, $tampered, $student)['payments']) === 2, 'Ignore client-selected owner');
$finance = buildReport($db, reportRequest(array_replace($input, ['tela' => 'financeiro']), $admin), $admin);
check(count($finance['payments']) === 3 && $finance['totalCents'] === 103410, 'Admin totals');
$december = reportRequest(array_replace($input, ['mes' => 12]), $admin);
check($december['end'] === '2025-01-01', 'Year rollover');
$empty = buildReport($db, reportRequest(array_replace($input, ['ano' => 2020]), $student), $student);
check(!$empty['payments'] && $empty['totalCents'] === 0, 'Empty period');
foreach (['alunos', 'turmas', 'financeiro'] as $type) {
    rejects(fn() => reportRequest(array_replace($input, ['tela' => $type]), $student), 403);
}
rejects(fn() => reportRequest($input, []), 401);
rejects(fn() => reportRequest(array_replace($input, ['tela' => 'paginaInicial']), $admin), 400);
rejects(fn() => reportRequest(array_replace($input, ['tela' => ['financeiro']]), $admin), 400);
foreach ([['mes' => 0], ['mes' => 13], ['ano' => 2101], ['ano' => 1899], ['ano' => '2024 OR 1=1'], ['mes' => ['2']]] as $invalid) {
    rejects(fn() => reportRequest(array_replace($input, $invalid), $admin), 400);
}
rejects(fn() => buildReport($db, $request, array_replace($student, ['cpf' => '00000000002'])), 404);
$groups = buildReport($db, reportRequest(array_replace($input, ['tela' => 'turmas']), $admin), $admin);
check(count($groups['groups']) === 3 && count($groups['groups'][0]['students']) === 1 && !$groups['groups'][1]['students'] && $groups['groups'][2]['students'][0]['id_aluno'] === 2, 'Include all groups and empty groups');
$students = buildReport($db, reportRequest(array_replace($input, ['tela' => 'alunos', 'ano' => 2000]), $admin), $admin);
check(count($students['students']) === 2, 'Reference period keeps current student records');
check(reportImage('../service/connection_create.php') === null, 'Block file traversal');
check(str_contains(reportEscape('<script>'), '&lt;script&gt;'), 'Escape untrusted HTML');
check(reportDate('2000-02-29') === '29/02/2000', 'Brazilian dates');

// Existing "Sem turma" records represent pending assignments, not actual classes.
check(turmaSemDefinicao('  SEM   TURMA  '), 'Normalize placeholder name');
check(!turmaSemDefinicao('08:00 às 09:00'), 'Keep real schedules');
$db->exec("INSERT INTO turmas VALUES (3, 'Sem turma', '')");
$insertStudent->execute([3, 3, 'Aluno aguardando turma', '00000000003', '', '', '2026-01-01', '', '', '', '', '', '']);
$pendingReport = buildReport($db, reportRequest(array_replace($input, ['tela' => 'turmas']), $admin), $admin);
check(count($pendingReport['groups']) === 3, 'Only real groups and one pending group');
$pendingGroup = $pendingReport['groups'][2];
check($pendingGroup['id_turma'] === null && count($pendingGroup['students']) === 2, 'Combine null assignments with legacy placeholder');
check(array_column($pendingGroup['students'], 'id_aluno') === [3, 2], 'Keep pending students in alphabetical order');
check(!in_array('Sem turma', array_column($pendingReport['groups'], 'horario'), true), 'Do not duplicate pending group');
$pendingCard = dadosCarteirinha(['id_aluno' => 3, 'id_turma' => 3, 'nome' => 'Ana Silva', 'horario' => 'Sem turma', 'cpf' => '00000000003']);
check($pendingCard['pendente'] && $pendingCard['horario'] === 'Aguardando definição', 'Legacy pending membership card');
check($pendingCard['iniciais'] === 'AS' && $pendingCard['foto'] === null, 'Photo fallback');
check($pendingCard['cpf'] === '000.000.000-03', 'Membership CPF formatting');



$output = $argv[1] ?? null;
if ($output && !is_dir($output)) {
    mkdir($output, 0700, true);
}
foreach (array_keys(reportDefinitions()) as $type) {
    $reportInput = $type === 'carteirinha' ? ['tela' => $type] : array_replace($input, ['tela' => $type]);
    $report = buildReport($db, reportRequest($reportInput, $admin), $admin);
    $pdf = renderReportPdf($report);
    check(str_starts_with($pdf, '%PDF-'), 'Valid PDF for ' . $type);
    if ($output) file_put_contents($output . '/' . $type . '.pdf', $pdf);
}
if ($output) file_put_contents($output . '/empty.pdf', renderReportPdf($empty));
// Multi-page report proves exports are independent of the on-screen page size.
for ($id = 10; $id < 100; $id++) {
    $insertPayment->execute([$id, 1, str_repeat('Descrição longa para teste de paginação. ', 5), '0.01', '2024-02-20', '2024-02-20']);
}
$large = buildReport($db, $request, $student);
check(count($large['payments']) === 92 && $large['totalCents'] === 13445, 'All rows, beyond table pagination');
$pdf = renderReportPdf($large);
preg_match_all('/\/Type\s*\/Page\b/', $pdf, $pages);
check(count($pages[0]) > 1, 'Multi-page PDF');
if ($output) file_put_contents($output . '/multipage.pdf', $pdf);
echo "PASS: authorization, owner isolation, dates, totals, current data, empty states, seven PDF types and pagination.\n";
