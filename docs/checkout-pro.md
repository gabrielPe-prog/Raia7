# Checkout Pro no Raia7

## Instalação

1. Em `app`, execute `composer install` com PHP 8.2 ou superior.
2. Execute `app/database/checkout.sql` no banco MySQL usado pelo Raia7 (por exemplo, pela aba SQL do phpMyAdmin). As novas tabelas são InnoDB. Não há conversão automática de pagamentos históricos.
3. Configure as variáveis de `.env.example` no ambiente do processo PHP:
   - `MERCADO_PAGO_ACCESS_TOKEN`: token privado da conta recebedora.
   - `MERCADO_PAGO_WEBHOOK_SECRET`: assinatura secreta obtida ao configurar Webhooks na aplicação.
   - `MERCADO_PAGO_COLLECTOR_ID`: ID numérico da conta vendedora correspondente ao token.
   - `MERCADO_PAGO_LIVE_MODE`: `false` para testes, `true` para produção; deve corresponder aos pagamentos da conta/ambiente utilizados.
   - `APP_URL`: URL pública HTTPS da pasta `app`, sem barra final. Se o sistema estiver em `/app`, inclua esse caminho.

O arquivo `.env.example` é apenas um modelo. PHP não carrega `.env` automaticamente. No Herd configure o ambiente do serviço PHP; em Docker encaminhe as variáveis no serviço `app`. Reinicie o serviço após configurar. Nunca coloque tokens em arquivos públicos ou versionados.

4. No painel Mercado Pago, selecione notificações **Payments**, cadastre `APP_URL/webhook.php` no ambiente correspondente e copie a assinatura secreta para a variável acima. HTTPS público é obrigatório; localhost precisa de túnel HTTPS. Configure também as notificações de produção antes de liberar cobranças reais.
5. Como administrador, abra **Financeiro → Faturas e pagamentos do Mercado Pago**, crie uma fatura. Como o aluno correspondente, abra **Pagamentos** e clique em **Pagar no Mercado Pago**.
6. Faça a compra com as contas e meios de teste previstos pelo Mercado Pago. O retorno do checkout somente apresenta o estado local. A confirmação vem do webhook após consulta à API. O simulador do painel valida a entrega, mas um ID fictício não quita uma fatura. Alguns fluxos com credenciais de teste não enviam notificações reais; confirme esse comportamento na documentação de testes vigente.

## Comportamento

- Uma URL de checkout é reutilizada por fatura. A criação ocorre sob bloqueio da fatura; falhas desfazem a gravação local. Se a API criar a preferência mas a resposta se perder, uma preferência órfã pode existir; ela não é apresentada ao usuário.
- ID de pagamento único, bloqueio de fatura e transação SQL evitam duplicação local. Eventos atrasados não substituem estados mais recentes.
- Uma tentativa recusada não desfaz outra aprovada. Estornos, contestações, mediações e aprovação duplicada marcam a fatura **Em revisão**; a equipe deve resolver esses casos no painel. Não há reembolso automático nem cobrança automática de saldo.
- O webhook verifica assinatura via SDK, ID, referência, conta recebedora, ambiente, moeda e valor. Falhas transitórias retornam 503 para permitir nova entrega.
- Novas faturas e transações ficam em tabelas próprias. Os relatórios/PDFs e recebimentos presenciais antigos continuam referentes à tabela `pagamentos`; consulte os novos recebimentos em `faturas.php`. A consolidação desses relatórios, recorrência e geração mensal automática ficam para a próxima etapa.
- O webhook excluído anteriormente foi recriado como endpoint do Checkout Pro; a exclusão de `controllerVisualizaPag.php` foi preservada.

## Recuperação de notificações

Para consultar novamente um pagamento conhecido, execute no servidor, com as mesmas variáveis de ambiente:

```sh
php app/reconciliar_pagamento.php ID_DO_PAGAMENTO
```

O script consulta a API e aplica as mesmas validações do webhook. Não cria pagamentos. Use para recuperar uma notificação perdida; verifique o histórico no painel para descobrir o ID. Ainda não há busca/agendamento automático de reconciliação.

## Validação

```sh
php app/tests/checkoutTest.php
```

Teste também com MySQL e a aplicação publicada: acesso de outro aluno, CSRF inválido, cliques simultâneos, aprovação, recusa, pagamento pendente, fechamento da aba antes do retorno, webhook repetido, estorno e duas aprovações. Os testes locais usam SQLite e clientes simulados, sem movimentar dinheiro. No Docker, que não possui PDO SQLite, execute `docker compose exec -e CHECKOUT_TEST_MYSQL=1 app php tests/checkoutTest.php`: usa tabelas temporárias MySQL isoladas da base real e clientes simulados.

Referências:
- https://mercadopago.github.io/sdk-php/
- https://www.mercadopago.com.br/developers/pt/docs/checkout-pro-preferences/payment-notifications

## Docker local

Na raiz do repositório, copie `.env.example` para `.env` e preencha localmente. O Compose já encaminha essas variáveis ao PHP. Recrie apenas o serviço da aplicação:

```sh
docker compose up -d --build app
```

Use `APP_URL` com a URL HTTPS de um túnel apontando para a porta local 8010. A URL precisa estar acessível ao Mercado Pago; `http://localhost:8010` não serve como webhook público. Cadastre o mesmo endereço no painel. Ao mudar a URL do túnel, recrie o serviço e use uma nova fatura de teste: preferências já criadas mantêm as URLs anteriores.

## HostGator

1. Verifique no painel da hospedagem que PHP 8.2+ está ativo, com cURL e PDO MySQL. Envie o conteúdo de `app/`, incluindo `vendor/` instalado pelo Composer, para a pasta pública do sistema.
2. Se a pasta pública for `/home/USUARIO/public_html`, copie `mercadopago.local.example.php` para `/home/USUARIO/mercadopago.local.php` e preencha pelo gerenciador de arquivos. Esse arquivo fica fora de `public_html`. Restrinja leitura ao usuário da hospedagem (por exemplo, permissão 600 se compatível com o PHP). O carregador busca o arquivo um nível acima de `DOCUMENT_ROOT`, a raiz pública definida pelo servidor.
3. Se publicar em subpasta, mantenha o arquivo privado fora da raiz pública; configure `MERCADO_PAGO_CONFIG_FILE` com o caminho absoluto privado, se a pasta pai de `DOCUMENT_ROOT` também for pública. Não coloque credenciais em uma subpasta pública.
4. Preencha `APP_URL` com o domínio HTTPS e eventual subpasta. Configure o banco da hospedagem na conexão já usada pelo sistema e execute `database/checkout.sql` no phpMyAdmin.
5. Cadastre `https://SEU-DOMINIO/webhook.php` (ou com a subpasta correspondente) no painel Mercado Pago. Use credenciais e assinatura do ambiente correto; configure `MERCADO_PAGO_LIVE_MODE` como `true` somente para pagamentos de produção.

O arquivo privado é alternativa às variáveis de ambiente. Variáveis não vazias têm prioridade. O domínio final e as credenciais precisam ser preenchidos pelo responsável pela hospedagem; não foram definidos pelo código.

Para o domínio real informado, siga [HostGator produção](hostgator-producao.md).
