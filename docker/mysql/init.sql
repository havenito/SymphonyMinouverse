-- Base de données : blog_db
-- Dump complet pour Docker

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Désactiver temporairement les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS=0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Structure de la table category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `category` (`id`, `name`, `description`) VALUES
(3, 'Animaux', ''),
(4, 'Comportement des chats', 'Comprendre le comportement de votre chat permet de mieux répondre à ses besoins et de renforcer votre complicité. Ces articles vous aident à décrypter les attitudes et les habitudes de votre félin.'),
(5, 'Santé et soins des chats', 'Assurer la santé de votre chat nécessite des soins réguliers et une attention particulière. Cette catégorie vous donne les clés pour maintenir votre félin en pleine forme.'),
(6, 'Alimentation et nutrition des chats', 'L\'alimentation est un pilier essentiel de la santé du chat. Offrir une nourriture adaptée et équilibrée à votre félin garantit une vie longue et en bonne santé.'),
(7, 'Jeux et stimulation mentale', 'Les chats ont besoin de stimulation physique et mentale pour rester en bonne santé. Offrez-leur des jeux variés pour qu\'ils restent actifs et heureux.'),
(8, 'Bien-être et vie quotidienne des chats', 'Le bien-être de votre chat ne se limite pas à sa santé physique. Ces articles explorent des conseils pour enrichir sa vie quotidienne et favoriser son épanouissement émotionnel.');

-- Structure de la table user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_accepted` tinyint(1) NOT NULL DEFAULT '0',
  `warning_count` int(11) NOT NULL DEFAULT '0',
  `is_banned` tinyint(1) NOT NULL DEFAULT '0',
  `banned_at` datetime DEFAULT NULL,
  `ban_reason` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table post
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `published_at` datetime NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5A8A6C8DA76ED395` (`user_id`),
  KEY `IDX_5A8A6C8D12469DE2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `post` (`id`, `title`, `content`, `published_at`, `picture`, `user_id`, `category_id`) VALUES
(8, 'Les Chats U', 'aaaaaaaaaaa', '2024-11-26 20:38:26', '674631c2428f0.jpg', 5, 3),
(14, 'Les secrets du langage des chats : ce que votre félin essaie de vous dire', 'Les chats utilisent un langage corporel très riche pour communiquer avec leurs maîtres et les autres animaux. Contrairement aux chiens, les chats miaulent peu pour échanger entre eux, mais davantage pour attirer l\'attention des humains. La position de leur queue est l\'un des indicateurs les plus révélateurs de leur état d\'esprit : une queue dressée indique un sentiment de sécurité et de confiance, tandis qu\'une queue basse ou entre les pattes traduit de l\'anxiété.\r\nLes oreilles jouent également un rôle crucial dans l\'expression des émotions du chat. Lorsqu\'elles sont orientées vers l\'avant, cela signifie que le chat est curieux ou détendu. En revanche, des oreilles rabattues vers l\'arrière indiquent souvent de la peur ou de l\'agressivité. Enfin, les clignements lents des yeux sont un signe de bienveillance et de confiance. Essayez de cligner lentement des yeux en retour : votre chat pourrait vous répondre de la même manière, renforçant ainsi votre lien.', '2024-11-28 15:38:00', '67488061effc2.jpg', 7, 4),
(15, 'Pourquoi les chats adorent-ils pétrir ?', 'Le pétrissage, souvent appelé « pattounage », est un comportement unique et fascinant des chats. Ce geste, consistant à appuyer alternativement avec leurs pattes avant sur une surface molle comme une couverture ou votre genou, remonte à leur enfance. Les chatons pétrissent le ventre de leur mère pour stimuler la montée de lait lors de l\'allaitement. Cette habitude persiste à l\'âge adulte et est souvent associée à un sentiment de confort et de bonheur.\r\nLorsque votre chat pétrit sur vous, il exprime son attachement et sa confiance. Certains experts pensent également que ce comportement est lié à la territorialité. En effet, les glandes situées sous leurs pattes libèrent des phéromones qui marquent leur environnement. Cela signifie qu\'en pétrissant, votre chat vous revendique comme faisant partie de son territoire.', '2024-11-28 15:49:00', '674882eb9daff.jpg', 7, 4),
(18, 'Pourquoi les chats aiment-ils les boîtes ?', 'Il n\'est pas rare de voir un chat se précipiter dans une boîte dès qu\'il en aperçoit une. Mais pourquoi cette fascination ? La réponse réside dans leurs instincts de survie. Les chats, même domestiques, conservent l\'attitude de leurs ancêtres sauvages. Les boîtes offrent une cachette parfaite pour se protéger des prédateurs et observer leur environnement sans être vus.\r\nDe plus, les boîtes sont des espaces confinés où les chats se sentent en sécurité. La chaleur y est mieux conservée, ce qui est parfait pour ces animaux qui adorent les températures chaudes. Les boîtes leur procurent aussi un sentiment de contrôle. Elles constituent un refuge idéal pour se détendre, se cacher ou jouer.', '2024-11-28 17:34:00', '67489b98edc62.jpg', 7, 4),
(19, 'Comprendre pourquoi les chats aiment les hauteurs', 'Les chats sont des grimpeurs nés, et leur amour pour les hauteurs est ancré dans leur instinct. Dans la nature, les félins grimpent pour mieux observer leur territoire et repérer les dangers ou les proies. Cette position élevée leur offre un avantage stratégique, leur permettant de voir sans être vus.\r\nÀ la maison, les chats recherchent les endroits en hauteur pour des raisons similaires : ils peuvent surveiller ce qui se passe tout en se sentant en sécurité. Offrir à votre chat des perchoirs ou des arbres à chat peut l\'aider à se sentir plus à l\'aise dans son environnement. Cela réduit également les comportements destructeurs, car le chat trouve une occupation naturelle.', '2024-11-28 17:35:00', '67489bd611818.jpg', 7, 4),
(20, 'Pourquoi les chats suivent-ils leurs maîtres partout ?', 'Si votre chat vous suit constamment d\'une pièce à l\'autre, ce comportement peut avoir plusieurs explications. Premièrement, il s\'agit souvent d\'un signe d\'affection et de curiosité. Les chats aiment participer à la vie de leur maître, qu\'il s\'agisse de s\'asseoir à côté de vous pendant que vous travaillez ou de se blottir près de vous lorsque vous vous détendez.\r\nDeuxièmement, cela peut être lié à leur instinct de survie : les chats aiment savoir où se trouvent les personnes qu\'ils considèrent comme des alliés. Certains chats peuvent aussi adopter ce comportement parce qu\'ils s\'ennuient ou cherchent de la stimulation. Veillez à leur fournir des jouets interactifs ou des moments de jeu pour combler ce besoin.', '2024-11-28 17:37:00', '67489c328ab34.jpg', 7, 4),
(21, 'L\'importance des visites régulières chez le vétérinaire', 'Même si votre chat semble en parfaite santé, il est crucial de l\'emmener chez le vétérinaire au moins une fois par an pour un bilan complet. Ces consultations permettent de détecter des problèmes de santé avant qu\'ils ne deviennent graves. Par exemple, les maladies rénales ou les troubles dentaires peuvent se développer sans signes évidents au début.\r\nLes visites régulières permettent également de mettre à jour les vaccins nécessaires, comme ceux contre la rage, le typhus ou le coryza. En outre, le vétérinaire peut conseiller sur la gestion du poids, la nutrition ou la prévention des parasites. La prévention est toujours préférable au traitement, et ces examens annuels garantissent une vie longue et saine à votre compagnon.', '2024-11-28 17:39:00', '67489cdfa15c8.jpg', 7, 5),
(22, 'Comment détecter les signes de maladie chez votre chat ?', 'Les chats sont des animaux discrets lorsqu\'il s\'agit de montrer qu\'ils ne vont pas bien. Pourtant, certains signes peuvent vous alerter. La perte d\'appétit ou une soif excessive peuvent indiquer des problèmes rénaux ou des troubles digestifs. Un chat qui devient subitement apathique ou au contraire agité peut être malade ou souffrir.\r\n\r\nSurveillez également l\'état de son pelage : un pelage terne ou une perte excessive de poils peuvent révéler des carences ou des maladies de peau. L\'utilisation de la litière est aussi un bon indicateur. Si votre chat y va plus souvent ou, au contraire, y évite, cela peut signaler des problèmes urinaires ou digestifs. N\'attendez pas pour consulter un vétérinaire si vous remarquez ces changements : une détection précoce peut faire toute la différence.', '2024-11-28 17:41:00', '67489d5a32068.jpg', 7, 5),
(23, 'Les bienfaits de la stérilisation chez le chat', 'La stérilisation est une procédure bénéfique tant pour la santé que pour le comportement du chat. Elle permet de prévenir certaines maladies graves, comme les infections utérines chez les femelles ou les tumeurs testiculaires chez les mâles. De plus, la stérilisation réduit les comportements indésirables comme le marquage urinaire ou les fugues fréquentes.\r\n\r\nChez les femelles, la stérilisation permet d\'éviter les chaleurs, qui peuvent être inconfortables pour elles et difficiles à gérer pour leurs maîtres. Chez les mâles, elle réduit les comportements agressifs et les bagarres, ce qui diminue également les risques de blessures. Enfin, la stérilisation contribue à lutter contre la surpopulation féline, évitant ainsi que de nombreux chatons ne finissent abandonnés.', '2024-11-28 17:43:00', '67489dbc1b758.jpg', 7, 5),
(24, 'Quels aliments sont toxiques pour les chats ?', 'Bien qu\'ils soient curieux de goûter à tout, certains aliments sont extrêmement dangereux pour les chats. Le chocolat, par exemple, contient de la théobromine, une substance toxique pour eux. Même en petite quantité, il peut provoquer des troubles digestifs, des tremblements ou des crises cardiaques. Les oignons et l\'ail, quant à eux, détruisent les globules rouges et peuvent entraîner une anémie sévère.\r\n\r\nLes produits laitiers, bien qu\'ils semblent inoffensifs, peuvent causer des troubles digestifs car les chats ne digèrent pas bien le lactose. De même, les raisins et les raisins secs sont potentiellement mortels, pouvant provoquer une insuffisance rénale aiguë. Il est donc important de s\'assurer que votre chat n\'a accès qu\'à des aliments adaptés à ses besoins nutritionnels.', '2024-11-28 17:44:00', '67489e0413807.jpg', 7, 5),
(25, 'La santé dentaire chez les chats : pourquoi est-ce important ?', 'La santé bucco-dentaire des chats est souvent négligée, mais elle est essentielle pour leur bien-être général. Les maladies dentaires, comme la gingivite ou la parodontite, peuvent causer des douleurs importantes et affecter leur appétit. Un chat qui refuse de manger peut rapidement perdre du poids et devenir vulnérable à d\'autres maladies.\r\n\r\nPour éviter cela, il est recommandé de brosser régulièrement les dents de votre chat avec un dentifrice spécialement conçu pour lui. Des croquettes spécifiques ou des friandises dentaires peuvent également aider à prévenir l\'accumulation de tartre. Enfin, des visites régulières chez le vétérinaire permettent de vérifier l\'état des dents et de réaliser un détartrage si nécessaire.', '2024-11-28 17:47:00', '67489e92733ee.jpg', 7, 5),
(26, 'Comment choisir la meilleure nourriture pour votre chat ?', 'Face à la multitude d\'options disponibles, choisir la nourriture idéale pour votre chat peut sembler compliqué. Pourtant, il est crucial de privilégier des aliments riches en protéines animales, car les chats sont des carnivores stricts. Optez pour des croquettes ou de la pâtée contenant une forte teneur en viande ou poisson, avec un minimum d\'additifs artificiels.\r\n\r\nÉvitez les aliments riches en céréales ou en glucides, qui peuvent entraîner des problèmes de surpoids ou de diabète à long terme. Une alimentation humide, comme la pâtée, est particulièrement recommandée pour les chats qui boivent peu, car elle favorise leur hydratation. Enfin, n\'hésitez pas à demander conseil à votre vétérinaire pour adapter la nourriture à l\'âge, la race ou l\'état de santé de votre compagnon.', '2024-11-28 17:49:00', '67489f1d8e3e5.jpg', 7, 6),
(27, 'Les bienfaits des aliments riches en protéines pour les chats', 'Les protéines sont essentielles pour le bon fonctionnement du corps du chat. Elles contribuent à la réparation des tissus, au maintien de la masse musculaire et à la production d\'énergie. Les chats, en tant que carnivores stricts, ont un besoin plus élevé en protéines que de nombreux autres animaux domestiques.\r\n\r\nLes aliments riches en protéines de qualité, comme le poulet, le poisson ou le bœuf, sont idéaux pour assurer une bonne santé. Assurez-vous que l\'aliment choisi comporte un pourcentage élevé de protéines animales dans la composition et évitez ceux où les protéines végétales prédominent. Une alimentation équilibrée en protéines garantit un pelage brillant, des griffes solides et une énergie constante chez votre chat.', '2024-11-28 17:50:00', '67489f57d7d15.jpg', 7, 6),
(28, 'Faut-il donner du lait aux chats ?', 'L\'image du chat buvant du lait est bien ancrée dans l\'imaginaire collectif, mais saviez-vous que la plupart des chats adultes sont intolérants au lactose ? En réalité, après le sevrage, la majorité des chats perdent l\'enzyme nécessaire à la digestion du lactose. Offrir du lait peut donc entraîner des troubles digestifs tels que diarrhée, ballonnements ou douleurs abdominales.\r\n\r\nSi vous souhaitez offrir une boisson spéciale à votre chat, il existe des alternatives sans lactose conçues spécialement pour eux. Cependant, la meilleure boisson reste l\'eau fraîche. Veillez à ce qu\'elle soit toujours accessible, surtout si votre chat consomme des croquettes, qui contiennent peu d\'eau. Évitez donc de lui donner du lait de vache et privilégiez une hydratation saine.', '2024-11-28 17:51:00', '67489fa2e5d29.jpg', 7, 6),
(29, 'L\'importance de l\'hydratation chez les chats', 'Les chats, descendants de félins désertiques, ont une faible tendance naturelle à boire de l\'eau. Cependant, une hydratation insuffisante peut causer des problèmes de santé graves, notamment des troubles rénaux et des infections urinaires. Pour encourager votre chat à boire davantage, offrez-lui de l\'eau fraîche et propre chaque jour, et placez plusieurs bols d\'eau dans différentes pièces de la maison.\r\n\r\nLes fontaines à eau pour chats sont également une excellente option, car elles attirent les chats grâce au mouvement continu de l\'eau. L\'alimentation humide, comme la pâtée, est une autre façon d\'augmenter leur apport en liquide. Surveillez attentivement les signes de déshydratation, tels que des gencives sèches ou une perte d\'élasticité de la peau, et consultez un vétérinaire en cas de doute.', '2024-11-28 17:53:00', '67489ff441ac3.jpg', 7, 6),
(30, 'Les aliments faits maison : ce que vous pouvez préparer pour votre chat', 'Préparer des repas maison pour votre chat peut être une excellente idée, mais cela nécessite de respecter certains principes pour garantir une alimentation équilibrée. Les chats ont besoin de protéines animales en grande quantité. Le poulet cuit, le poisson (sans arêtes) et la viande maigre sont de bons choix. Assurez-vous d\'éviter les épices, le sel et les os, qui peuvent être dangereux pour eux.\r\n\r\nÉvitez également les légumes et fruits toxiques, comme l\'oignon, l\'ail ou l\'avocat. Pour compléter le repas, vous pouvez inclure un supplément en taurine, un acide aminé essentiel pour la santé cardiaque et oculaire des chats. N\'oubliez pas de consulter un vétérinaire ou un nutritionniste félin avant de modifier radicalement l\'alimentation de votre chat.', '2024-11-28 17:54:00', '6748a05c7c00f.jpg', 7, 6),
(31, 'Les meilleurs jeux pour stimuler l\'instinct de chasseur de votre chat', 'Les chats sont des chasseurs nés et adorent les jeux qui imitent la chasse. Les cannes à pêche avec des plumes, les balles rebondissantes et les lasers sont parfaits pour satisfaire cet instinct. Jouer avec votre chat permet non seulement de le divertir, mais aussi de renforcer votre lien.\r\n\r\nVeillez à varier les jouets pour éviter qu\'il ne se lasse. Offrez-lui également des moments de jeu en solo avec des jouets interactifs comme des distributeurs de friandises. Cela l\'aidera à rester actif même en votre absence. Le jeu est essentiel pour prévenir l\'obésité et maintenir une bonne santé mentale chez votre chat.', '2024-11-28 17:58:00', '6748a14cdf120.jpg', 7, 7),
(32, 'Comment fabriquer des jouets maison pour votre chat ?', 'Pas besoin de dépenser une fortune pour amuser votre chat ! De nombreux jouets peuvent être fabriqués à partir d\'objets du quotidien. Un simple carton avec des trous découpés devient une excellente cachette. Une boule de papier aluminium roulée fait une balle légère parfaite pour être chassée.\r\n\r\nLes rouleaux de papier toilette peuvent être transformés en puzzles en y glissant des friandises à extraire. Laissez libre cours à votre imagination, tout en veillant à utiliser des matériaux sûrs. Évitez les objets pointus ou toxiques et surveillez toujours votre chat lors de ses premières interactions avec un nouveau jouet.', '2024-11-28 17:59:00', '6748a17fd6a50.jpg', 7, 7);

-- Structure de la table comment
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526CA76ED395` (`user_id`),
  KEY `IDX_9474526C4B89032C` (`post_id`),
  KEY `IDX_9474526CBF2AF943` (`parent_comment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comment` (`id`, `content`, `created_at`, `status`, `user_id`, `post_id`, `parent_comment_id`) VALUES
(1, 'caca', '2024-11-27 13:23:45', NULL, 1, 8, NULL),
(2, 'ddddddddd', '2024-11-29 12:25:00', NULL, 3, 37, NULL),
(3, 'Super article ! J\'ai beaucoup appris sur les chats.', '2025-09-26 13:45:41', 'validé', 14, 8, NULL),
(4, 'Merci pour ces informations très utiles.', '2025-09-26 13:45:41', 'validé', 14, 8, NULL),
(5, 'Ce commentaire est inapproprié et devrait être signalé.', '2025-09-26 13:45:41', 'validé', 14, 8, NULL),
(6, 'J\'aimerais en savoir plus sur ce sujet.', '2025-09-26 13:45:41', 'validé', 14, 8, NULL),
(7, 'Article très intéressant, continuez comme ça !', '2025-09-26 13:45:41', 'validé', 14, 8, NULL),
(8, 'test', '2025-09-26 14:47:47', NULL, 19, 38, NULL),
(9, 'ddd', '2025-09-26 14:50:51', NULL, 19, 38, NULL);

-- Structure de la table comment_report
CREATE TABLE IF NOT EXISTS `comment_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) DEFAULT NULL,
  `reported_by_id` int(11) DEFAULT NULL,
  `reviewed_by_id` int(11) DEFAULT NULL,
  `report_category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` longtext COLLATE utf8mb4_unicode_ci,
  `reported_at` datetime NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9679FD1CF8697D13` (`comment_id`),
  KEY `IDX_9679FD1CF2E7F9ED` (`reported_by_id`),
  KEY `IDX_9679FD1C7C8CA49D` (`reviewed_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comment_report` (`id`, `comment_id`, `reported_by_id`, `reviewed_by_id`, `report_category`, `reason`, `reported_at`, `status`, `reviewed_at`) VALUES
(1, 3, 14, 7, 'Contenu inapproprié', 'Contenu inapproprié - Test automatique', '2025-09-26 13:58:00', 'resolved', '2025-09-26 14:18:07'),
(2, 6, 1, 7, 'offensive_language', NULL, '2025-09-26 13:59:53', 'resolved', '2025-09-26 14:46:10'),
(3, 3, 13, 7, 'misinformation', 'Langage inapproprié et offensant', '2025-09-26 14:10:39', 'resolved', '2025-09-26 14:17:33'),
(4, 4, 13, 7, 'spam', 'Ce commentaire contient des informations fausses sur les chats', '2025-09-26 14:10:39', 'dismissed', '2025-09-26 14:12:55'),
(5, 8, 1, 7, 'inappropriate_content', NULL, '2025-09-26 14:48:35', 'resolved', '2025-09-26 14:49:11');

-- Structure de la table category_suggestion
CREATE TABLE IF NOT EXISTS `category_suggestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suggested_by_id` int(11) DEFAULT NULL,
  `reviewed_by_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BBC0E0CC4BEA8E1` (`suggested_by_id`),
  KEY `IDX_BBC0E0CC7C8CA49D` (`reviewed_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table user_warning
CREATE TABLE IF NOT EXISTS `user_warning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `issued_by_id` int(11) DEFAULT NULL,
  `reason` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_22160D65A76ED395` (`user_id`),
  KEY `IDX_22160D6570BC0FC6` (`issued_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20241125225108', '2024-11-25 22:51:41', 141),
('DoctrineMigrations\\Version20241126083752', '2024-11-26 08:38:22', 667),
('DoctrineMigrations\\Version20241126180106', '2024-11-26 18:01:21', 205),
('DoctrineMigrations\\Version20241126194801', '2024-11-26 19:48:04', 210),
('DoctrineMigrations\\Version20241127143649', '2024-11-27 14:37:16', 232),
('DoctrineMigrations\\Version20250926131813', '2025-09-26 13:24:01', 1022),
('DoctrineMigrations\\Version20250926142652', '2025-09-26 14:27:20', 12),
('DoctrineMigrations\\Version20250926143119', '2025-09-26 14:33:20', 10);

-- Structure de la table messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contraintes pour les tables
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8D12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);

ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526CA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9474526C4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_9474526CBF2AF943` FOREIGN KEY (`parent_comment_id`) REFERENCES `comment` (`id`);

ALTER TABLE `comment_report`
  ADD CONSTRAINT `FK_9679FD1CF8697D13` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`),
  ADD CONSTRAINT `FK_9679FD1CF2E7F9ED` FOREIGN KEY (`reported_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_9679FD1C7C8CA49D` FOREIGN KEY (`reviewed_by_id`) REFERENCES `user` (`id`);

ALTER TABLE `category_suggestion`
  ADD CONSTRAINT `FK_BBC0E0CC4BEA8E1` FOREIGN KEY (`suggested_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_BBC0E0CC7C8CA49D` FOREIGN KEY (`reviewed_by_id`) REFERENCES `user` (`id`);

ALTER TABLE `user_warning`
  ADD CONSTRAINT `FK_22160D65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_22160D6570BC0FC6` FOREIGN KEY (`issued_by_id`) REFERENCES `user` (`id`);

-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS=1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
