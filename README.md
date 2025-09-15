# AutoConf Vehicles API

API REST para gerenciamento de veículos desenvolvida em Laravel 12.

## 🚗 **Sobre o Projeto**

Sistema SaaS multiusuário para gestão de veículos com:
- CRUD completo de veículos
- Upload múltiplo de imagens com capa única
- Autenticação e autorização por policies
- Documentação Swagger completa
- Testes automatizados

## 🏗️ **Arquitetura**

### **Estrutura:**
- **Controllers**: Endpoints da API
- **Services**: Lógica de negócio
- **Policies**: Autorização
- **Form Requests**: Validações
- **Factories**: Dados de teste
- **Seeders**: Dados de exemplo

## 🛠️ **Instalação e Setup**

### **Pré-requisitos**
- PHP 8.3 ou superior
- Composer
- MySQL 8.0 ou superior

### **Passos para instalação**

1. **Clone o repositório**
   ```bash
   git clone https://github.com/seu-usuario/autoconf-vehicles-api.git
   cd autoconf-vehicles-api
   ```

2. **Instale as dependências**
   ```bash
   composer install
   ```

3. **Configure o ambiente**
   ```bash
   cp .env.example .env
   ```

4. **Configure o banco de dados no `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=autoconf_vehicles
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

5. **Gere a chave da aplicação**
   ```bash
   php artisan key:generate
   ```

6. **Execute as migrações**
   ```bash
   php artisan migrate
   ```

7. **Crie o link simbólico para storage**
   ```bash
   php artisan storage:link
   ```

8. **Execute os seeders**
   ```bash
   php artisan db:seed
   ```

9. **Inicie o servidor**
   ```bash
   php artisan serve
   ```

A API estará disponível em: **http://localhost:8000/api/v1**

## 🔐 **Autenticação (Sanctum - PAT)**

### **Usuários Seed Criados:**
- **Admin**: `admin@autoconf.com` | Senha: `password`
- **Usuário**: `joao@autoconf.com` | Senha: `password`
- **Seu usuário**: `ximas@autoconf.com` | Senha: `password`

### **Como Autenticar:**

#### **1. Login e obter token:**
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@autoconf.com",
    "password": "password"
  }'
```

**Resposta:**
```json
{
  "success": true,
  "message": "Login realizado com sucesso",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@autoconf.com"
    },
    "token": "1|abc123...xyz789"
  }
}
```

#### **2. Usar token nas requisições:**
```bash
curl -X GET http://localhost:8000/api/v1/vehicles \
  -H "Authorization: Bearer 1|abc123...xyz789" \
  -H "Accept: application/json"
```

## 📚 **Documentação da API**

### **Swagger UI:**
- **URL**: http://localhost:8000/api/documentation
- Documentação completa com exemplos

## 🧪 **Testes**

### **Executar testes:**
```bash
# Testes específicos
php artisan test tests/Feature/VehicleManagementTest.php
```

## 🔧 **Comandos Úteis**

```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Gerar documentação Swagger
php artisan l5-swagger:generate

# Ver rotas da API
php artisan route:list --path=api
```

## 🔗 **Links Úteis**

- **API Base**: http://localhost:8000/api/v1/
- **Swagger**: http://localhost:8000/api/documentation
