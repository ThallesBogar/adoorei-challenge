-- Criar o banco de dados de testes, se ainda não existir
CREATE DATABASE IF NOT EXISTS `adoorei_test_database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Conceder privilégios ao usuário no banco de dados de testes
GRANT ALL PRIVILEGES ON `adoorei_test_database`.* TO 'adoorei_db'@'%';

-- Aplicar as mudanças de privilégios
FLUSH PRIVILEGES;
