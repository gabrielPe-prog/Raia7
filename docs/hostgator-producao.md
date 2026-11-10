# Ativação em produção — Raia7

O redirecionamento público foi verificado: o domínio raiz encaminha para `Raia7/app/index.php`.

- URL da aplicação: `https://raia7.codewave.net.br/Raia7/app`
- Webhook: `https://raia7.codewave.net.br/Raia7/app/webhook.php`
- Administração das faturas: `https://raia7.codewave.net.br/Raia7/app/faturas.php`
- Aluno: `https://raia7.codewave.net.br/Raia7/app/pagamentosAluno.php`

## 1. Credenciais privadas

Copie `mercadopago.local.example.php` como `mercadopago.local.php` para a pasta imediatamente acima da raiz pública do subdomínio, NÃO para dentro de `Raia7/`.

Se a raiz pública configurada no cPanel for `/home/USUARIO/raia7.codewave.net.br`, coloque o arquivo em `/home/USUARIO/mercadopago.local.php`. Confirme a raiz do documento em Domínios no cPanel. O código utiliza `DOCUMENT_ROOT` do PHP para encontrar essa pasta pai.

O arquivo deve ficar fora de todas as pastas públicas da hospedagem. Se a pasta pai também for pública, configure a variável de ambiente `MERCADO_PAGO_CONFIG_FILE` com um caminho absoluto privado alternativo. Essa variável também permite usar a reconciliação via CLI com o mesmo arquivo.

Preencha somente:
- `MERCADO_PAGO_ACCESS_TOKEN`: Access Token de produção.
- `MERCADO_PAGO_COLLECTOR_ID`: ID numérico da conta recebedora correspondente ao token.
- `MERCADO_PAGO_WEBHOOK_SECRET`: assinatura secreta de Webhooks dessa aplicação.

A URL final e `MERCADO_PAGO_LIVE_MODE=true` já estão preenchidos no modelo. Não envie credenciais por chat nem publique o arquivo. Variáveis de ambiente não vazias têm prioridade sobre o arquivo privado.

## 2. Arquivos do sistema

Envie a aplicação atualizada para `raia7.codewave.net.br/Raia7/app/`, incluindo `vendor/` gerado por `composer install` e as novas páginas e serviços. Preserve a conexão de produção em `service/connection_create.php`, os uploads e outros dados existentes da hospedagem; não substitua a conexão por aquela do Docker.

Use PHP 8.2 ou superior com cURL e PDO MySQL. Faça backup dos arquivos atuais antes de substituí-los. O SQL já foi executado na hospedagem, conforme informado; confira que as três tabelas `mp_faturas`, `mp_checkouts` e `mp_transacoes` estão no mesmo banco usado pelo sistema.

## 3. Webhook no Mercado Pago

No ambiente de produção da aplicação, cadastre:

`https://raia7.codewave.net.br/Raia7/app/webhook.php`

Selecione o evento de pagamentos da integração via Preferences/Payments (no painel apresentado: **Pagamentos (legacy)**). Copie a assinatura secreta para o arquivo privado. A página de webhook não é uma tela: abri-la no navegador retorna 405 porque aceita POST.

## 4. Validação após publicação

Entre como administrador e crie uma fatura para o aluno que participará da validação. Entre como esse aluno e abra o Checkout Pro. O pagamento em produção movimenta dinheiro real; efetue apenas a cobrança autorizada pelo responsável. Depois confira o recebimento no painel e a fatura paga no sistema, inclusive sem depender do retorno do navegador.

O código ainda não foi publicado nem testado com a API de produção. A validação completa depende do upload, das credenciais e da entrega do webhook. Os PDFs/relatórios antigos continuam separados dos novos recebimentos; as novas transações aparecem em Faturas.
