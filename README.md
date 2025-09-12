# AutoConf Vehicles API

API REST para gerenciamento de veículos desenvolvida em Laravel 12.

## 🛠️ Instalação

### Pré-requisitos

- PHP 8.3 ou superior
- Composer
- MySQL 8.0 ou superior

### Passos para instalação

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

6. **Execute as migrações e seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Crie o link simbólico para storage**
   ```bash
   php artisan storage:link
   ```

8. **Inicie o servidor**
   ```bash
   php artisan serve
   ```

A API estará disponível em: `http://localhost:8000`
