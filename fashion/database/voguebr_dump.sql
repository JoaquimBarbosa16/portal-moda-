-- ============================================================
--  VOGUE BR — Dump MySQL
--  IMPORTANTE: Após importar, execute fix_passwords.php
--  para gerar os hashes bcrypt corretos nesta máquina!
--
--  Opção 1 (browser):
--    http://localhost:8080/fix_passwords.php?token=voguebr_setup_2025
--
--  Opção 2 (terminal):
--    php fix_passwords.php
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '-03:00';

CREATE DATABASE IF NOT EXISTS `voguebr`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `voguebr`;

-- ── USERS ────────────────────────────────────────────────────
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120)     NOT NULL,
  `email`      VARCHAR(160)     NOT NULL,
  `password`   VARCHAR(255)     NOT NULL,
  `role`       ENUM('admin','user') NOT NULL DEFAULT 'user',
  `bio`        TEXT             NULL,
  `avatar`     VARCHAR(500)     NULL,
  `active`     TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `idx_role`   (`role`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── NEWS ─────────────────────────────────────────────────────
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `author_id`    INT UNSIGNED  NOT NULL,
  `title`        VARCHAR(300)  NOT NULL,
  `slug`         VARCHAR(320)  NOT NULL,
  `category`     ENUM('Moda','Beleza','Lifestyle','Tendências','Entrevistas','Eventos') NOT NULL DEFAULT 'Moda',
  `status`       ENUM('published','draft','archived') NOT NULL DEFAULT 'draft',
  `excerpt`      TEXT          NOT NULL,
  `content`      LONGTEXT      NOT NULL,
  `image_url`    VARCHAR(500)  NULL,
  `views`        INT UNSIGNED  NOT NULL DEFAULT 0,
  `published_at` DATETIME      NULL,
  `created_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`),
  KEY `idx_status`   (`status`),
  KEY `idx_category` (`category`),
  KEY `idx_author`   (`author_id`),
  CONSTRAINT `fk_news_author`
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── CATEGORIES ───────────────────────────────────────────────
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`  VARCHAR(80)  NOT NULL,
  `slug`  VARCHAR(90)  NOT NULL,
  `color` VARCHAR(7)   NOT NULL DEFAULT '#c9a96e',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── DADOS: users (senha = placeholder, fix_passwords.php vai corrigir) ──
-- A senha aqui é apenas um placeholder inválido.
-- OBRIGATÓRIO rodar fix_passwords.php depois!
INSERT INTO `users` (`id`,`name`,`email`,`password`,`role`,`bio`,`created_at`) VALUES
(1,'Admin Geral',  'admin@vogue.com','admin123','admin','Editora-chefe e administradora do portal VOGUE BR.','2024-01-01 08:00:00'),
(2,'Ana Clara',    'ana@gmail.com',  'PLACEHOLDER_RUN_FIX_PASSWORDS','user', 'Apaixonada por moda, viagens e cultura brasileira.','2024-02-15 10:30:00'),
(3,'Beatriz Lima', 'bea@gmail.com',  'PLACEHOLDER_RUN_FIX_PASSWORDS','user', 'Editora de beleza e lifestyle.','2024-03-10 14:00:00'),
(4,'Carla Mendes', 'carla@gmail.com','PLACEHOLDER_RUN_FIX_PASSWORDS','user', 'Fotógrafa de moda e criadora de conteúdo.','2024-04-20 09:15:00');

-- ── DADOS: categories ────────────────────────────────────────
INSERT INTO `categories` (`name`,`slug`,`color`) VALUES
('Moda','moda','#c9a96e'),('Beleza','beleza','#c0655c'),
('Lifestyle','lifestyle','#5a9a6a'),('Tendências','tendencias','#5a7fa8'),
('Entrevistas','entrevistas','#7a5a9a'),('Eventos','eventos','#c9703a');

-- ── DADOS: news ──────────────────────────────────────────────
INSERT INTO `news` (`id`,`author_id`,`title`,`slug`,`category`,`status`,`excerpt`,`content`,`image_url`,`views`,`published_at`,`created_at`) VALUES

(1,1,'Semana de Moda de Paris 2025: Os Looks que Dominaram as Passarelas',
'semana-moda-paris-2025','Moda','published',
'As tendências que vêm direto de Paris, a capital mundial da moda, com tudo o que você precisa saber desta temporada.',
'A Semana de Moda de Paris 2025 foi uma verdadeira celebração da elegância e inovação. Designers apresentaram coleções que misturaram tradição e vanguarda de forma magistral.\n\nOs destaques ficaram para Valentino, com sua linha monocromática em vermelho intenso, e para a Dior, que apostou em silhuetas fluidas e bordados elaborados.\n\nEntre as grandes revelações esteve a coleção da Balenciaga, que retornou às suas raízes de alta costura com vestidos esculturais dos anos 1950 reinterpretados com materiais modernos.',
'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
1240,'2025-03-15 09:00:00','2025-03-15 08:30:00'),

(2,2,'Tendências de Beleza para o Verão: Pele Iluminada é a Grande Aposta',
'tendencias-beleza-verao-2025','Beleza','published',
'Os cosméticos e técnicas que prometem dominar o verão brasileiro com looks naturais, radiantes e duradouros.',
'O verão 2025 chega com uma promessa clara: pele iluminada e natural. As marcas de beleza apostam em bases leves, glosses em tons nude e blushes em pêssego e terracota.\n\nO skincare ganhou protagonismo absoluto, com séricos de vitamina C e protetor solar colorido como itens indispensáveis.\n\nOlhos esfumados em tons terrosos, lábios glossy e iluminadores estratégicos completam o look da temporada.',
'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&q=80',
980,'2025-03-10 10:00:00','2025-03-10 09:00:00'),

(3,1,'Minimalismo Chic: Como Montar um Armário Cápsula Perfeito em 2025',
'minimalismo-armario-capsula-2025','Lifestyle','published',
'Menos é mais quando se trata de moda consciente. Aprenda a criar combinações infinitas com apenas 30 peças.',
'O conceito de armário cápsula chegou para ficar. Com apenas 30 peças bem escolhidas, é possível criar mais de 100 combinações diferentes.\n\nO segredo está na escolha de cores neutras — branco, preto, bege e camel — aliadas a cortes clássicos e tecidos de qualidade.\n\nA filosofia do "buy less, buy better" ganha força em 2025, com consumidoras investindo em peças atemporais.',
'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=800&q=80',
820,'2025-03-05 11:00:00','2025-03-05 10:00:00'),

(4,1,'Entrevista Exclusiva: A Estilista Brasileira que Conquista Paris',
'entrevista-carol-bassi-paris','Entrevistas','published',
'Conheça a trajetória de Carol Bassi, fenômeno da moda nacional que está conquistando os palcos internacionais.',
'Em entrevista exclusiva, Carol Bassi fala sobre inspirações e os desafios de ser uma mulher brasileira no mercado internacional de moda.\n\n"A moda brasileira tem uma identidade única — cores, texturas, a alegria do nosso povo — e é isso que levo para fora."\n\nCom coleções vendidas em mais de 20 países, a estilista prova que talento brasileiro tem lugar garantido nos maiores centros de moda do mundo.',
'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80',
1560,'2025-02-28 09:00:00','2025-02-28 08:00:00'),

(5,2,'SPFW N°53: Tudo o que Aconteceu no Maior Evento de Moda da América Latina',
'spfw-53-cobertura-completa','Eventos','published',
'Os destaques, os looks e os desfiles do São Paulo Fashion Week — cobertura completa.',
'O SPFW N°53 surpreendeu com apresentações ousadas e forte presença de marcas sustentáveis.\n\nA nova geração de estilistas brasileiros mostrou força com coleções que dialogam com a cultura periférica e o artesanato regional.\n\nO street style no entorno do evento também foi destaque, com fashionistas de todo o Brasil.',
'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
740,'2025-02-20 10:00:00','2025-02-20 09:00:00'),

(6,1,'As Bolsas que Todo Mundo Quer em 2025: Das It-Bags às Peças Artesanais',
'bolsas-it-bags-2025','Tendências','published',
'Das micro-bags às bolsas XXL: descubra quais acessórios dominam o desejo das fashionistas.',
'2025 é o ano das bolsas com personalidade. As micro-bags continuam mas dividem espaço com modelos bucket XXL e clutches estruturadas em couro.\n\nAs bolsas com franjas estão de volta com força total, assim como os modelos de palha e crochê para o verão.\n\nAs marcas brasileiras, como Arezzo e Schutz, apresentaram coleções que rivalizam com as internacionais.',
'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80',
690,'2025-02-15 11:00:00','2025-02-15 10:00:00'),

(7,3,'Sapatos da Temporada: Plataformas, Bico Fino e Botas que Dominam as Ruas',
'sapatos-temporada-2025','Tendências','published',
'Descubra quais calçados vão dominar seu guarda-roupa — do street style à alta costura.',
'Os calçados desta temporada são declarações de estilo. Plataformas em couro chegam com tudo, seja em sandálias, botas ou tênis.\n\nAs botas cano longo seguem como must-have, agora em couro envernizado nas versões preto e vinho.\n\nPara o verão, as sandálias gladiador em versão mini disputam com as flats de tiras finas.',
'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=800&q=80',
510,'2025-02-10 09:00:00','2025-02-10 08:00:00'),

(8,2,'Skincare Minimalista: A Rotina de 3 Passos que Todo Mundo Está Adotando',
'skincare-minimalista-3-passos','Beleza','published',
'Menos produtos, mais eficiência. A rotina de beleza enxuta é a grande tendência do skincare em 2025.',
'A era do skincare com 12 passos ficou para trás. Em 2025 a tendência é: limpeza, hidratação e proteção solar.\n\nDermatologistas alertaram para o perigo de usar muitos ativos simultaneamente. Agora a indústria respondeu com produtos multifuncionais que reúnem vitamina C, ácido hialurônico e FPS em um só frasco.\n\nAs marcas brasileiras Epidrat e Neutrogena lideram esse movimento.',
'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&q=80',
870,'2025-01-28 10:00:00','2025-01-28 09:00:00'),

(9,1,'Fashion Capitals 2025: Nova York, Milão, Paris e Londres em Uma Cobertura',
'fashion-capitals-2025','Eventos','published',
'Cobertura completa das quatro semanas de moda mais importantes do mundo — shows, looks e bastidores.',
'As quatro semanas de moda que definem o que o mundo vai vestir aconteceram entre fevereiro e março de 2025.\n\nNew York abriu com foco em diversidade. London surpreendeu com a nova geração britânica. Milan trouxe luxo clássico com Prada e Gucci. Paris fechou com chave de ouro.\n\nNossos repórteres estiveram em todos os desfiles.',
'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&q=80',
1100,'2025-01-20 09:00:00','2025-01-20 08:00:00'),

(10,1,'Paleta Mocha Mousse: Como Usar a Cor Pantone do Ano em Cada Ocasião',
'paleta-mocha-mousse-pantone-2025','Tendências','draft',
'O tom terroso eleito pela Pantone como cor de 2025 pode ser usado de formas surpreendentes.',
'A cor Mocha Mousse, tom terroso que mistura marrom, nude e bege, foi eleita pela Pantone como a cor de 2025.\n\nEm breve publicaremos o guia completo de como usar essa paleta do verão ao inverno.\n\nRascunho em desenvolvimento.',
'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=800&q=80',
0,NULL,'2025-02-05 14:00:00');

-- ── VIEWS ────────────────────────────────────────────────────
CREATE OR REPLACE VIEW `v_published_news` AS
SELECT n.id,n.title,n.slug,n.category,n.excerpt,n.image_url,n.views,n.published_at,
       u.id AS author_id, u.name AS author_name
FROM news n JOIN users u ON u.id=n.author_id
WHERE n.status='published'
ORDER BY n.published_at DESC;

SET FOREIGN_KEY_CHECKS = 1;

SELECT '✓ Banco voguebr criado!' AS status;
SELECT CONCAT(COUNT(*),' usuários inseridos (senhas = PLACEHOLDER)') AS atencao FROM users;
SELECT CONCAT(COUNT(*),' notícias inseridas') AS info FROM news;
SELECT '⚠ EXECUTE fix_passwords.php AGORA para ativar os logins!' AS IMPORTANTE;
