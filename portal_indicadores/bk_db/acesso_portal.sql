-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/10/2024 às 12:54
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `banco`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acesso_portal`
--

CREATE TABLE `acesso_portal` (
  `id` int(11) NOT NULL,
  `codigo` int(11) NOT NULL,
  `portal` varchar(30) NOT NULL,
  `mes_acesso` int(2) NOT NULL,
  `ano_acesso` int(4) NOT NULL,
  `numero_acessos` int(6) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `acesso_portal`
--

INSERT INTO `acesso_portal` (`id`, `codigo`, `portal`, `mes_acesso`, `ano_acesso`, `numero_acessos`, `data`) VALUES
(1, 16, 'Fatec', 1, 2022, 239, '0000-00-00 00:00:00'),
(1, 17, 'Fatec', 2, 2022, 238, '0000-00-00 00:00:00'),
(1, 18, 'Fatec', 3, 2022, 412, '0000-00-00 00:00:00'),
(1, 19, 'Fatec', 4, 2022, 222, '0000-00-00 00:00:00'),
(1, 20, 'Fatec', 5, 2022, 225, '0000-00-00 00:00:00'),
(1, 21, 'Fatec', 6, 2022, 173, '0000-00-00 00:00:00'),
(1, 22, 'Fatec', 7, 2022, 187, '0000-00-00 00:00:00'),
(1, 23, 'Fatec', 8, 2022, 243, '0000-00-00 00:00:00'),
(1, 24, 'Fatec', 9, 2022, 179, '0000-00-00 00:00:00');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acesso_portal`
--
ALTER TABLE `acesso_portal`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acesso_portal`
--
ALTER TABLE `acesso_portal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
