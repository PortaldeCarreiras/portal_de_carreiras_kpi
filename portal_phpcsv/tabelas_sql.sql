-- Estrutura da tabela `portal_acesso`
CREATE TABLE `portal_acesso` (
  `id` int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  `codigo` int(11) NOT NULL,
  `portal` varchar(30) NOT NULL,
  `mes_acesso` int(2) NOT NULL,
  `ano_acesso` int(4) NOT NULL,
  `numero_acessos` int(6) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`)  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura da tabela `portal_vagas_estagio`
CREATE TABLE `portal_vagas_estagio` (
  `id` int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  `empresa` varchar(70) NOT NULL,
  `item` int(6) NOT NULL,
  `codigo` int(6),
  `nome_vaga` varchar(50) NOT NULL,
  `data_abertura` date NOT NULL,
  `data_final_candidatar` date NOT NULL,
  `previsao_contratacao` date NOT NULL,
  `eixo_formacao` int(2) NOT NULL,
  `confidencial` char(1),
  `responsavel` varchar(50) NOT NULL,
  `email_responsavel` varchar(50),
  `telefone_responsavel` varchar(50),
  `data_alteracao` datetime NOT NULL,
  `revisao` int(4),
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`)  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura da tabela `portal_saida_estagio`
CREATE TABLE `portal_saida_estagio` (
  `id` int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  `empresa_estagio` varchar(70) NOT NULL,
  `aluno_codigo` int(10) NOT NULL,
  `aluno_ra` int(13) NOT NULL,
  `aluno_nome` varchar(70) NOT NULL,
  `aluno_eixo` int(2) NOT NULL,
  `aluno_periodo` varchar(15) NOT NULL,
  `aluno_categoria` varchar(15) NOT NULL,
  `aluno_data` date NOT NULL,
	`data_inicio` date NOT NULL,
	`data_final` date NOT NULL,
	`orientador` varchar(50) NOT NULL,
	`resp_empresa` varchar(50) NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`)  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------


-- Alguns comandos
DELETE FROM portal_acesso;  -- Deleta todos itens de uma tabela
TRUNCATE TABLE portal_acesso; -- Deleta todos itens de uma tabela e Retorna para o ID 01