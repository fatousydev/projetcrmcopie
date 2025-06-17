-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 17 juin 2025 à 09:59
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `crm`
--

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ajouter_membre_depuis_prospect` (IN `id_prospect` INT)   BEGIN
    DECLARE done INT DEFAULT 0;

    -- Récupérer les données du prospect
    SELECT 
        numero_membre, statut, NOW() AS date_admission, type, nom_entreprise, effectif, classification,
        fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
        telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id,
        a_beneficie_credit, numero_piece
    INTO
        @numero_membre, @statut, @date_admission, @type, @nom_entreprise, @effectif, @classification,
        @fonction, @telephone, @email, @adresse, @activites, @besoins, @personne_contact, @relation_contact,
        @telephone_contact, @commentaires, @nom, @prenom, @Region, @campagne_id, @caisse_id,
        @a_beneficie_credit, @numero_piece
    FROM prospects
    WHERE id = id_prospect;

    -- Insérer dans la table membre
    INSERT INTO membres (
        numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification,
        fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
        telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id,
        a_beneficie_credit, numero_piece
    ) VALUES (
        @numero_membre, @statut, @date_admission, @type, @nom_entreprise, @effectif, @classification,
        @fonction, @telephone, @email, @adresse, @activites, @besoins, @personne_contact, @relation_contact,
        @telephone_contact, @commentaires, @nom, @prenom, @Region, @campagne_id, @caisse_id,
        @a_beneficie_credit, @numero_piece
    );

    -- ✅ Mettre à jour le statut du prospect
    UPDATE prospects SET statut = 'membre' WHERE id = id_prospect;

    SELECT '✅ Migration effectuée avec succès, le prospect est maintenant membre.' AS message;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `check_prospect` (IN `p_id` INT)   BEGIN
    -- Déclaration des variables
    DECLARE v_num_piece VARCHAR(100);
    DECLARE v_existe_deja INT DEFAULT 0;
    
    -- 1. Récupération du numéro de pièce du prospect
    SELECT numero_piece INTO v_num_piece 
    FROM prospects 
    WHERE id = p_id
    LIMIT 1;
    
    -- 2. Vérification d'existence dans la table membre
    SELECT COUNT(*) INTO v_existe_deja 
    FROM membres 
    WHERE numero_piece = v_num_piece;
    
    -- 3. Retour du résultat
    IF v_existe_deja > 0 THEN
        -- Cas où le numéro existe déjà
        SELECT 
            'ERROR' AS status,
            CONCAT('Le numéro ', v_num_piece, ' existe déjà dans la table membre.') AS message;
    ELSE
        -- Cas où la conversion est possible
        SELECT 
            'SUCCESS' AS status,
            'OK' AS message,
            p_id AS prospect_id,
            v_num_piece AS numero_piece;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Migrer1Prospect` (IN `p_numero_piece` VARCHAR(255), OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE nb INT;

    SELECT COUNT(*) INTO nb FROM membres WHERE numero_piece = p_numero_piece;

    IF nb = 0 THEN
        -- 1. Insérer dans membres
        INSERT INTO membres (
            numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, Prenom, Region, campagne_id, caisse_id, guichet_id, a_beneficie_credit, numero_piece
        )
        SELECT
            numero_membre, 'membre', NOW(), type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id, NULL, a_beneficie_credit, numero_piece
        FROM prospects WHERE numero_piece = p_numero_piece;

        -- 2. Mettre à jour le statut du prospect en 'migré'
        UPDATE prospects SET statut = 'migré' WHERE numero_piece = p_numero_piece;

        -- 3. Message de retour
        SET p_message = 'Migration réussie.';
    ELSE
        SET p_message = 'Erreur : Ce numéro de pièce existe déjà.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Migrer2Prospect` (IN `numero` VARCHAR(50), OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE nb INT;

    SELECT COUNT(*) INTO nb FROM membres WHERE numero_piece = numero;

    IF nb = 0 THEN
        INSERT INTO membres (
            numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, Prenom, Region, campagne_id, caisse_id, guichet_id, a_beneficie_credit, numero_piece
        )
        SELECT
            numero_membre, 'membre', NOW(), type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id, NULL, a_beneficie_credit, numero_piece
        FROM prospects WHERE numero_piece = numero;

        UPDATE prospects SET statut = 'migré' WHERE numero_piece = numero;

        SET p_message = '✅ Migration réussie.';
    ELSE
        SET p_message = '❌ Erreur : Ce numéro de pièce existe déjà dans la table membres.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MigrerProspect` (IN `p_numero_piece` VARCHAR(255), OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE nb INT;

    SELECT COUNT(*) INTO nb FROM membres WHERE numero_piece = p_numero_piece;

    IF nb = 0 THEN
        INSERT INTO membres (
            numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, Prenom, Region, campagne_id, caisse_id, guichet_id, a_beneficie_credit, numero_piece
        )
        SELECT
            numero_membre, statut, NOW(), type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id, NULL, a_beneficie_credit, numero_piece
        FROM prospects WHERE numero_piece = p_numero_piece;

        SET p_message = 'Migration réussie.';
    ELSE
        SET p_message = 'Erreur : Ce numéro de pièce existe déjà.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MigrerUnProspect` (IN `p_numero_piece` VARCHAR(255), OUT `p_message` VARCHAR(255))   BEGIN
    DECLARE nb INT;

    SELECT COUNT(*) INTO nb FROM membres WHERE numero_piece = p_numero_piece;

    IF nb = 0 THEN
        INSERT INTO membres (
            numero_membre, statut, date_admission, type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, Prenom, Region, campagne_id, caisse_id, guichet_id, a_beneficie_credit, numero_piece
        )
        SELECT
            numero_membre, statut, NOW(), type, nom_entreprise, effectif, classification,
            fonction, telephone, email, adresse, activites, besoins, personne_contact, relation_contact,
            telephone_contact, commentaires, nom, prenom, Region, campagne_id, caisse_id, NULL, a_beneficie_credit, numero_piece
        FROM prospects WHERE numero_piece = p_numero_piece;

        SET p_message = 'Migration réussie.';
    ELSE
        SET p_message = 'Erreur : Ce numéro de pièce existe déjà.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `VerifierEtMigrerProspect` (IN `numero` VARCHAR(50))   BEGIN
    DECLARE nb_membre INT DEFAULT 0;
    DECLARE nb_prospect INT DEFAULT 0;
    DECLARE message VARCHAR(255);

    -- Vérifie si le numéro est déjà dans membres
    SELECT COUNT(*) INTO nb_membre FROM membres WHERE numero_piece = numero;

    IF nb_membre > 0 THEN
        SELECT '⚠️ Ce numéro de pièce existe déjà dans la table membres.' AS Alerte;
    ELSE
        -- Vérifie si le numéro est dans prospects
        SELECT COUNT(*) INTO nb_prospect FROM prospects WHERE numero_piece = numero;

        IF nb_prospect > 0 THEN
            -- Appel de la procédure de migration
            CALL Migrer1Prospect(numero, @message);
            SELECT @message AS Résultat;

            -- Affiche les membres ajoutés
            SELECT * FROM membres WHERE numero_piece = numero;
        ELSE
            SELECT '❌ Ce numéro de pièce n’existe ni dans membres ni dans prospects. Veuillez l’ajouter d’abord.' AS Alerte;
        END IF;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `verifier_et_migrer_membre` (IN `id_prospect` INT)   BEGIN
    DECLARE num_piece VARCHAR(100);
    DECLARE existe INT DEFAULT 0;

    -- Récupérer le numéro de pièce du prospect
    SELECT numero_piece INTO num_piece FROM prospects WHERE id = id_prospect;

    -- Vérifier s’il existe déjà dans la table membres
    SELECT COUNT(*) INTO existe FROM membres WHERE numero_piece = num_piece;

    IF existe > 0 THEN
        SELECT CONCAT('⚠️ Le numéro de pièce "', num_piece, '" existe déjà dans la table des membres.') AS message;
    ELSE
        -- Le numéro n’existe pas, procéder à la migration
        CALL ajouter_membre_depuis_prospect(id_prospect);
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `acces_caisses`
--

CREATE TABLE `acces_caisses` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `acces_caisses`
--

INSERT INTO `acces_caisses` (`id`, `utilisateur_id`, `caisse_id`) VALUES
(1, 4, 2),
(4, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `agences`
--

CREATE TABLE `agences` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `code_agence` varchar(50) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `caisses`
--

CREATE TABLE `caisses` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `statut` enum('active','inactive') DEFAULT 'inactive',
  `localisation` varchar(255) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `caisses`
--

INSERT INTO `caisses` (`id`, `nom`, `statut`, `localisation`, `date_creation`) VALUES
(1, 'mecni', 'active', 'pikine', '2025-04-03 15:58:51'),
(2, 'Mecdiam', 'active', 'Diamaguene', '2025-04-03 15:59:41'),
(3, 'Mecson', 'active', 'Keur massar', '2025-04-03 16:00:30'),
(4, 'Mecsomma', 'active', 'Parcelle', '2025-04-03 16:02:31'),
(7, 'Mecpa', 'active', 'Parcelle', '2025-04-24 20:03:21');

-- --------------------------------------------------------

--
-- Structure de la table `campagnes_marketing`
--

CREATE TABLE `campagnes_marketing` (
  `id` int(11) NOT NULL,
  `nom_campagne` varchar(255) NOT NULL,
  `date_lancement` date NOT NULL,
  `date_cloture` date NOT NULL DEFAULT current_timestamp(),
  `cible` varchar(255) NOT NULL,
  `canal_utilise` varchar(255) NOT NULL,
  `resultats` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `campagnes_marketing`
--

INSERT INTO `campagnes_marketing` (`id`, `nom_campagne`, `date_lancement`, `date_cloture`, `cible`, `canal_utilise`, `resultats`, `created_at`, `updated_at`) VALUES
(1, 'campagne de taux de conversion', '2025-03-18', '2025-03-24', 'Prospects', 'SMS', 'Réussie avec succée.', '2025-03-18 10:53:25', '2025-03-18 10:53:25'),
(2, 'campagne de taux de couverture ', '2025-02-11', '2025-03-24', 'Membre', 'Email', 'Réussie avec succée.', '2025-03-10 23:00:00', '2025-03-22 23:00:00'),
(3, 'campagne de taux de recouvrement', '2025-02-11', '2025-03-24', 'Prospect', 'Watchapp', 'Réussie avec succée.', '2025-03-17 23:00:00', '2025-03-23 23:00:00'),
(4, 'campagne de  taux  de  finance', '2025-02-11', '2025-03-24', 'Prospect', 'Email', 'Réussie avec succée.', '2025-03-06 23:00:00', '2025-03-11 23:00:00'),
(5, 'Campagne de lancement', '2025-03-01', '2025-03-31', 'prospect', 'sms', 'Réussi', '2025-03-26 11:25:59', '2025-03-26 11:46:51'),
(6, 'campagne de taux de couverture ', '2025-02-11', '2025-03-24', 'Membre', 'Email', 'Réussie avec succée.', '2025-03-10 23:00:00', '2025-03-22 23:00:00'),
(7, 'campagne dolél djiguéne gni', '2025-02-01', '2025-04-20', 'Prospects', 'Email', 'En cous', '2025-04-05 00:19:53', '2025-04-05 00:19:53'),
(8, 'Campagne and dolél djiguéne yi', '2025-06-15', '2025-07-19', 'Membre_individuel', 'Email', 'En cous', '2025-06-15 13:32:25', '2025-06-15 13:32:25');

-- --------------------------------------------------------

--
-- Structure de la table `campagne_participants`
--

CREATE TABLE `campagne_participants` (
  `id` int(11) NOT NULL,
  `campagne_id` int(11) NOT NULL,
  `participant_cible` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `a_beneficie_credit` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_lancement` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_cloture` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `campagne_participants`
--

INSERT INTO `campagne_participants` (`id`, `campagne_id`, `participant_cible`, `participant_id`, `a_beneficie_credit`, `created_at`, `date_lancement`, `date_cloture`) VALUES
(5, 1, 0, 3, 0, '2025-03-24 11:10:33', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(6, 1, 0, 4, 1, '2025-03-24 11:14:08', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(7, 4, 0, 1, 1, '2025-03-24 11:15:04', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(8, 2, 0, 3, 1, '2025-03-24 11:15:58', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(9, 3, 0, 2, 1, '2025-03-24 11:16:53', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(10, 1, 0, 3, 1, '2025-03-24 11:21:14', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(11, 1, 0, 4, 1, '2025-03-24 13:14:45', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(12, 1, 0, 4, 1, '2025-03-24 13:17:34', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(13, 1, 0, 4, 1, '2025-03-24 13:17:47', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(14, 1, 0, 1, 0, '2025-03-24 13:18:42', '2025-04-23 16:30:12', '0000-00-00 00:00:00'),
(15, 1, 0, 1, 0, '2025-03-24 13:18:46', '2025-04-23 16:30:12', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `groupement`
--

CREATE TABLE `groupement` (
  `id` int(11) NOT NULL,
  `numero_groupement` varchar(50) DEFAULT NULL,
  `nom_groupement` varchar(100) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date_admission` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `num_piece_identite` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `source_connaissance` varchar(100) DEFAULT NULL,
  `personne_contact` varchar(100) DEFAULT NULL,
  `relation_contact` varchar(100) DEFAULT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `effectif` int(11) DEFAULT NULL,
  `activites` text DEFAULT NULL,
  `besoins` text DEFAULT NULL,
  `campagne_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `guichet_id` int(11) DEFAULT NULL,
  `a_beneficie_credit` bit(1) DEFAULT NULL,
  `id_prospect` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `guichets`
--

CREATE TABLE `guichets` (
  `id` int(11) NOT NULL,
  `agence_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `statut` enum('actif','inactif') DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `interaction`
--

CREATE TABLE `interaction` (
  `id` int(11) NOT NULL,
  `canal_interaction` varchar(100) DEFAULT NULL,
  `date_interaction` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `id_membre` int(11) DEFAULT NULL,
  `campagne_id` int(11) NOT NULL,
  `id_prospect` int(11) DEFAULT NULL,
  `type_cible` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `interaction`
--

INSERT INTO `interaction` (`id`, `canal_interaction`, `date_interaction`, `description`, `id_membre`, `campagne_id`, `id_prospect`, `type_cible`) VALUES
(5, 'SMS', '2025-03-11', 'Réussie', 3, 0, NULL, 'membres'),
(6, 'EMAIL', '2025-03-11', 'En attente', NULL, 0, 2, 'prospects'),
(7, 'SMS', '2025-04-11', 'Réussi', NULL, 0, 2, 'prospects'),
(8, 'SMS', '0003-04-20', 'reussi', NULL, 0, 3, 'prospects');

-- --------------------------------------------------------

--
-- Structure de la table `interactions`
--

CREATE TABLE `interactions` (
  `id` int(11) NOT NULL,
  `campagne_id` int(11) NOT NULL,
  `cible_type` varchar(20) NOT NULL COMMENT 'prospect, membre_individuel, membre_groupe, dirigeant_entreprise',
  `cible_id` int(11) NOT NULL COMMENT 'ID selon la table correspondante',
  `canal` varchar(20) NOT NULL COMMENT 'appel, email, visite, reunion, sms, chat, courrier, reseaux_sociaux',
  `utilisateur_id` int(11) NOT NULL,
  `date_interaction` datetime NOT NULL,
  `duree` int(11) DEFAULT NULL COMMENT 'Durée en minutes',
  `statut` varchar(20) NOT NULL COMMENT 'planifié, réalisé, annulé, reporté',
  `notes` text DEFAULT NULL,
  `satisfaction` varchar(1) DEFAULT NULL COMMENT '1-5 (Niveau de satisfaction)',
  `pieces_jointes` varchar(255) DEFAULT NULL COMMENT 'Chemins des fichiers joints',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `statut` enum('prospect','membre') DEFAULT 'prospect',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`id`, `nom`, `email`, `telephone`, `statut`, `date_inscription`) VALUES
(1, 'Mbaye', 'mbaye@gmail.com', '778972379', 'membre', '2025-02-20 11:01:17');

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

CREATE TABLE `membres` (
  `id` int(11) NOT NULL,
  `numero_membre` varchar(50) DEFAULT NULL,
  `statut` enum('Actif','Inactif') NOT NULL,
  `date_admission` date DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `nom_entreprise` varchar(255) DEFAULT NULL,
  `effectif` int(11) DEFAULT NULL,
  `classification` varchar(50) DEFAULT NULL,
  `fonction` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text DEFAULT NULL,
  `activites` text DEFAULT NULL,
  `besoins` text DEFAULT NULL,
  `personne_contact` varchar(255) DEFAULT NULL,
  `relation_contact` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci DEFAULT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `nom` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `Prenom` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `Region` varchar(50) NOT NULL,
  `campagne_id` int(10) UNSIGNED DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `guichet_id` int(11) DEFAULT NULL,
  `a_beneficie_credit` tinyint(1) NOT NULL DEFAULT 0,
  `numero_piece` varchar(50) NOT NULL,
  `id_prospect` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membres`
--

INSERT INTO `membres` (`id`, `numero_membre`, `statut`, `date_admission`, `type`, `nom_entreprise`, `effectif`, `classification`, `fonction`, `telephone`, `email`, `adresse`, `activites`, `besoins`, `personne_contact`, `relation_contact`, `telephone_contact`, `commentaires`, `nom`, `Prenom`, `Region`, `campagne_id`, `caisse_id`, `guichet_id`, `a_beneficie_credit`, `numero_piece`, `id_prospect`) VALUES
(1, '200100', 'Actif', '2024-05-11', 'Particulier', 'N/A', 0, 'Autre', 'Commerçante', '778972345', 'DA@gmail.com', 'HLM', 'commerce', 'Credit', 'Amina Ndiaye', 'Amis', '775112233', 'Membre fidéle', 'Diop', 'Assane', 'Dakar', 5, 2, NULL, 1, '2750200003870', 5),
(2, '200101', 'Inactif', '2023-04-02', 'Association', 'N/A', 0, 'PME', 'Enseignant', '776549567', 'Sene@gmail.com', 'Grand Dakar', 'Sport', 'épargne', 'Thierno niass', 'Amis', '775112245', 'Membre infidéle', 'Fall', 'Cheikh', 'Dakar', 4, 1, NULL, 1, '2750200003871', 3),
(3, '200102', 'Actif', '2023-08-12', 'Particulier', 'N/A', 0, 'Autre', 'Commerçante', '778972345', 'Diop@gmail.com', 'parcelle', 'commerce', 'Crédit', 'Pape diop', 'Famille', '775112233', 'Membre fidéle', 'Diop', 'Astou', 'thies', 3, 4, NULL, 1, '2750200003878', 4),
(4, '200103', 'Inactif', '2024-05-12', 'Entreprise', 'SyCommunity', 25, 'Autre', 'Informaticien', '777085793', 'SY@gmail.com', 'Keur massar', 'Entrepreneriat', 'Crédit', 'Anta faye', 'Professionnel', '775112245', 'Membre fidéle', 'Sy', 'pape', 'Dakar', 1, 3, NULL, 0, '2750200003879', 6),
(8, '200106', 'Actif', '2025-04-07', 'Particulier', 'N/A', 0, 'PME', 'Enseignante', ' 775443322', 'fanta.dupont@gmail.com', 'dixiéme', 'Entrepreneur', 'Crédit', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 'Dupont', 'Fanta', 'thies', 4, 4, NULL, 1, '2750200003876', 6),
(16, '200105', 'Actif', '2025-04-07', 'Entreprise', 'Newmandesign', 120, 'Grande Entreprise', 'Menuisier', '776425174', 'newman@gmail.com', 'Mbour2', 'Entrepreneriat', 'Assurance', 'Cheikh niass', 'Famille', '787085793', 'Membre fidele', 'Niass', 'modou', 'thies', 5, 1, NULL, 0, '2750200003875', 4),
(19, '200104', 'Actif', '2025-04-07', 'Entreprise', 'Newmandesign', 120, 'Grande Entreprise', 'Menuisier', '776425163', 'newman@gmail.com', 'Keur massar', 'Entrepreneriat', 'Crédit', 'Cheikh niass', 'Famille', '787085793', 'Membre fidele', 'Niass', 'modou', 'Dakar', 4, 3, NULL, 0, '2750200003874', NULL),
(21, '200107', 'Inactif', '2025-04-07', 'Particulier', 'N/A', 0, 'Autre', 'Musicien', '781004060', 'ndiaye@gmail.com', 'Bopp', 'commerce', 'Credit', 'Anta faye', 'Famille', '771270340', '      prospect intéressé', 'Salif', 'Ndiaye', 'Kédougou', 1, 1, NULL, 0, '2750200003877', NULL),
(24, '200108', 'Actif', '2025-04-07', 'Particulier', 'N/A', 0, 'Autre', 'DSI', '778972345', 'nmcamara@pamecas.sn', 'hlm kaolack', 'Technologie de l\'information', 'credit', 'Thierno niass', 'Famille', '775112245', 'Prospects intéréssée par l\'offre', 'Camara', 'Ndiogou', 'Kaolack', 2, 2, NULL, 0, '2750200003873', NULL),
(25, '200109', '', '2025-04-07', 'Association', 'Dolél djiguéne yi', 60, 'Autre', 'informaticienne', '764005780', 'Diagne@gmail.com', 'Malika', 'Entreprendre', 'Credit', 'Astou diop', 'Amis', '762034568', ' prospects en voie de conversion            ', 'Diagne', 'Soda', 'Dakar', 0, 1, NULL, 1, '2750200003881', 7),
(27, '200110', 'Actif', '2025-04-08', 'Particulier', 'N/A', 0, 'PME', 'Enseignante', ' 775443322', 'syfatoukine95@gmail.com', 'Keur massar', 'Entrepreneur', 'Crédit', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 'Sy', 'Fatou kine', 'Dakar', 1, 1, NULL, 0, '2750200003872', NULL),
(28, '200111', '', '2025-04-08', 'Particulier', 'N/A', 0, 'Autre', 'DSI', '778972345', 'nmcamara@pamecas.sn', 'hlm kaolack', 'Technologie de l\'information', 'credit', 'Thierno niass', 'Famille', '775112245', 'Prospects intéréssée par l\'offre', 'Camara', 'Ndiogou', 'Kaolack', 2, 2, NULL, 0, '2750200003882', 8),
(29, '200112', '', '2025-04-08', 'Particulier', 'N/A', 0, 'PME', 'Edutiante', '701045677', 'MBAYE@gmail.com', 'Fass', 'Entrepreneriat', 'Credit', 'Anta faye', 'Professionnel', '785015677', '         Prospect intéresé à offre   ', 'Mbaye', 'Astou', 'dakar', 1, 1, NULL, 0, '2750200003883', 9),
(30, '200115', 'Inactif', '2025-04-09', 'Autres', 'N/A', 0, 'Autre', 'Eléve', '776425163', 'seck@gmail.com', 'Fass', 'commerce', 'Array', 'Pape diop', 'Amis', '771270340', '         Prospect     ', 'Seck', 'mmussa', 'Dakar', 0, 2, NULL, 0, '2750200003889', NULL),
(31, 'MEM2024001', 'Actif', '0000-00-00', 'Entreprise', 'ABC SARL', 12, 'PME', 'Directeur', '781234567', 'contact@abc.sn', 'Dakar Plateau', 'Commerce', 'Crédit stock', 'M. Sow', 'DG', '761112233', 'Client sérieux', 'Sow', 'Abdoul', 'Dakar', 0, 0, 0, 0, 'SN12345678', 0),
(32, 'MEM2024002', 'Inactif', '0000-00-00', 'Particulier', '', 0, '', 'Enseignant', '761234567', 'a.diop@mail.com', 'Mermoz', '', 'Prêt perso', '', '', '', 'Ancien membre', 'Diop', 'Aminata', 'Dakar', 0, 0, 0, 0, 'SN87654321', 0);

-- --------------------------------------------------------

--
-- Structure de la table `membre_entreprise`
--

CREATE TABLE `membre_entreprise` (
  `id` int(11) NOT NULL,
  `numero_compt_entreprise` varchar(50) DEFAULT NULL,
  `nom_entreprise` varchar(100) DEFAULT NULL,
  `nom_dirigant` varchar(100) DEFAULT NULL,
  `prenom_dirigant` varchar(100) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date_admission` date DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `numero_piece` varchar(20) DEFAULT NULL,
  `numero_passeport` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `classification` varchar(100) DEFAULT NULL,
  `effectif` int(11) DEFAULT NULL,
  `activites` text DEFAULT NULL,
  `besoins` text DEFAULT NULL,
  `source_connaissance` varchar(150) DEFAULT NULL,
  `personne_contact` varchar(100) DEFAULT NULL,
  `relation_contact` varchar(100) DEFAULT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `campagne_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `guichet_id` int(11) DEFAULT NULL,
  `a_beneficie_credit` bit(20) DEFAULT NULL,
  `id_prospect` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `membre_groupe`
--

CREATE TABLE `membre_groupe` (
  `id` int(11) NOT NULL,
  `id_groupement` int(11) NOT NULL,
  `Numero_membre` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `adress` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `num_piece_identite` varchar(50) DEFAULT NULL,
  `num_passeport` varchar(50) DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `poste_dans_groupement` varchar(100) DEFAULT NULL,
  `date_adhesion` date DEFAULT NULL,
  `commentaire` varchar(50) DEFAULT NULL,
  `campagne_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `guichet_id` int(11) DEFAULT NULL,
  `a_benefice_credit` bit(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `membre_individuel`
--

CREATE TABLE `membre_individuel` (
  `id` int(11) NOT NULL,
  `numero_membre` varchar(50) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date_admission` date DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `fonction` varchar(100) DEFAULT NULL,
  `num_telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `region` varchar(225) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `numero_piece` varchar(50) DEFAULT NULL,
  `numero_passeport` varchar(50) DEFAULT NULL,
  `besoins` text DEFAULT NULL,
  `source_connaissance` varchar(100) DEFAULT NULL,
  `personne_contact` varchar(100) DEFAULT NULL,
  `relation_contact` varchar(100) DEFAULT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `campagne_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `guichet_id` int(11) DEFAULT NULL,
  `a_benefice_credit` bit(1) DEFAULT NULL,
  `id_prospect` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membre_individuel`
--

INSERT INTO `membre_individuel` (`id`, `numero_membre`, `statut`, `date_admission`, `nom`, `prenom`, `fonction`, `num_telephone`, `email`, `region`, `adresse`, `numero_piece`, `numero_passeport`, `besoins`, `source_connaissance`, `personne_contact`, `relation_contact`, `telephone_contact`, `commentaire`, `campagne_id`, `caisse_id`, `guichet_id`, `a_benefice_credit`, `id_prospect`) VALUES
(1, '04202', 'actif', '0000-00-00', 'Fall', 'Fanta', 'Commerçante', '702245768', 'Diouffanta@gmail.com', 'thies', 'Mbour1', '2750200003807', NULL, 'Credit', 'Boucha à oreille', 'Astou ndiaye', 'Amie', '773056721', NULL, 1, 3, 0, b'1', 3),
(0, '200102', 'Actif', '2025-06-15', 'Séne', 'Moussa', 'Menuisier', '773045070', 'Sene@gmail.com', 'Dakar', 'Yeumbeul', '225619900360', 'N/A', 'Credit', 'Bouche à oreille', 'Sonia', 'Amis', '775112237', 'Membre est intéressé par le crédit', 0, 0, 0, b'1', 0);

-- --------------------------------------------------------

--
-- Structure de la table `opportunites`
--

CREATE TABLE `opportunites` (
  `id` int(11) NOT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `type_opportunite` varchar(100) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `date_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `opportunites`
--

INSERT INTO `opportunites` (`id`, `membre_id`, `produit_id`, `utilisateur_id`, `caisse_id`, `type_opportunite`, `statut`, `date_creation`) VALUES
(1, 2, 3, 1, 1, 'Autre', 'En cours', '2025-04-23'),
(2, 8, 4, 1, 3, 'Crédit', 'Validée', '2025-04-23'),
(3, 2, 4, 5, 5, 'Crédit', 'En cours', '2025-04-25');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom_produit` varchar(255) NOT NULL,
  `type_produit` enum('Crédit','Épargne','Assurance','Autre') NOT NULL,
  `description` text DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `taux_interet` decimal(5,2) DEFAULT 0.00,
  `montant` decimal(15,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom_produit`, `type_produit`, `description`, `conditions`, `taux_interet`, `montant`, `created_at`, `updated_at`) VALUES
(1, 'Compte épargne ', 'Épargne', 'Épargne pour les jeunes de 18 à 30 ans', 'de 18 à 30 ans', 2.00, 500000.00, '2025-03-04 23:00:00', '2025-04-24 14:24:46'),
(2, 'Compte Assurance maladie', 'Assurance', 'Asurance pour les jeunes de 18 à 30 ans', 'de 18 de plus', 1.00, 200000.00, '2025-02-01 23:00:00', '2025-04-24 14:52:39'),
(3, 'Compte Assurance décés', 'Assurance', 'Asurance pour les adultes', '40ans de plus', 2.00, 300000.00, '0000-00-00 00:00:00', '2025-04-24 14:53:39'),
(4, 'Compte credit', 'Crédit', 'Crédit pour achat de voiture', 'de 18 à 30 ans', 1.00, 2000000.00, '2025-04-04 22:00:00', '2025-04-24 14:41:45');

-- --------------------------------------------------------

--
-- Structure de la table `prospects`
--

CREATE TABLE `prospects` (
  `id` int(11) NOT NULL,
  `caisse_id` int(11) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `enregistre_par` varchar(255) DEFAULT NULL,
  `agence_concernee` varchar(255) DEFAULT NULL,
  `date_enregistrement` date DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `nom_entreprise` varchar(255) DEFAULT NULL,
  `effectif` int(11) DEFAULT NULL,
  `classification` varchar(50) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `Region` varchar(50) NOT NULL,
  `fonction` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `telephone_whatsapp` tinyint(1) DEFAULT 0,
  `email` varchar(255) NOT NULL,
  `adresse` text DEFAULT NULL,
  `activites` text DEFAULT NULL,
  `besoins` text DEFAULT NULL,
  `source_connaissance` varchar(20) NOT NULL,
  `numero_membre` varchar(50) DEFAULT NULL,
  `personne_contact` varchar(255) DEFAULT NULL,
  `relation_contact` varchar(20) NOT NULL,
  `telephone_contact` varchar(20) DEFAULT NULL,
  `commentaires` text DEFAULT NULL,
  `a_beneficie_credit` tinyint(1) NOT NULL DEFAULT 0,
  `campagne_id` int(10) UNSIGNED NOT NULL,
  `numero_piece` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prospects`
--

INSERT INTO `prospects` (`id`, `caisse_id`, `statut`, `enregistre_par`, `agence_concernee`, `date_enregistrement`, `type`, `nom_entreprise`, `effectif`, `classification`, `nom`, `prenom`, `Region`, `fonction`, `telephone`, `telephone_whatsapp`, `email`, `adresse`, `activites`, `besoins`, `source_connaissance`, `numero_membre`, `personne_contact`, `relation_contact`, `telephone_contact`, `commentaires`, `a_beneficie_credit`, `campagne_id`, `numero_piece`) VALUES
(1, 1, 'prospect', 'Sarah Ndiaye', 'bourguiba', '2023-05-11', 'Particulier', 'N/A', 0, 'PME', 'Sy', 'Fatou kine', 'Dakar', 'Enseignante', ' 775443322', 0, 'syfatoukine95@gmail.com', 'Keur massar', 'Entrepreneur', 'Crédit', 'Autre', '200110', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 0, 1, '2750200003872'),
(2, 2, 'prospect', 'Moussa Ndiaye', 'Pikine', '2024-06-02', 'Particulier', 'N/A', 0, 'Autre', 'Camara', 'Ndiogou', 'Kaolack', 'DSI', '778972345', 1, 'mndoyedsi@pamecas.sn', 'hlm kaolack', 'Technologie de l\'information', 'credit', 'Autre', '200108', 'Thierno niass', 'Famille', '775112245', 'Prospects intéréssée par l\'offre', 0, 2, '2750200003873'),
(3, 3, 'prospect', 'mme diop', 'Point de service 1', '2025-03-12', 'Entreprise', 'Newmandesign', 120, 'Grande Entreprise', 'Niass', 'modou', 'Dakar', 'Menuisier', '776425163', 0, 'newman@gmail.com', 'Keur massar', 'Entrepreneriat', 'Crédit', 'Instagram', '200104', 'Cheikh niass', 'Famille', '787085793', 'Membre fidele', 0, 4, '2750200003874'),
(4, 1, 'prospect', 'mme diop', 'Point de service 1', '2025-03-12', 'Entreprise', 'Newmandesign', 120, 'Grande Entreprise', 'Niass', 'modou', 'thies', 'Menuisier', '776425174', 0, 'newman@gmail.com', 'Mbour2', 'Entrepreneriat', 'Assurance', 'Instagram', '200105', 'Cheikh niass', 'Famille', '787085793', 'Membre fidele', 0, 5, '2750200003875'),
(5, 4, 'prospect', 'Sarah Ndiaye', 'bourguiba', '2023-05-11', 'Particulier', 'N/A', 0, 'PME', 'Dupont', 'Fanta', 'thies', 'Enseignante', ' 775443322', 0, 'fanta.dupont@gmail.com', 'dixiéme', 'Entrepreneur', 'Crédit', 'Autre', '200106', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 1, 4, '2750200003876'),
(6, 1, 'prospect', 'mme ndiaye', 'Agence principale', '2025-04-01', 'Particulier', 'N/A', 0, 'Autre', 'Salif', 'Ndiaye', 'Kédougou', 'Musicien', '781004060', 0, 'ndiaye@gmail.com', 'Bopp', 'commerce', 'Credit', 'Bouche à oreille', '200107', 'Anta faye', 'Famille', '771270340', '             prospect intéressé', 0, 1, '2750200003877'),
(7, 2, 'migré', 'Mme Marone', 'Agence principale', '2025-03-11', 'Association', 'Dolél djiguéne yi', 60, 'Autre', 'Diagne', 'Soda', 'Dakar', 'informaticienne', '764005780', 0, 'Diagne@gmail.com', 'Malika', 'Entreprendre', 'Credit', 'Bouche à oreille', '200109', 'Astou diop', 'Amis', '762034568', ' prospects en voie de conversion            ', 0, 1, '2750200003881'),
(8, 2, 'migré', 'Moussa Ndiaye', 'Pikine', '2024-06-02', 'Particulier', 'N/A', 0, 'Autre', 'Camara', 'Ndiogou', 'Kaolack', 'DSI', '778972345', 1, 'nmcamara@pamecas.sn', 'hlm kaolack', 'Technologie de l\'information', 'credit', 'Autre', '200111', 'Thierno niass', 'Famille', '775112245', 'Prospects intéréssée par l\'offre', 0, 2, '2750200003882'),
(9, 1, 'migré', 'Mr faye', 'Point de service 1', '0025-04-11', 'Particulier', 'N/A', 0, 'PME', 'Mbaye', 'Astou', 'dakar', 'Edutiante', '701045677', 0, 'MBAYE@gmail.com', 'Fass', 'Entrepreneriat', 'Credit', 'Agence', '200112', 'Anta faye', 'Professionnel', '785015677', '         Prospect intéresé à offre   ', 0, 1, '2750200003883'),
(13, 1, 'prospect', 'Sarah Ndiaye', 'bourguiba', '2023-05-11', 'Particulier', '', 0, 'PME', 'Sy', 'Fatou kine', 'Dakar', 'Enseignante', '775443322', 127, 'syfatoukine95@gmail.com', 'Keur massar', 'Entrepreneur', 'Crédit', 'Autre', '200110', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 0, 1, '2750200003872'),
(14, 1, 'prospect', 'Sarah Ndiaye', 'bourguiba', '2023-05-11', 'Particulier', '', 0, 'PME', 'Sy', 'Fatou kine', 'Dakar', 'Enseignante', '775443322', 127, 'aba@pamecas.sn', 'Keur massar', 'Entrepreneur', 'Crédit', 'Autre', '200110', 'Amina Ndiaye', 'Amis', '775112233', 'Cliente intéressée par nos services.', 0, 1, '2750200003872'),
(15, 2, 'prospect', 'Mr sarr', 'Plateu', '0000-00-00', 'Particulier', '', 0, 'PME', 'Faye', 'Cheikh', 'Thies', 'Etudiant', '775443322', 127, 'Cisse@gmail.com', 'dakar plateu', 'Entrepreneur', 'Crédit', 'Autre', '', 'Bocar diop ', 'Amis', '775112233', 'Cliente intéressée par nos services.', 0, 1, '2750200003872'),
(16, 2, 'prospect', 'Mr sarr', 'Plateu', '0000-00-00', 'Particulier', '', 0, 'PME', 'Faye', 'Cheikh', 'Thies', 'Etudiant', '775443322', 127, 'Cisse@gmail.com', 'dakar plateu', 'Entrepreneur', 'Crédit', 'Autre', '', 'Bocar diop ', 'Amis', '775112233', 'Cliente intéressée par nos services.', 0, 1, '2750200003872'),
(17, NULL, 'Prospect', 'Mme Diouf', 'Point de service 1', '2025-06-04', 'Particulier', 'N/A', 0, 'PME', ' Diagne', 'Assane', 'Dakar', 'Informaticien', 'Array', 0, 'DiagneA@gmail.com', 'Yeumbeul', 'Entreprendre', 'Array', 'Bouche à oreille', 'N/A', 'Amdy diop', 'Professionnel', '775112235', '             \r\nProspect en besoins', 0, 0, '225619900345'),
(18, 0, 'Prospect', 'bourguiba', '2023-05-11', '2025-06-05', 'N/A', 'N/A', 0, 'PME', 'Sy', 'Baba', 'Dakar', 'Commercant', '775443322', 0, 'Diouf@gmail.com', 'Keur massar', 'Entreprendre', 'Credit', 'Bouche à oreille', 'N/A', 'Amis', 'Amis', '701204567', 'Prospect interessé', 1, 2, '4294967295');

-- --------------------------------------------------------

--
-- Structure de la table `rapport_campagne`
--

CREATE TABLE `rapport_campagne` (
  `id` int(11) NOT NULL,
  `campagne_marketing_id` int(11) NOT NULL,
  `date_rapport` date NOT NULL,
  `nombre_prospects` int(11) NOT NULL,
  `nombre_conversions` int(11) NOT NULL,
  `cout_total` decimal(10,2) DEFAULT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `caisse_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `role`, `caisse_id`) VALUES
(1, 'Astou diop', 'astoudiop@gmail.com', '$2y$10$TitZNZ/NOjRFljqgcZGbP.LCuF9KppI7Nvfr0XRxaxGT8QiBCkVAe', 'commercial', 4),
(2, 'fatou kine sy', 'syfatoukine95@gmail.com', '$2y$10$0Zeo/lidDLEsOj5AFfzbquDbQ0BbcEBJN5IrrwuSrfIzRB6/ZGuMS', 'admin', 1),
(3, 'Malick camara', 'Mc@gmail.com', '$2y$10$ziCreUlKGnZk4QUl7vXqhuNO1yGajUHUXoWQqlJrzUX1EbWY6/WNm', 'admin', 3),
(4, 'Mbacké niang', 'Mn@gmail.com', '$2y$10$fP.kpOMrzJRt9xpEZUTwq.Wkp/qAH5HmIRbdRL4jANaHNOa/.hRj6', 'directeur', 2),
(5, 'Soda diagne', 'Sd@gmail.com', '$2y$10$lrt.khSlmltYHAon7dTHqO/L5oHFSpP4Jpxq2diXazusa0Vm0RIWK', 'animatrice', 4),
(6, 'Babacar diop', 'BD@gmail.com', '$2y$10$iR.6BEe2/TnyeEjtzbFw5.Idh8dY5OD7i0rk75oAAepI6YowWmOaO', 'admin', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `membre_id` int(11) DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `date_vente` date NOT NULL,
  `debut` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `produit_id`, `membre_id`, `utilisateur_id`, `quantite`, `date_vente`, `debut`) VALUES
(1, 4, 1, 1, 1, '2025-03-10', NULL),
(3, 2, 21, 1, 1, '2025-04-23', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `acces_caisses`
--
ALTER TABLE `acces_caisses`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `agences`
--
ALTER TABLE `agences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_agence` (`code_agence`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `caisses`
--
ALTER TABLE `caisses`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `campagnes_marketing`
--
ALTER TABLE `campagnes_marketing`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `campagne_participants`
--
ALTER TABLE `campagne_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campagne_id` (`campagne_id`);

--
-- Index pour la table `guichets`
--
ALTER TABLE `guichets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agence_id` (`agence_id`);

--
-- Index pour la table `interaction`
--
ALTER TABLE `interaction`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `interactions`
--
ALTER TABLE `interactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `membres`
--
ALTER TABLE `membres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_membre` (`numero_membre`);

--
-- Index pour la table `opportunites`
--
ALTER TABLE `opportunites`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `prospects`
--
ALTER TABLE `prospects`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rapport_campagne`
--
ALTER TABLE `rapport_campagne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campagne_marketing_id` (`campagne_marketing_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `acces_caisses`
--
ALTER TABLE `acces_caisses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `agences`
--
ALTER TABLE `agences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `caisses`
--
ALTER TABLE `caisses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `campagnes_marketing`
--
ALTER TABLE `campagnes_marketing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `campagne_participants`
--
ALTER TABLE `campagne_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `guichets`
--
ALTER TABLE `guichets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `interaction`
--
ALTER TABLE `interaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `interactions`
--
ALTER TABLE `interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `membres`
--
ALTER TABLE `membres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `opportunites`
--
ALTER TABLE `opportunites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `prospects`
--
ALTER TABLE `prospects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `rapport_campagne`
--
ALTER TABLE `rapport_campagne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `campagne_participants`
--
ALTER TABLE `campagne_participants`
  ADD CONSTRAINT `campagne_participants_ibfk_1` FOREIGN KEY (`campagne_id`) REFERENCES `campagnes_marketing` (`id`);

--
-- Contraintes pour la table `guichets`
--
ALTER TABLE `guichets`
  ADD CONSTRAINT `guichets_ibfk_1` FOREIGN KEY (`agence_id`) REFERENCES `agences` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `rapport_campagne`
--
ALTER TABLE `rapport_campagne`
  ADD CONSTRAINT `rapport_campagne_ibfk_1` FOREIGN KEY (`campagne_marketing_id`) REFERENCES `campagnes_marketing` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
