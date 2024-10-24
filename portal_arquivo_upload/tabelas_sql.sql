

CREATE TABLE pasta1 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cargo INT
);

CREATE TABLE pasta2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cargo INT,
    salario INT
);

CREATE TABLE pasta3 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    cargo INT,
    salario INT,
    tempo INT
);


CREATE TABLE arquivos_upload (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_arquivo VARCHAR(255),
    tipo_arquivo VARCHAR(50),
    tamanho_arquivo INT,
    dados LONGBLOB,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);







