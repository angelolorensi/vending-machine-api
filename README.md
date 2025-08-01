# Sistema de Máquinas de Venda Automatizadas

Um sistema completo de máquinas de venda com controle de cartões de funcionários, desenvolvido para ambientes corporativos. O projeto integra backend Laravel, frontend React/TypeScript e painel administrativo Filament.

## Funcionalidades

### Sistema de Cartões e Pontos
- **Cartões de funcionários** com recarga automática diária
- **Controle de limites** por categoria de produto (bebidas, lanches, refeições)
- **Histórico completo** de transações
- **Verificação de saldo** em tempo real

### Gerenciamento de Máquinas
- **Múltiplas máquinas** por localização
- **Sistema de slots** com numeração única
- **Gestão automática** de estoque
- **Produtos categorizados** com cores visuais

### Interface do Usuário
- **Simulação realística** da máquina de venda
- **Visor de funcionário** com informações do cartão
- **Seleção intuitiva** de produtos
- **Feedback visual** de status e erros

### Dashboard Administrativo
- **Painel Filament** com CRUD completo
- **Dashboard analytics** com widgets personalizados
- **Relatórios em tempo real** de transações
- **Gráficos interativos** por categoria e período

## Stack Tecnológica

### Backend
- **Laravel 10** com arquitetura de serviços
- **PostgreSQL** com relacionamentos complexos
- **API REST** com documentação completa
- **PHPUnit** para testes unitários
- **Filament PHP** para painel administrativo

### Frontend
- **React 18** com TypeScript
- **Tailwind CSS** para estilização
- **Componentização modular**
- **Estados globais** para gerenciamento de dados

### DevOps
- **Docker** com Laravel Sail
- **Git** com controle de versão
- **Seeders** para dados de teste

## Pré-requisitos

- Docker (com Docker Compose plugin)
- Node.js 18+
- Composer 2+ (opcional, usado via Docker)

## Instalação e Configuração

### Início Rápido (Recomendado)

Para desenvolvedores que querem começar rapidamente:

```bash
git clone <repository-url>
cd vending-machine-api
./dev.sh
```

O script `dev.sh` automaticamente:
- Verifica todas as dependências (Docker, Node.js)
- Cria o arquivo `.env` se não existir
- Instala dependências do Composer e NPM
- Inicia os containers Docker
- Executa migrations e seeders
- Inicia o Vite para desenvolvimento
- Exibe URLs e informações úteis

### Instalação Manual

Se preferir fazer passo a passo:

#### 1. Clone o repositório
```bash
git clone <repository-url>
cd vending-machine-api
```

#### 2. Configure o ambiente
```bash
cp .env.example .env
```

#### 3. Instale as dependências
```bash
# Backend
composer install

# Frontend
npm install
```

#### 4. Inicie os containers
```bash
./vendor/bin/sail up -d
```

#### 5. Configure o banco de dados
```bash
# Execute as migrations
./vendor/bin/sail php artisan migrate

# Popule com dados de teste
./vendor/bin/sail php artisan db:seed
```

#### 6. Compile os assets do frontend
```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

#### 7. Crie um usuário administrador
```bash
./vendor/bin/sail php artisan make:filament-user
```

### Scripts NPM Disponíveis

```bash
# Configuração inicial completa
npm run setup

# Iniciar desenvolvimento (backend + frontend)
npm run start

# Parar todos os serviços
npm run stop

# Apenas frontend (com backend já rodando)
npm run dev

# Build de produção
npm run build
```

## Como Usar

### Interface da Máquina de Venda
1. Acesse `http://localhost` 
2. Insira um número de cartão (ex: `CARD001`)
3. Verifique o cartão para ver saldo e limites
4. Selecione um produto usando os botões numéricos
5. Confirme a compra

### Painel Administrativo
1. Acesse `http://localhost/admin`
2. Faça login com suas credenciais
3. Gerencie máquinas, produtos, funcionários e cartões
4. Visualize o dashboard de transações

## Testes

Execute a suíte de testes completa:

```bash
# Todos os testes
./vendor/bin/sail php artisan test

# Testes específicos
./vendor/bin/sail php artisan test tests/Unit/PurchaseProductActionTest.php

# Com cobertura
./vendor/bin/sail php artisan test --coverage
```

## Estrutura do Projeto

```
app/
├── Actions/          # Lógica de negócio complexa
├── Services/         # Acesso a dados e operações
├── Http/
│   ├── Controllers/  # Controllers da API
│   ├── Resources/    # Transformação de dados
│   └── Requests/     # Validação de entrada
├── Models/           # Modelos Eloquent
├── Filament/         # Painel administrativo
└── Exceptions/       # Exceptions customizadas

resources/
├── js/
│   ├── components/   # Componentes React
│   ├── pages/        # Páginas principais
│   ├── services/     # Serviços do frontend
│   └── types/        # Definições TypeScript
└── views/            # Views Blade

tests/
├── Unit/             # Testes unitários
└── Feature/          # Testes de integração
```

## API Endpoints

### Autenticação e Cartões
- `POST /api/cards/verify` - Verificar cartão
- `GET /api/cards/{id}` - Obter detalhes do cartão

### Máquinas e Produtos  
- `GET /api/machines` - Listar máquinas
- `GET /api/machines/{id}?with=slots` - Detalhes da máquina com slots

### Transações
- `POST /api/purchase` - Realizar compra
- `GET /api/transactions` - Listar transações
- `GET /api/transactions/{id}` - Detalhes da transação

## Comandos Artisan Personalizados

```bash
# Recarregar pontos diários de todos os cartões
./vendor/bin/sail php artisan cards:daily-recharge

# Preencher slots vazios automaticamente
./vendor/bin/sail php artisan slots:fill
```
