<?php
function validateStudentProfile(array $input): array
{
    $limits = ['cpf'=>14, 'nome'=>255, 'escola'=>50, 'serie_escola'=>15, 'contato'=>30, 'cep'=>20, 'endereco'=>255, 'data_nascimento'=>10, 'obs_saude'=>1000];
    $data = [];
    foreach ($limits as $field => $limit) {
        if (!isset($input[$field]) || !is_string($input[$field])) {
            throw new RuntimeException('Preencha os campos do formulário corretamente.', 422);
        }
        $data[$field] = trim($input[$field]);
        if (mb_strlen($data[$field]) > $limit) {
            throw new RuntimeException('Um dos campos ultrapassou o tamanho permitido.', 422);
        }
    }
    if (!preg_match('/^(?:\d{11}|\d{3}\.\d{3}\.\d{3}-\d{2})$/D', $data['cpf'])) {
        throw new RuntimeException('Informe o CPF com 11 dígitos.', 422);
    }
    $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
    if ($data['nome'] === '' || $data['contato'] === '' || $data['endereco'] === '') {
        throw new RuntimeException('Preencha nome, contato e endereço.', 422);
    }
    if (!preg_match('/^\d{10,11}$/', preg_replace('/\D/', '', $data['contato']))) {
        throw new RuntimeException('Informe um telefone com DDD, com 10 ou 11 dígitos.', 422);
    }
    if (!preg_match('/^\d{8}$/', preg_replace('/\D/', '', $data['cep']))) {
        throw new RuntimeException('Informe um CEP com 8 dígitos.', 422);
    }
    $birth = DateTimeImmutable::createFromFormat('!Y-m-d', $data['data_nascimento']);
    if (!$birth || $birth->format('Y-m-d') !== $data['data_nascimento'] || $birth > new DateTimeImmutable('today') || $birth->format('Y') < '1900') {
        throw new RuntimeException('Informe uma data de nascimento válida.', 422);
    }
    return $data;
}

function updateStudentProfile(PDO $db, array $session, array $data): void
{
    if (($session['logged_in'] ?? false) !== true || empty($session['id_aluno']) || empty($session['cpf']) || empty($session['id_user'])) {
        throw new RuntimeException('Sua sessão expirou. Entre novamente.', 401);
    }
    $db->beginTransaction();
    try {
        $owner = $db->prepare('SELECT a.id_aluno FROM alunos a JOIN user u ON u.cpf = a.cpf WHERE a.id_aluno = ? AND a.cpf = ? AND u.id_user = ?');
        $owner->execute([$session['id_aluno'], $session['cpf'], $session['id_user']]);
        if (!$owner->fetchColumn()) {
            throw new RuntimeException('Seu cadastro não foi encontrado.', 404);
        }
        foreach (['alunos' => 'id_aluno', 'user' => 'id_user'] as $table => $idColumn) {
            $duplicate = $db->prepare("SELECT $idColumn FROM $table WHERE REPLACE(REPLACE(TRIM(cpf), '.', ''), '-', '') = ? AND $idColumn <> ?");
            $duplicate->execute([$data['cpf'], $session[$idColumn]]);
            if ($duplicate->fetchColumn()) {
                throw new RuntimeException('Este CPF já está cadastrado em outra conta.', 409);
            }
        }
        $update = $db->prepare('UPDATE alunos SET cpf = ?, nome = ?, escola = ?, serie_escola = ?, contato = ?, cep = ?, endereco = ?, data_nascimento = ?, obs_saude = ? WHERE id_aluno = ? AND cpf = ?');
        $update->execute([$data['cpf'], $data['nome'], $data['escola'], $data['serie_escola'], $data['contato'], $data['cep'], $data['endereco'], $data['data_nascimento'], $data['obs_saude'], $session['id_aluno'], $session['cpf']]);
        $user = $db->prepare('UPDATE user SET cpf = ?, nome = ? WHERE id_user = ? AND cpf = ?');
        $user->execute([$data['cpf'], $data['nome'], $session['id_user'], $session['cpf']]);
        $db->commit();
    } catch (Throwable $error) {
        if ($db->inTransaction()) $db->rollBack();
        throw $error;
    }
}
