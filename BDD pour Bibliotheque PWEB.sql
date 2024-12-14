-- Création des tables.
CREATE TABLE Administrateur (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Clé primaire auto-incrémentée
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE Etudiant (
    MatriculeEtudiant INT PRIMARY KEY, -- Clé primaire
    Nom VARCHAR(50) NOT NULL,
    Prenom VARCHAR(50) NOT NULL,
    DateNaissance DATE NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    EtatEtudiant VARCHAR(20) NOT NULL -- Exemples : 'Inscrit', 'Non inscrit', 'Diplômé'
);

CREATE TABLE Edition (
    NumEdition INT PRIMARY KEY,
    AnnéeEdition  INT
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

CREATE TABLE localisation (
    IDLocalisation INT PRIMARY KEY,
    NomLocalisation VARCHAR(100)
);

CREATE TABLE Exemplaire (
    IDExemplaire INT PRIMARY KEY,
    EtatExemplaire VARCHAR(50),
    IDLocalisation INT,
    CodeOuvrage INT,
    FOREIGN KEY (IDLocalisation) REFERENCES localisation(IDLocalisation),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage)
);

  CREATE TABLE Date (

     DateEmprunt  DATE PRIMARY KEY

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

CREATE TABLE Ecrit (
    CodeOuvrage INT,
    IDAuteur INT,
    PRIMARY KEY (CodeOuvrage, IDAuteur),
    FOREIGN KEY (CodeOuvrage) REFERENCES Ouvrage(CodeOuvrage),
    FOREIGN KEY (IDAuteur) REFERENCES Auteur(IDAuteur)
);

-- Insertion des données

INSERT INTO Administrateur (id, nom, prenom, email, mot_de_passe)  
VALUES (1, 'Ferroukhi', 'Wassila', 'wassila.ferroukhi@esst-sup.com', 'frwass2019');


INSERT INTO Etudiant (MatriculeEtudiant, Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant)  
VALUES (2, 'Chibane', 'El Hadi', '1998-08-15', 'chibane.elhadi@esst-sup.com', 'elhad2019', 'Non inscrit');


INSERT INTO Etudiant (MatriculeEtudiant, Nom, Prenom, DateNaissance, email, mot_de_passe, EtatEtudiant)  
VALUES (1, 'Bouchaar', 'Mourad', '2003-05-10', 'bouchaar.mourad@esst-sup.com', 'boumou2019', 'Inscrit');


INSERT INTO Auteur (IDAuteur, NomAuteur)
VALUES
(1, 'H.Djelouah'),
(2, 'Ahmed Bourdache');

INSERT INTO Edition (NumEdition, AnnéeEdition)
VALUES
(1, 2020),
(2, 1970);

INSERT INTO Categorie (NumCategorie, NomCategorie)
VALUES
(1, 'Physique');

INSERT INTO Categorie (NumCategorie, NomCategorie)
VALUES
(2, 'Math');

INSERT INTO Ouvrage (CodeOuvrage, Titre, DateAcquisition, DateEdition, NumEdition, NumCategorie)
VALUES
(1, 'vibrations & ondes cours et exercices corrigés', '2023-01-01', '2021-01-01', 1, 1),
(2, 'Travaux pratiques de vibrations et ondes', '2023-01-01', '2020-01-01', 2, 1);

INSERT INTO localisation (IDLocalisation, NomLocalisation)
VALUES (1, '2eranger3etage');

INSERT INTO localisation (IDLocalisation, NomLocalisation)
VALUES (2, '3ranger5etage');


INSERT INTO Exemplaire (IDExemplaire, EtatExemplaire, IDLocalisation, CodeOuvrage)
VALUES
(1, 'Neuf', 1, 1),
(2, 'Usagé', 1, 2),
(3, 'En Retard ', 1, 3),
(2, 'En Retard ', 1, 4);
INSERT INTO Date (DateEmprunt)
VALUES ('2024-01-10');

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
