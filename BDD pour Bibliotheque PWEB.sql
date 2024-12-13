-- Création des tables
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);
CREATE TABLE Etudiant (
    MatriculeEtudiant INT PRIMARY KEY,
    Nom VARCHAR(50),
    Prenom VARCHAR(50),
    DateNaissance DATE,
    EtatEtudiant VARCHAR(20) -- Exemples : 'Inscrit', 'Non inscrit', 'Diplômé'
);

CREATE TABLE Edition (
    NumEdition INT PRIMARY KEY,
    MaisonEdition VARCHAR(50)
);

CREATE TABLE Categorie (
    NumCategorie INT PRIMARY KEY,
    NomCategorie VARCHAR(50)
);

CREATE TABLE Ouvrage (
    CodeOuvrage INT PRIMARY KEY,
    Titre VARCHAR(100),
    DateAcquisition DATE,
    DateEdition DATE,
    NumEdition INT,
    NumCategorie INT,
    FOREIGN KEY (NumEdition) REFERENCES Edition(NumEdition),
    FOREIGN KEY (NumCategorie) REFERENCES Categorie(NumCategorie)
);

CREATE TABLE Auteur (
    IDAuteur INT PRIMARY KEY,
    NomAuteur VARCHAR(50)
);

CREATE TABLE Exemplaire (
    IDExemplaire INT PRIMARY KEY,
    EtatExemplaire VARCHAR(50),
    IDLocalisation INT,
    CodeOuvrage INT,
    FOREIGN KEY (IDLocalisation) REFERENCES Localisation(IDLocalisation),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage)
);

CREATE TABLE Localisation (
    IDLocalisation INT PRIMARY KEY,
    LibelleLocalisation VARCHAR(100)
);

CREATE TABLE Emprunter (
    MatriculeEtudiant INT,
    IDExemplaire INT,
    DateEmprunt DATE,
    DateRestitutionPrevue DATE,
    DateRestitutionReelle DATE,
    PRIMARY KEY (MatriculeEtudiant, IDExemplaire, DateEmprunt),
    FOREIGN KEY (MatriculeEtudiant) REFERENCES Etudiant(MatriculeEtudiant),
    FOREIGN KEY (IDExemplaire) REFERENCES Exemplaire(IDExemplaire),
    FOREIGN KEY (DateEmprunt) REFERENCES Date(DateEmprunt)

);

   CREATE TABLE Date (

     DateEmprunt  DATE PRIMARY KEY,

);


CREATE TABLE Ecrit (
    CodeOuvrage INT,
    IDAuteur INT,
    PRIMARY KEY (CodeOuvrage, IDAuteur),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage),
    FOREIGN KEY (IDAuteur) REFERENCES Auteur(IDAuteur)
);

-- Insertion des données
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
VALUES ('ferroukhi', 'wassila', 'wassila.ferroukhi@esst-sup.com', 'frwass2019', 'Admin');
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) 
VALUES ('aitmessaoud', 'tewfik', 'tewfik.aitmessaoud@esst-sup.com', 'aitmess2019', 'Utilisateur');
INSERT INTO Auteur (IDAuteur, NomAuteur)
VALUES
(1, 'H.Djelouah'),
(2, 'Ahmed Bourdache');

INSERT INTO Edition (NumEdition, MaisonEdition)
VALUES
(1, '2021'),
(2, '2020');

INSERT INTO Categorie (NumCategorie, NomCategorie)
VALUES
(1, 'Physique');

INSERT INTO Ouvrage (CodeOuvrage, Titre, DateAcquisition, DateEdition, NumEdition, NumCategorie)
VALUES
(1, 'vibrations & ondes cours et exercices corrigés', '2023-01-01', '2021-01-01', 1, 1),
(2, 'Travaux pratiques de vibrations et ondes', '2023-01-01', '2020-01-01', 2, 1);

INSERT INTO Localisation (IDLocalisation, LibelleLocalisation)
VALUES
(1, 'Bibliothèque Centrale');

INSERT INTO Exemplaire (IDExemplaire, EtatExemplaire, IDLocalisation, CodeOuvrage)
VALUES
(1, 'Neuf', 1, 1),
(2, 'Usagé', 1, 2);

INSERT INTO Etudiant (MatriculeEtudiant, Nom, Prenom, DateNaissance, EtatEtudiant)
VALUES
(1, 'Dupont', 'Jean', '2000-05-10', 'Inscrit'),
(2, 'Martin', 'Claire', '1999-08-15', 'Diplômé');

INSERT INTO Emprunter (MatriculeEtudiant, IDExemplaire, DateEmprunt, DateRestitutionPrevue, DateRestitutionReelle)
VALUES
(1, 1, '2024-01-10', '2024-02-10', NULL);

-- Mises à jour
UPDATE Exemplaire
SET EtatExemplaire = 'Disponible'
WHERE IDExemplaire = 1;

UPDATE Etudiant
SET EtatEtudiant = 'Diplômé'
WHERE MatriculeEtudiant = 1;

UPDATE Emprunter
SET DateRestitutionReelle = '2024-12-05'
WHERE MatriculeEtudiant = 1 AND IDExemplaire = 1;

-- Suppressions
DELETE FROM Emprunter
WHERE MatriculeEtudiant = 1 AND IDExemplaire = 1 AND DateEmprunt = '2024-01-10';

DELETE FROM Exemplaire
WHERE IDExemplaire = 2;

DELETE FROM Etudiant
WHERE MatriculeEtudiant = 2 AND EtatEtudiant = 'Non inscrit';

-- Procédures et déclencheurs
CREATE TRIGGER check_etudiant_inscrit
BEFORE INSERT ON Emprunter
FOR EACH ROW
BEGIN
    IF (SELECT EtatEtudiant FROM Etudiant WHERE MatriculeEtudiant = NEW.MatriculeEtudiant) != 'Inscrit' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'L\'étudiant doit être inscrit pour emprunter.';
    END IF;
END;

CREATE PROCEDURE inventaire_ouvrages()
BEGIN
    SELECT * FROM Ouvrage;
END;

CREATE PROCEDURE RO2_ouvrages_non_restitues()
BEGIN
    SELECT * FROM Emprunter
    WHERE DateRestitutionReelle IS NULL AND NOW() > DateRestitutionPrevue;
END;
