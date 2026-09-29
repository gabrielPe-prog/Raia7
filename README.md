# Projeto de Aplicação Web para Academia Aquática R7

Sobre o Projeto

Bem-vindo ao Projeto de Software para a Academia Aquática R7! Este projeto tem como objetivo desenvolver uma solução personalizada em PHP para atender às necessidades específicas da Academia Aquática R7, localizada em Santa Cruz do Capibaribe.
Funcionalidades

    Portfólio Online: O software servirá como uma vitrine virtual para a Academia Aquática R7, apresentando seus serviços, instalações, horários e equipe.
    Controle de Alunos: Gerencie informações dos alunos, incluindo inscrições, histórico de pagamentos, e dados pessoais.
    Controle Financeiro: Acompanhe as finanças da academia, incluindo mensalidades, pagamentos, e geração de relatórios financeiros.

Tecnologias Utilizadas

    PHP
    MySQL
    HTML/CSS
    JavaScript

Como Utilizar

    Clone este repositório.
    Configure seu ambiente de desenvolvimento.
    Execute o software em seu servidor local.
    Explore as funcionalidades e envie feedback!

Equipe CodeWave

Este projeto é mantido por uma equipe dedicada de desenvolvedores apaixonados pela natação e pela tecnologia. Junte-se a nós!

Contato

Para mais informações, entre em contato conosco em codewave.ad@gmail.com.

## Exportação em PDF

As telas de alunos, turmas, financeiro, informações do aluno, carteirinha,
histórico de pagamentos e pagamentos possuem o botão **Exportar PDF**.
Informações Gerais, login e cadastro não exibem relatórios.

- Na carteirinha, **Exportar PDF** baixa diretamente os dados atuais do aluno, sem mês e ano.
- Nas demais telas, escolha mês e ano e clique em **Baixar PDF**.
- Financeiro e pagamentos usam a **data do pagamento**, incluindo o primeiro e o
  último dia do mês. Registros sem data de pagamento não pertencem a um período.
- Alunos, turmas e informações pessoais mostram os **dados atuais**;
  mês e ano indicam a referência, não uma reconstrução histórica.
- Os PDFs incluem todos os registros do relatório, independentemente da busca e
  da paginação da tela. Períodos sem pagamentos geram um relatório vazio com total zero.
- Relatórios administrativos exigem nível 1. Relatórios pessoais usam o aluno
  da sessão autenticada; não aceitam escolher outro aluno pela URL.

A geração usa [Dompdf](https://github.com/dompdf/dompdf), instalada pelo Composer.
Após baixar o projeto ou atualizar as dependências, execute:

```sh
cd app
composer install --no-dev --optimize-autoloader
```

Com o ambiente Docker em execução, o equivalente é:

```sh
docker compose exec app composer install --no-dev --optimize-autoloader
```

O PHP precisa de DOM, mbstring e GD (já instalados pelo Dockerfile). A exportação
usa imagens e fontes locais, sem enviar dados a serviços externos.

Para validar filtros, permissões, totais e geração dos sete tipos de PDF, execute
com PHP e PDO SQLite disponíveis:

```sh
php app/tests/reportExportTest.php
```

Um diretório opcional permite salvar PDFs de exemplo com dados fictícios:

```sh
php app/tests/reportExportTest.php /tmp/raia-report-samples
```
