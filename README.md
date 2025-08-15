# 📚 Biblioteca - Sistema de Gestão e E-commerce

![Screenshot do Dashboard do Projeto](https://github.com/inovcorp-regiane/biblioteca-backup/raw/main/assets/screenshot.png)

Projeto **Laravel 12** completo que implementa um sistema de gestão de biblioteca com funcionalidades de requisição de livros e um fluxo de e-commerce para venda, incluindo painel administrativo, testes automatizados e sistema de logs.

---

## 🛠️ Stack Tecnológica

- **Framework:** Laravel 12
- **Frontend:** Blade, Tailwind CSS, DaisyUI
- **Base de Dados:** SQLite
- **Testes:** Pest PHP
- **Pagamentos:** Stripe Checkout
- **Geração de PDF:** `barryvdh/laravel-dompdf`
- **Logs de Atividade:** `spatie/laravel-activitylog`

---

## ✨ Funcionalidades

### Para o Utilizador
- **Navegação e Pesquisa:** Visualização do catálogo de livros, autores e editoras.
- **Requisição de Livros:** Empréstimo de livros com controlo de limites e devoluções.
- **E-commerce Completo:**
  - **Carrinho de Compras:** Funciona para utilizadores autenticados e visitantes (com fusão no login).
  - **Checkout Seguro:** Processo em múltiplos passos (morada de envio e pagamento).
  - **Pagamentos via Stripe:** Integração com página segura do Stripe Checkout, aceitando cartões e Multibanco.
- **Área Pessoal:** Dashboard com histórico de requisições.

### Para o Administrador
- **Dashboard Analítico:** Estatísticas chave da plataforma.
- **Gestão de Catálogo (CRUD):**
  - Livros (criar, editar, apagar, ativar/inativar)
  - Autores
  - Editoras
- **Gestão de Requisições:** Aprovação e registo de devoluções.
- **Gestão de Encomendas:**
  - Listagem e filtragem
  - Detalhes de cada encomenda
  - Alteração de estado (Pago, Enviado, Cancelado)
  - **Faturas PDF**
- **Gestão de Utilizadores:** CRUD de utilizadores.

### Funcionalidades Avançadas
- **Testes Automatizados:** Suíte completa com Pest.
- **Logs de Atividade:** Auditoria detalhada de ações importantes.
- **Notificação de Carrinho Abandonado:** Emails automáticos para recuperar vendas.

---

## 🚀 Instalação e Configuração

### Pré-requisitos
- PHP >= 8.2
- Composer
- Node.js & NPM
- Laravel Herd (ou outro ambiente Laravel)

### Passos

1. **Clonar o repositório**
   ```bash
   git clone https://github.com/inovcorp-regiane/biblioteca-backup.git
   cd biblioteca-backup
   
Instalar dependências
cp .env.example .env
php artisan key:generate

No .env, configure a base de dados (SQLite por padrão) e as chaves do Stripe.
Caso use SQLite:

touch database/database.sqlite


Migrar a base de dados

php artisan migrate
# Opcional: popular com dados de exemplo
# php artisan db:seed


Criar link do storage

php artisan storage:link

Iniciar servidor

npm run dev
php artisan serve

⚙️ Tarefas e Testes
Scheduler (Tarefas Agendadas)

Para funcionalidades automáticas (ex.: carrinho abandonado) num servidor de produção, adicione ao cron:

* * * * * cd /caminho/para/o/projeto && php artisan schedule:run >> /dev/null 2>&1

Testes Automatizados
# Executar todos os testes
vendor/bin/pest

# Executar teste específico
vendor/bin/pest tests/Feature/RequisicaoTest.php

🤝 Como Contribuir

Faça um fork do projeto.

Crie uma branch:

git checkout -b minha-feature


Faça commit das alterações:

git commit -m "Adiciona minha nova feature"


Envie para o seu fork:

git push origin minha-feature


Abra um Pull Request neste repositório.
