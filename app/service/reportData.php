<?php
require_once __DIR__ . '/turmaStatus.php';
/** Shared report definitions, authorization and read-only queries. */
function reportDefinitions(): array
{
    return [
        'alunos' => ['title' => 'Alunos matriculados', 'admin' => true, 'financial' => false],
        'turmas' => ['title' => 'Turmas e horários', 'admin' => true, 'financial' => false],
        'financeiro' => ['title' => 'Relatório financeiro', 'admin' => true, 'financial' => true],
        'infoAlunos' => ['title' => 'Informações do aluno', 'admin' => false, 'financial' => false],
        'carteirinha' => ['title' => 'Carteirinha do aluno', 'admin' => false, 'financial' => false],
        'infoPagAlunos' => ['title' => 'Histórico de pagamentos', 'admin' => false, 'financial' => true],
        'pagamentosAluno' => ['title' => 'Pagamentos do aluno', 'admin' => false, 'financial' => true],
    ];
}

function reportRequest(array $input, array $session): array
{
    if (($session['logged_in'] ?? false) !== true) {
        throw new RuntimeException('Sua sessão expirou. Entre novamente para exportar.', 401);
    }
    $type = $input['tela'] ?? '';
    $definitions = reportDefinitions();
    if (!is_string($type) || !isset($definitions[$type])) {
        throw new RuntimeException('Esta tela não possui relatório disponível.', 400);
    }
    $definition = $definitions[$type];
    if ($definition['admin'] && (string) ($session['nivel'] ?? '') !== '1') {
        throw new RuntimeException('Você não tem permissão para exportar este relatório.', 403);
    }
    if (!$definition['admin'] && (empty($session['id_aluno']) || empty($session['cpf']))) {
        throw new RuntimeException('Seu cadastro de aluno não foi identificado. Entre novamente.', 403);
    }
    if ($type === 'carteirinha') {
        return $definition + [
            'type' => $type, 'period' => null,
            'note' => 'Dados atuais do cadastro na data de emissão.',
        ];
    }
    foreach (['mes', 'ano'] as $key) {
        if (!isset($input[$key]) || !is_scalar($input[$key]) || !preg_match('/^\d+$/D', (string) $input[$key])) {
            throw new RuntimeException('Selecione um mês e um ano válidos.', 400);
        }
    }
    $month = (int) $input['mes'];
    $year = (int) $input['ano'];
    if ($month < 1 || $month > 12 || $year < 1900 || $year > 2100) {
        throw new RuntimeException('Selecione um mês de 1 a 12 e um ano entre 1900 e 2100.', 400);
    }
    $start = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month), new DateTimeZone('America/Recife'));
    $months = [1 => 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    return $definition + [
        'type' => $type, 'month' => $month, 'year' => $year,
        'start' => $start->format('Y-m-d'), 'end' => $start->modify('+1 month')->format('Y-m-d'),
        'period' => $months[$month] . ' de ' . $year,
        'note' => $definition['financial']
            ? 'Inclui pagamentos pela data do pagamento, dentro do mês selecionado.'
            : 'Mês/ano de referência. Dados atuais na emissão; este relatório não representa um histórico do período.',
    ];
}

function reportRows(PDO $db, string $sql, array $params = []): array
{
    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function buildReport(PDO $db, array $request, array $session): array
{
    $report = $request + ['issued' => (new DateTimeImmutable('now', new DateTimeZone('America/Recife')))->format('d/m/Y H:i'), 'students' => [], 'groups' => [], 'payments' => [], 'totalCents' => 0];
    // Personal reports always resolve the owner from the authenticated session, never a request ID.
    if (!$request['admin']) {
        $report['students'] = reportRows($db,
            'SELECT a.*, t.horario AS horario_turma, t.piscina FROM alunos a LEFT JOIN turmas t ON t.id_turma = a.id_turma WHERE a.id_aluno = ? AND a.cpf = ?',
            [$session['id_aluno'], $session['cpf']]);
        if (!$report['students']) {
            throw new RuntimeException('Cadastro do aluno não encontrado.', 404);
        }
    }
    if ($request['financial']) {
        $sql = 'SELECT p.*, a.nome AS aluno_nome, a.cpf AS aluno_cpf FROM pagamentos p JOIN alunos a ON a.id_aluno = p.aluno_id WHERE p.data_pagamento >= ? AND p.data_pagamento < ?';
        $params = [$request['start'], $request['end']];
        if (!$request['admin']) {
            $sql .= ' AND p.aluno_id = ?';
            $params[] = $session['id_aluno'];
        }
        $report['payments'] = reportRows($db, $sql . ' ORDER BY p.data_pagamento, a.nome, p.id', $params);
        foreach ($report['payments'] as $payment) {
            $report['totalCents'] += (int) round((float) $payment['valor'] * 100);
        }
    } elseif ($request['type'] === 'alunos') {
        $report['students'] = reportRows($db, 'SELECT a.*, t.horario AS horario_turma, t.piscina FROM alunos a LEFT JOIN turmas t ON t.id_turma = a.id_turma ORDER BY a.nome, a.id_aluno');
    } elseif ($request['type'] === 'turmas') {
        $report['groups'] = reportRows($db, 'SELECT id_turma, horario, piscina FROM turmas ORDER BY horario, id_turma');
        $report['groups'] = array_values(array_filter($report['groups'], fn($group) => !turmaSemDefinicao($group['horario'])));
        $students = reportRows($db, 'SELECT id_aluno, id_turma, nome, cpf, contato, data_nascimento FROM alunos ORDER BY nome, id_aluno');
        $byGroup = [];
        foreach ($students as $student) {
            $byGroup[(string) $student['id_turma']][] = $student;
        }
        foreach ($report['groups'] as &$group) {
            $group['students'] = $byGroup[$group['id_turma']] ?? [];
        }
        unset($group);
        $knownGroups = array_column($report['groups'], 'id_turma');
        $unassigned = array_values(array_filter($students, fn($student) => !in_array($student['id_turma'], $knownGroups)));
        if ($unassigned) {
            $report['groups'][] = ['id_turma' => null, 'horario' => 'Sem turma definida', 'piscina' => '', 'students' => $unassigned];
        }
    }
    return $report;
}
