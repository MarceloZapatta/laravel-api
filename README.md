# API de exemplo Laravel

## Instruções

Clone do projeto

Instalar dependências composer
  
`composer install`

Copiar o .env.example para a raiz como .env
Criar um novo banco de dados e configurar no .env

Gerar o APP KEY

`php artisan key:generate`

Gerar o secret do JWT

`php artisan jwt:secret`

Rodar as migrations

`php artisan migrate`

Rodar a api:

`php -S 127.0.0.1:8000 -t public`

Rodar os jobs:

`php artisan queue:work`

Armazenar histórico BTC

`php artisan crypt:history`
