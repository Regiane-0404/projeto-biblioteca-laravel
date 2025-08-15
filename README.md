# 📚 Biblioteca - Sistema de Gestão e E-commerce

![Screenshot do Dashboard do Projeto](./assets/screenshot.png)

Um projeto Laravel completo que implementa um sistema de gestão de biblioteca com funcionalidades de requisição de livros e um fluxo de e-commerce para venda, incluindo um painel de administração robusto, testes automatizados e sistema de logs.

---

## 🛠️ Stack Tecnológica

*   **Framework:** Laravel 12
*   **Frontend:** Blade, Tailwind CSS, DaisyUI
*   **Base de Dados:** SQLite
*   **Testes:** Pest PHP
*   **Pagamentos:** Stripe Checkout
*   **Geração de PDF:** `barryvdh/laravel-dompdf`
*   **Logs de Atividade:** `spatie/laravel-activitylog`

---

## ✨ Funcionalidades Principais

### Para o Utilizador (Cidadão)
- **Navegação e Pesquisa:** Visualização de catálogo de livros, autores e editoras.
- **Requisição de Livros:** Sistema de empréstimo de livros com controlo de limites e devoluções.
- **E-commerce Completo:**
    - **Carrinho de Compras:** Adição de livros para compra, funcional para utilizadores autenticados e visitantes (com fusão no login).
    - **Checkout Seguro:** Processo de checkout em múltiplos passos (morada e pagamento).
    - **Pagamentos via Stripe:** Integração com a página segura do Stripe Checkout, aceitando cartões e Multibanco.
- **Área Pessoal:** Dashboard para visualização do histórico de requisições.

### Para o Administrador
- **Dashboard Analítico:** Visão geral com estatísticas chave da plataforma.
- **Gestão de Catálogo (CRUD Completo):**
    - Gestão de **Livros** (criar, editar, apagar, ativar/inativar).
    - Gestão de **Autores**.
    - Gestão de **Editoras**.
- **Gestão de Requisições:** Aprovação e registo de devoluções.
- **Gestão de Encomendas:**
    - Listagem e filtragem de todas as encomendas.
    - Visualização detalhada de cada encomenda.
    - Alteração manual de estados (Marcar como Pago, Enviada, Cancelar).
    - **Geração de Faturas em PDF**.
- **Gestão de Utilizadores:** CRUD de utilizadores da plataforma.

### Funcionalidades Avançadas
- **Testes Automatizados (Pest):** Suíte de testes para garantir a estabilidade e a qualidade das regras de negócio críticas.
- **Logs de Atividade:** Sistema de auditoria completo que regista todas as ações importantes na plataforma, com uma interface de visualização e filtragem para o administrador.
- **Notificação de Carrinho Abandonado:** Sistema automático que envia emails de lembrete a utilizadores que deixaram itens no carrinho, para recuperação de vendas.

---

## 🚀 Como Instalar e Correr o Projeto

### Pré-requisitos
- PHP (>=8.2)
- Composer
- Node.js & NPM
- Laravel Herd (ou outro ambiente Laravel)

### Passos
1. **Clonar o repositório:**
   ```bash
   git clone [https://github.com/inovcorp-regiane/biblioteca-backup/]
   cd nome-da-pasta-do-projeto
Instalar dependências:

bash
Copiar
Editar
composer install
npm install
Configurar o Ambiente:

bash
Copiar
Editar
cp .env.example .env
php artisan key:generate
Configure no .env a base de dados (SQLite por padrão) e chaves do Stripe.

Migrar a Base de Dados:

bash
Copiar
Editar
php artisan migrate
# Opcional: popular com dados de exemplo
# php artisan db:seed
Criar o Link do Storage:

bash
Copiar
Editar
php artisan storage:link
Compilar Assets e Iniciar Servidor:

bash
Copiar
Editar
npm run dev
⚙️ Tarefas e Testes
Scheduler (Tarefas Agendadas)
Configurar no servidor:

bash
Copiar
Editar
* * * * * cd /caminho/para/o/seu/projeto && php artisan schedule:run >> /dev/null 2>&1
Testes Automatizados
bash
Copiar
Editar
# Todos os testes
vendor/bin/pest

# Teste específico
vendor/bin/pest tests/Feature/RequisicaoTest.php
🤝 Como Contribuir
Faça um fork do projeto.

Crie uma branch para a sua feature/bugfix:

bash
Copiar
Editar
git checkout -b minha-feature
Commit suas alterações:

bash
Copiar
Editar
git commit -m "Minha nova feature"
Envie para o seu fork:

bash
Copiar
Editar
git push origin minha-feature
Abra um Pull Request neste repositório.

