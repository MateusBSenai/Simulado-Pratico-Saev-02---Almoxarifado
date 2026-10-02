-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema industria_db
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `industria_db` ;

-- -----------------------------------------------------
-- Schema industria_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `industria_db` DEFAULT CHARACTER SET utf8 ;
USE `industria_db` ;

-- -----------------------------------------------------
-- Table `industria_db`.`usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `industria_db`.`usuarios` ;

CREATE TABLE IF NOT EXISTS `industria_db`.`usuarios` (
  `id` INT NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(45) NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `industria_db`.`estoque`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `industria_db`.`estoque` ;

CREATE TABLE IF NOT EXISTS `industria_db`.`estoque` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(45) NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `preco` DECIMAL(10,2) NOT NULL,
  `estoque_atual` INT NOT NULL DEFAULT 0,
  `estoque_minimo` INT NOT NULL,
  `categoria` VARCHAR(45) NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `industria_db`.`movimentacoes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `industria_db`.`movimentacoes` ;

CREATE TABLE IF NOT EXISTS `industria_db`.`movimentacoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` INT NOT NULL COMMENT '1-entrada, 2-saida',
  `data` DATE NOT NULL,
  `quantidade` INT NOT NULL,
  `saldo_anterior` INT NOT NULL,
  `usuarios_id` INT NOT NULL,
  `estoque_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_movimentacoes_usuarios_idx` (`usuarios_id` ASC) VISIBLE,
  INDEX `fk_movimentacoes_estoque1_idx` (`estoque_id` ASC) VISIBLE,
  CONSTRAINT `fk_movimentacoes_usuarios`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `industria_db`.`usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_movimentacoes_estoque1`
    FOREIGN KEY (`estoque_id`)
    REFERENCES `industria_db`.`estoque` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- ------------
-- INSERÇÕES
-- ------------

-- Tabela: usuarios
INSERT INTO usuarios (id, nome, email, senha) VALUES 
(1, 'Marco', 'Marco@gmail.com', '1234'),
(2, 'Zé', 'Zé@gmail.com', '4321'),
(3, 'Bene', 'Bene@gmail.com', '4132');

-- Tabela: estoque
INSERT INTO estoque (
  id, codigo, nome, categoria, preco, 
  estoque_atual, estoque_minimo, ativo
) VALUES 
(1, 'P001', 'chapa de aço', 'Parte', 20.00, 50, 10, 1),
(2, 'P002', 'parafusos,', 'Peça', 10.00, 50, 10, 1),
(3, 'P003', 'tinta roxa', 'Detalhe', 15.00, 50, 10, 1);

-- Tabela: movimentacoes
INSERT INTO movimentacoes (tipo, data, quantidade, saldo_anterior, usuarios_id, estoque_id) VALUES 
(1, STR_TO_DATE('11/09/2026', '%d/%m/%Y'), 5, 2, 1, 3),
(2, STR_TO_DATE('11/09/2026', '%d/%m/%Y'), 10, 20, 2, 2),
(2, STR_TO_DATE('11/09/2026', '%d/%m/%Y'), 15, 50, 3, 1);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
