# Introdução

Esta API foi desenvolvida como parte do desafio técnico no processo seletivo para a vaga de Pessoa Desenvolvedora Back-end na Adoorei.

# O que foi desenvolvido
Foram desenvolvidos os seguintes endpoints no formato RESTful:
- `GET /products`             => Listar produtos disponíveis
- `POST /sales`               => Cadastrar nova venda
- `GET /sales`                => Consultar vendas realizadas
- `GET /sales/{id}`           => Consultar uma venda específica
- `POST /sales/{id}/cancel`   => Cancelar uma venda
- `POST /sales/{id}/products` => Cadastrar novas produtos a uma venda

Além disso, foram desenvolvidos 36 testes de integração para os endpoints, utilizando o PestPHP.

As operações com o banco de dados foram implementadas dentro de Commands, para as operações de escrita, e Queries, para operações de leitura, cumprindo com o conceito de Command Query Responsibility Segregation (CQRS).

Para o levantamento dos serviços, foi utilizado o Docker e Docker Compose, com os seguintes serviços:
- PHP 8.0
- Nginx
- MySQL 8.0

# Pré requisitos
Para executar a API, é necessário ter instalado o [Docker](https://docs.docker.com/get-docker/) e o [Docker Compose](https://docs.docker.com/compose/install/).

# Como executar
- Clonar repositório na sua máquina ou utilizar o Codespaces
- Criar o arquivo `.env` na raiz do projeto, com base no arquivo `.env.example` com o seguinte comando: `cp .env.example .env`. Para facilitar, todas as variáveis necessárias já estão preechidas, mas normalmente não seria o caso.
- Executar o arquivo `init.sh` localizado na raiz do projeto, através do comando `./init.sh`
- Ou se preferir, você pode executar os seguintes comandos manualmente:
  - `docker compose build`
  - `docker compose up -d`
  - `docker compose exec api composer install`
  - `docker compose exec api php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
  - `docker compose exec api php artisan l5-swagger:generate`
  - `docker compose exec api php artisan migrate --seed`
- A API estará disponível em `http://localhost:80/api`

**Obs: As portas 80 e 3306 não podem estar em uso.**

# Como executar os testes
Para executar os testes, basta executar o comando `docker-compose exec api php artisan test`

# Documentação da API
A documentação da API foi feita utilizando o Swagger/OpenAPI. Para acessar, basta acessar o link http://localhost/api/docs



