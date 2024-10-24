-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/10/2024 às 13:33
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

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_tarefas`
--

CREATE TABLE `tab_tarefas` (
  `id` int(11) NOT NULL,
  `nome_tarefa` varchar(80) NOT NULL,
  `desc_tarefa` text NOT NULL,
  `data_tarefa` datetime NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `prioridade` varchar(10) NOT NULL,
  `status_tarefa` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_tarefas`
--

INSERT INTO `tab_tarefas` (`id`, `nome_tarefa`, `desc_tarefa`, `data_tarefa`, `id_usuario`, `prioridade`, `status_tarefa`) VALUES
(48, 'dvdsv', 'sdvsdv', '2023-04-05 13:48:51', 5, '1', 1),
(49, 'dcsdvc', 'dsvsdvbfgb', '2023-04-05 14:21:01', 5, '1', 1),
(50, 'ewfew', 'ewfewf', '2023-04-05 17:13:30', 5, '1', 1),
(51, 'wefwe', 'fewf', '2023-04-05 17:14:26', 13, '1', 0),
(52, 'y', 'ytj', '2023-04-05 17:14:27', 5, '1', 1),
(53, 'yjtyj', 'tyjtyj', '2023-04-05 17:14:29', 5, '1', 1),
(54, 'tyjytj', 'tyjytj', '2023-04-05 17:14:31', 5, '1', 1),
(55, 'tyjytj', 'ytjyt', '2023-04-05 17:14:33', 13, '1', 0),
(56, 'tyjytj', 'ytjyt', '2023-04-05 17:14:34', 5, '1', 1),
(57, 'adm1', ' fgnfgn', '2023-04-12 13:59:42', 5, '1', 0),
(58, 'efwef', 'ewfwef', '2023-04-12 13:59:47', 5, '1', 0),
(59, 'efwef', 'ewfwe', '2023-04-12 13:59:49', 13, '1', 0),
(60, 'adm3', 'ewfwef', '2023-04-12 13:59:50', 5, '1', 0),
(61, 'adm2', ' ewgewg', '2023-04-12 13:59:52', 5, '1', 0),
(62, 'ghgfh', 'gfhfgh', '2023-04-12 13:59:54', 5, '1', 0),
(63, 'jtj', 'ytjtyj', '2023-04-12 13:59:57', 5, '1', 0),
(64, 'yjytj', 'ytjytj', '2023-04-12 13:59:59', 13, '1', 0),
(65, 'fewfwef', 'wefewf', '2023-04-12 14:00:01', 5, '1', 0),
(66, 'egew', 'gweg', '2023-04-12 14:00:03', 5, '1', 0),
(67, 'ewgewg', 'ewgewg', '2023-04-12 14:00:10', 5, '1', 0),
(68, 'ewgewg', 'ewgewg', '2023-04-12 14:00:11', 5, '1', 0),
(69, 'ewgewg', 'ewgewg', '2023-04-12 14:00:13', 5, '1', 0),
(70, 'trrth', 'rthrthrt', '2023-04-12 15:10:52', 5, '1', 0),
(71, 'ewfwe', 'fwef', '2023-04-14 14:04:42', 5, '1', 0),
(72, 'efewf', 'ewfewf', '2023-04-14 16:57:33', 5, '1', 0),
(73, 'ewfwef', 'wefew', '2023-04-14 16:57:50', 5, '1', 0),
(74, 'wefewf', 'ewfew', '2023-04-14 16:57:53', 5, '1', 0),
(75, 'efewf', 'ewfewf', '2023-04-14 16:57:54', 5, '1', 0),
(76, 'efewf', 'ewfewf', '2023-04-14 16:57:57', 5, '1', 0),
(77, 'ewfewf', 'ewfewf', '2023-04-14 16:57:59', 5, '1', 0),
(78, 'ewfewf', 'ewfewf', '2023-04-14 16:58:01', 5, '1', 0),
(79, 'ewfewf', 'ewfewf', '2023-04-14 16:58:02', 5, '1', 0),
(80, 'dvv', 'dvsdvs', '2023-04-17 13:48:52', 5, '1', 1),
(81, 'Número de Acessos ao portal', 'Acessos ao Portal de carreiras', '2024-10-04 19:43:21', 13, '1', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tab_usuarios`
--

CREATE TABLE `tab_usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(40) NOT NULL,
  `senha` varchar(20) NOT NULL,
  `tipo_adm` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tab_usuarios`
--

INSERT INTO `tab_usuarios` (`id`, `usuario`, `senha`, `tipo_adm`) VALUES
(5, 'adm', '123', 1),
(13, 'jff', '345', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tab_tarefas`
--
ALTER TABLE `tab_tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chaveidusuario` (`id_usuario`);

--
-- Índices de tabela `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tab_tarefas`
--
ALTER TABLE `tab_tarefas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de tabela `tab_usuarios`
--
ALTER TABLE `tab_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tab_tarefas`
--
ALTER TABLE `tab_tarefas`
  ADD CONSTRAINT `chaveidusuario` FOREIGN KEY (`id_usuario`) REFERENCES `tab_usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
