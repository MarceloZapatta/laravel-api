# Desafio backend Eduzz

## Instruções

Clone do projeto

Instalar dependências composer
  
`composer install`

Instalar dependências npm

`npm i`

Copiar o .env.example para a raiz como .env
Criar um novo banco de dados e configurar no .env

Gerar o APP KEY

`php artisan key:generate`

Gerar o secret do JWT

`php artisan jwt:secret`

Rodar as migrations

`php artisan migrate`

Build dos assets

`npm run dev`

Rodar a api:

`php -S 127.0.0.1:8000 -t public`

Rodar os jobs:

`php artisan queue:work`

Armazenar histórico BTC

`php artisan crypt:history`

## Observações

Tentei manter os mesmos endpoints da API de exemplo.

#### Item 11) Histórico:

Gerei um novo command no artisan para armazenar o histórico no momento que roda. Sendo assim seria só configurar um CRON para rodar a cada 10 minutos com esse comando.