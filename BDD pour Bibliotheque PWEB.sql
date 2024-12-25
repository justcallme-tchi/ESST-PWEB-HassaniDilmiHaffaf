-- Création de la base de données
DROP DATABASE IF EXISTS projetweb;
CREATE DATABASE projetweb;
USE projetweb;

-- Création des tables
CREATE TABLE Administrateur (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE Etudiant (
    MatriculeEtudiant INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    Nom VARCHAR(50) NOT NULL,
    Prenom VARCHAR(50) NOT NULL,
    DateNaissance DATE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    EtatEtudiant VARCHAR(20) NOT NULL -- Exemples : 'Inscrit', 'Non inscrit', 'Diplômé'
);

CREATE TABLE Edition (
    NumEdition INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    AnneeEdition INT
);

CREATE TABLE Categorie (
    NumCategorie INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    NomCategorie VARCHAR(50)
);

CREATE TABLE Ouvrage (
    CodeOuvrage INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    Titre VARCHAR(100),
    DateAcquisition DATE,
    DateEdition DATE,
    NumEdition INT,
    NumCategorie INT,
    FOREIGN KEY (NumEdition) REFERENCES Edition(NumEdition),
    FOREIGN KEY (NumCategorie) REFERENCES Categorie(NumCategorie)
);

CREATE TABLE Auteur (
    IDAuteur INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    NomAuteur VARCHAR(50)
);

CREATE TABLE localisation (
    IDLocalisation INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    NomLocalisation VARCHAR(100)
);

CREATE TABLE Exemplaire (
    IDExemplaire INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    EtatExemplaire VARCHAR(50),
    IDLocalisation INT,
    CodeOuvrage INT,
    FOREIGN KEY (IDLocalisation) REFERENCES localisation(IDLocalisation),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage)
);

CREATE TABLE Date (
    DateEmprunt DATE PRIMARY KEY
);

CREATE TABLE Emprunter (
    MatriculeEtudiant INT,
    IDExemplaire INT,
    EtatExemplaire VARCHAR(50),
    DateEmprunt DATE,
    DateRestitutionPrevue DATE,
    DateRestitutionReelle DATE,
    PRIMARY KEY (MatriculeEtudiant, IDExemplaire, DateEmprunt),
    FOREIGN KEY (MatriculeEtudiant) REFERENCES Etudiant(MatriculeEtudiant),
    FOREIGN KEY (IDExemplaire) REFERENCES Exemplaire(IDExemplaire),
    FOREIGN KEY (DateEmprunt) REFERENCES Date(DateEmprunt)
);

CREATE TABLE Ecrit (
    CodeOuvrage INT,
    IDAuteur INT,
    PRIMARY KEY (CodeOuvrage, IDAuteur),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage),
    FOREIGN KEY (IDAuteur) REFERENCES Auteur(IDAuteur)
);

-- Insertion des données
INSERT INTO Administrateur (nom, prenom, email, mot_de_passe)  
VALUES ('Ferroukhi', 'Wassila', 'wassila.ferroukhi@esst-sup.com', '$2y$10$ttz.8.uNJyu095TS5nfbreZlRxcN23eepq/akGfwMNJpRDovfoeea'); -- frwass2019

INSERT INTO Etudiant (Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant)  
VALUES ('Chibane', 'El Hadi', '1998-08-15', 'chibane.elhadi@esst-sup.com', '$2y$10$nRX89Fd3.14zliCDQ6UVpev3Ug7jAuw6SvAyiG7arAqex/FgH3yj2', 'Non inscrit'); -- elhad2019 

INSERT INTO Etudiant (Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant)  
VALUES ('Bouchaar', 'Mourad', '2003-05-10', 'bouchaar.mourad@esst-sup.com', '$2y$10$rLCSDiyjp5/a62YH42pbCerDE64xncfBIEwweHP7UCYcfDys5hdYK', 'Inscrit'); -- boumou2019

INSERT INTO Auteur (NomAuteur)
VALUES
('H.Djelouah'),
('Ahmed Bourdache');

INSERT INTO Edition (AnneeEdition) VALUES 
(2021),
(2020),
(2014),
(2002);

INSERT INTO Categorie (NomCategorie)
VALUES
('Physique'),
('Math');

INSERT INTO Ouvrage (Titre, DateAcquisition, DateEdition, NumEdition, NumCategorie)
VALUES
('vibrations & ondes cours et exercices corrigés', '2023-01-01', '2021-01-01', 1, 1),
('Travaux pratiques de vibrations et ondes', '2023-01-01', '2020-01-01', 2, 1),
('chimie 1 structure de la matière', '2023-06-15', '2014-08-11', 3, 1),
('energies renouvelables', '2019-12-15', '2002-03-14', 4, 1);

INSERT INTO ecrit (CodeOuvrage, IDAuteur)
VALUES
('1','1'),
('2','2'),
('3','1'),
('3','2'),
('4','2');

INSERT INTO localisation (NomLocalisation)
VALUES ('2eranger3etage'), ('3ranger5etage');

INSERT INTO Exemplaire (EtatExemplaire, IDLocalisation, CodeOuvrage)
VALUES
('Neuf', 1, 1),
('Usagé', 1, 2),
('En Retard', 1, 3),
('En Retard', 1, 4);

INSERT INTO Date (DateEmprunt) VALUES
('2024-01-10'),
('2024-02-06');

INSERT INTO Emprunter (MatriculeEtudiant, IDExemplaire, EtatExemplaire, DateEmprunt, DateRestitutionPrevue, DateRestitutionReelle)
VALUES
(1, 3, 'En Retard', '2024-01-10', '2024-02-10', NULL),
(2, 4, 'En Retard', '2024-02-06', '2024-03-06', NULL);

-- Mises à jour
UPDATE Exemplaire
SET EtatExemplaire = 'Disponible'
WHERE IDExemplaire = 1;

UPDATE Etudiant
SET EtatEtudiant = 'Diplômé'
WHERE MatriculeEtudiant = 1;

UPDATE Emprunter
SET DateRestitutionReelle = '2024-12-05'
WHERE MatriculeEtudiant = 1 AND IDExemplaire = 3;

UPDATE Emprunter
SET DateRestitutionReelle = '2024-11-11'
WHERE MatriculeEtudiant = 2 AND IDExemplaire = 4;

-- Suppressions
DELETE FROM Emprunter
WHERE MatriculeEtudiant = 1 AND IDExemplaire = 3 AND DateEmprunt = '2024-01-10';

DELETE FROM Exemplaire
WHERE IDExemplaire = 2;

DELETE FROM Etudiant 
WHERE MatriculeEtudiant = 2 AND EtatEtudiant = 'Non inscrit';

-- Procédures et déclencheurs
DELIMITER $$

CREATE TRIGGER check_etudiant_inscrit
BEFORE INSERT ON Emprunter
FOR EACH ROW
BEGIN
    -- Vérifie si l'étudiant n'est pas inscrit
    IF (SELECT EtatEtudiant FROM Etudiant WHERE MatriculeEtudiant = NEW.MatriculeEtudiant) != 'Inscrit' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L’étudiant n’est pas inscrit et ne peut pas emprunter.';
    END IF;
END$$

DELIMITER ;
