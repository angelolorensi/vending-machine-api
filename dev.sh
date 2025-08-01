#!/bin/bash


echo "Vending Machine API - Development Setup"
echo "================================================"

# Função para verificar se um comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Verificar dependências
echo "Verificando dependências..."

if ! command_exists docker; then
    echo "Docker não encontrado. Por favor, instale o Docker primeiro."
    exit 1
fi

# Verificar Docker Compose (plugin ou standalone)
if ! docker compose version >/dev/null 2>&1 && ! command_exists docker-compose; then
    echo "Docker Compose não encontrado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

if ! command_exists npm; then
    echo "Node.js/NPM não encontrado. Por favor, instale o Node.js primeiro."
    exit 1
fi

echo "Todas as dependências encontradas!"

# Verificar se o .env existe
if [ ! -f .env ]; then
    echo "Arquivo .env não encontrado. Copiando .env.example..."
    cp .env.example .env
    echo "Arquivo .env criado!"
fi

# Verificar se vendor existe
if [ ! -d "vendor" ]; then
    echo "Instalando dependências do Composer..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php82-composer:latest \
        composer install --ignore-platform-reqs
    echo "Dependências do Composer instaladas!"
fi

# Verificar se node_modules existe
if [ ! -d "node_modules" ]; then
    echo "Instalando dependências do NPM..."
    npm install
    echo "Dependências do NPM instaladas!"
fi

# Iniciar containers Docker
echo "Iniciando containers Docker..."
./vendor/bin/sail up -d

# Aguardar containers inicializarem
echo "Aguardando containers inicializarem..."
sleep 10

# Verificar se o banco foi configurado
if ! ./vendor/bin/sail php artisan migrate:status >/dev/null 2>&1; then
    echo "Configurando banco de dados..."
    ./vendor/bin/sail php artisan migrate
    echo "Migrações executadas!"
    
    echo "Populando banco com dados de teste..."
    ./vendor/bin/sail php artisan db:seed
    echo "Seeders executados!"
fi

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Gerando chave da aplicação..."
    ./vendor/bin/sail php artisan key:generate
    echo "Chave da aplicação gerada!"
fi

# Função para limpar ao sair
cleanup() {
    echo "\nParando serviços..."
    kill $VITE_PID 2>/dev/null
    ./vendor/bin/sail down
    echo "Serviços parados!"
    exit 0
}

# Capturar Ctrl+C
trap cleanup SIGINT SIGTERM

# Iniciar Vite em background
echo "Iniciando Vite (frontend)..."
npm run dev &
VITE_PID=$!

# Aguardar um pouco para o Vite inicializar
sleep 5

echo "Desenvolvimento iniciado com sucesso!"
echo "================================================"
echo "URLs disponíveis:"
echo "   Frontend: http://localhost"
echo "   Admin Panel: http://localhost/admin"
echo "   API: http://localhost/api"
echo ""
echo "Cartões de teste:"
echo "   CARD001 (John Smith)"
echo "   CARD002 (Sarah Johnson)"
echo ""
echo "Comandos úteis:"
echo "   ./vendor/bin/sail php artisan make:filament-user - Criar usuário admin"
echo "   ./vendor/bin/sail php artisan test - Executar testes"
echo "   ./vendor/bin/sail php artisan queue:work - Processar filas"
echo ""
echo "Pressione Ctrl+C para parar todos os serviços"

# Manter o script rodando
wait $VITE_PID