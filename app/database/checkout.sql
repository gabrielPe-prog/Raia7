-- Novas cobranças; não converte recebimentos históricos em dívidas.
CREATE TABLE IF NOT EXISTS mp_faturas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 aluno_id BIGINT NOT NULL, descricao VARCHAR(255) NOT NULL,
 valor DECIMAL(12,2) NOT NULL, vencimento DATE NOT NULL,
 status VARCHAR(30) NOT NULL DEFAULT 'aberta',
 criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX (aluno_id, vencimento)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS mp_checkouts (
 referencia VARCHAR(64) PRIMARY KEY, fatura_id BIGINT UNSIGNED NOT NULL,
 preference_id VARCHAR(255) NULL, url TEXT NULL,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (fatura_id) REFERENCES mp_faturas(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS mp_transacoes (
 payment_id VARCHAR(64) PRIMARY KEY, fatura_id BIGINT UNSIGNED NOT NULL,
 status VARCHAR(40) NOT NULL, valor DECIMAL(12,2) NOT NULL,
 estornado DECIMAL(12,2) NOT NULL DEFAULT 0, metodo VARCHAR(80) NULL,
 aprovado_em VARCHAR(40) NULL, atualizado_em VARCHAR(40) NOT NULL,
 FOREIGN KEY (fatura_id) REFERENCES mp_faturas(id)
) ENGINE=InnoDB;
