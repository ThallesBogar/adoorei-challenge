# Introdução

Esta API foi desenvoldida como parte do desafio técnico no processo seletivo para a vaga de Pessoa Desenvolvedora Back-end na Adoorei.

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

Para o levantamento dos serviços, foi utilizado o Docker Compose, com os seguintes serviços:
- PHP 8.0
- Nginx
- MySQL 8.0

# Pré requisitos
Para executar a API, é necessário ter instalado o [Docker](https://docs.docker.com/get-docker/) e o [Docker Compose](https://docs.docker.com/compose/install/).

# Como executar
- Clonar repositório na sua máquina ou utilizar o Codespaces
- Executar o arquivos `init.sh` localizado na raiz do projeto, através do comando `./init.sh`
- Ou se preferir, você pode executar os seguintes comandos:
  - `docker-compose build`
  - `docker-compose up -d`
  - `docker-compose exec app composer install`
  - `docker-compose exec app php artisan migrate`
  - `docker-compose exec app php artisan db:seed`
- A API estará disponível em `http://localhost:80/api`

# Como executar os testes
Para executar os testes, basta executar o comando `docker-compose exec api php artisan test`



