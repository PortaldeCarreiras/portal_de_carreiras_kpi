-- Estrutura da tabela 'portal_acesso'
CREATE TABLE 'portal_acesso' (
  'id' int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  'codigo' int(11) NOT NULL,
  'portal' varchar(30) NOT NULL,
  'mes_acesso' int(2) NOT NULL,
  'ano_acesso' int(4) NOT NULL,
  'numero_acessos' int(6) NOT NULL,
  'data' datetime NOT NULL,
  PRIMARY KEY ('id')  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura da tabela 'portal_vagas_estagio'
CREATE TABLE 'portal_vagas_estagio' (
  'id' int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  'empresa' varchar(70) NOT NULL,
  'item' int(6) NOT NULL,
  'codigo' int(6),
  'nome_vaga' varchar(50) NOT NULL,
  'data_abertura' date NOT NULL,
  'data_final_candidatar' date NOT NULL,
  'data_previsao_contratacao' date NOT NULL,
  'eixo_formacao' int(2) NOT NULL,
  'confidencial' char(1),
  'responsavel' varchar(50) NOT NULL,
  'responsavel_email' varchar(50),
  'responsavel_telefone' varchar(50),
  'data_alteracao' datetime NOT NULL,
  'revisao' int(4),
  'data' date NOT NULL,
  PRIMARY KEY ('id')  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura da tabela 'portal_saida_estagio'
CREATE TABLE 'portal_saida_estagio' (
  'id' int(11) NOT NULL AUTO_INCREMENT, -- definido AUTO_INCREMENT
  'empresa_estagio' VARCHAR(70) NOT NULL,
  'aluno_codigo' int(10) NOT NULL,
  'aluno_ra_unidade' VARCHAR(3) NOT NULL,
  'aluno_ra_curso' VARCHAR(3) NOT NULL,
  'aluno_ra_ano_sem' VARCHAR(3) NOT NULL,
  'aluno_ra_periodo' VARCHAR(3) NOT NULL,
  'aluno_ra_siga' VARCHAR(3) NOT NULL,
  'aluno_nome' VARCHAR(70) NOT NULL,
  'aluno_eixo' int(2) NOT NULL,
  'aluno_periodo' VARCHAR(15) NOT NULL,
  'aluno_categoria' VARCHAR(15) NOT NULL,
  'aluno_data' date NOT NULL,
	'data_inicio' date NOT NULL,
	'data_final' date NOT NULL,
	'orientador' VARCHAR(50) NOT NULL,
	'resp_empresa' VARCHAR(50) NOT NULL,
  'data_arquivo' date NOT NULL,
  'data' datetime NOT NULL,
  PRIMARY KEY ('id')  -- Chave primária definida
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura da tabela 'planilha_upload'
CREATE TABLE planilha_upload (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arquivo_nome VARCHAR(255) NOT NULL,
    arquivo_tipo VARCHAR(255) NOT NULL,
    arquivo_tamanho INT NOT NULL,
    arquivo_data DATETIME NOT NULL,
    arquivo_data_upload DATETIME NOT NULL,
    arquivo_local_armazenado VARCHAR(255) NOT NULL
);

-- --------------------------------------------------------
-- Alguns comandos
DELETE FROM portal_acesso;  -- Deleta todos itens de uma tabela
TRUNCATE TABLE portal_acesso; -- Deleta todos itens da tabela portal_acesso e Retorna para o ID 01
TRUNCATE TABLE portal_vagas_estagio;  -- Deleta todos itens da tabela portal_vagas_estagio e Retorna para o ID 01
TRUNCATE TABLE portal_saida_estagio;  -- Deleta todos itens da tabela portal_saida_estagio e Retorna para o ID 01
>>>>>>> portal-jffvale/master
