DROP TABLE IF EXISTS Panier,Reservation,Compte,Utilisateur,Produits,OrigineProduits,TypeProduits,Entreprise,LocalisationEntreprise,TypeEntreprise,Avis;

CREATE TABLE Avis(
	id_avis int(50) AUTO_INCREMENT NOT NULL,
	note_avis int(50),
	PRIMARY KEY (id_avis)
)DEFAULT CHARSET=utf8;
INSERT INTO Avis VALUES (1,'5');
INSERT INTO Avis VALUES (2,'1');

CREATE TABLE TypeEntreprise(
	id_typeEntreprise int AUTO_INCREMENT NOT NULL,
	libelle_typeEntreprise varchar(255),
	PRIMARY KEY (id_typeEntreprise)
)DEFAULT CHARSET=utf8;
INSERT INTO TypeEntreprise VALUES (1,'Boulangerie');
INSERT INTO TypeEntreprise VALUES (2,'Patisserie');
INSERT INTO TypeEntreprise VALUES (3,'Restaurant');

CREATE TABLE LocalisationEntreprise(
	id_localisation int AUTO_INCREMENT NOT NULL,
	adresse varchar(255),
	latitude float (6,3),
	longitude float (6,3),
	PRIMARY KEY (id_localisation)
)DEFAULT CHARSET=utf8;
INSERT INTO LocalisationEntreprise VALUES(1,'13 rue du beau soleil','140.325','150.205');
INSERT INTO LocalisationEntreprise VALUES(2,'4 rue de charles de gaulle','852.023','741.201');
INSERT INTO LocalisationEntreprise VALUES(3,'8 rue de Patrique','152.028','152.185');

CREATE TABLE Entreprise(
	id_entreprise	int AUTO_INCREMENT NOT NULL,
	libelle_entreprise varchar(255),
	id_localisation int,
	PRIMARY KEY (id_entreprise),
	CONSTRAINT fk_LocalisatioinEntreprise_Entreprise FOREIGN KEY (id_localisation) REFERENCES LocalisationEntreprise(id_localisation)
)DEFAULT CHARSET=utf8;
INSERT INTO Entreprise VALUES (1,'Pasta','1');
INSERT INTO Entreprise VALUES (2,'La Foret des fées','2');
INSERT INTO Entreprise VALUES (3,'Il padre','3');

CREATE TABLE TypeProduits(
	id_typeProduits int AUTO_INCREMENT NOT NULL,
	libelle_typeProduits varchar(255),
	PRIMARY KEY (id_typeProduits)
)DEFAULT CHARSET=utf8;
INSERT INTO TypeProduits VALUES (1,'Gateaux');
INSERT INTO TypeProduits VALUES (2,'Pain');
INSERT INTO TypeProduits VALUES (3,'Pate bolognaise');

CREATE TABLE OrigineProduits(
	id_origineProduits int AUTO_INCREMENT NOT NULL,
	libelle_origineProduits varchar(255),
	PRIMARY KEY (id_origineProduits)
)DEFAULT CHARSET=utf8;
INSERT INTO OrigineProduits VALUES (1,'Italie');
INSERT INTO OrigineProduits VALUES (2,'France');
INSERT INTO OrigineProduits VALUES (3,'Espagne');

CREATE TABLE Produits(
	id_produits int AUTO_INCREMENT NOT NULL,
	libelle_produits varchar(255),
	prix_produits float (4,2),
	quantite_produits int(255),
	heure_debut_vente DateTime,
	heure_fin_vente DateTime,
	id_typeProduits int,
	id_origineProduits int,
	id_compte int,
	PRIMARY KEY (id_produits),
	CONSTRAINT fk_typeProduits_Produits FOREIGN KEY (id_typeProduits) REFERENCES TypeProduits(id_typeProduits),
	CONSTRAINT fk_origineProduits_Produits FOREIGN KEY (id_origineProduits) REFERENCES OrigineProduits(id_origineProduits),
	CONSTRAINT fk_compte_Produits FOREIGN KEY (id_compte) REFERENCES Compte(id_compte)
)DEFAULT CHARSET=utf8;
INSERT INTO Produits VALUES (1,'EL Gringos','16.20','5','2017/12/12 15:00:05','2017/12/12 20:05:40','1','1','2');
INSERT INTO Produits VALUES (2,'Michelle','5.20','2','2017/10/12 14:00:05','2017/10/12 18:05:40','2','2','2');
INSERT INTO Produits VALUES (3,'Cloclo','8.20','1','2017/02/02 14:28:20','2017/02/03 18:00:00','3','3','2');

CREATE TABLE Utilisateur(
	id_utilisateur int AUTO_INCREMENT NOT NULL,
	nom_utilisateur varchar (255),
	prenom_utilisateur varchar(255),
	numero_tel NUMERIC,
	code_postal NUMERIC,
	id_entreprise int,
	id_avis int DEFAULT NULL,
	PRIMARY KEY (id_utilisateur),
	CONSTRAINT fk_Entreprise_Utilisateur FOREIGN KEY (id_entreprise) REFERENCES Entreprise(id_entreprise),
	CONSTRAINT fk_Avis_Utilisateur FOREIGN KEY (id_avis) REFERENCES Avis(id_avis)
)DEFAULT CHARSET=utf8;
INSERT INTO Utilisateur VALUES (1,'ZEC ','Mich','0512345985','68000','1','1');
INSERT INTO Utilisateur VALUES(2,'MUNINGER','Kim','0215859578','68500','2','2');
INSERT INTO Utilisateur VALUES(3,'Bastos','Marc','0752895685','90000','3',null);

CREATE TABLE Compte(
	id_compte int AUTO_INCREMENT NOT NULL,
	username varchar(255),
	password varchar(255),
	motDePasse varchar(255),
	email varchar(255),
	droits varchar(255),
	id_utilisateur int,
	PRIMARY KEY (id_compte),
	CONSTRAINT fk_Utilisateur_Compte FOREIGN KEY (id_utilisateur) REFERENCES Utilisateur (id_utilisateur) ON DELETE CASCADE
)DEFAULT CHARSET=utf8;

INSERT INTO Compte VALUES (1, 'admin', '$2y$13$mJK5hyDNAY9rcDuEBofjJ.h3d7xBwlApfMoknBDO0AvXLr1AaJM02', 'admin', 'admin@gmail.com','ROLE_ADMIN','1');
INSERT INTO Compte VALUES (2, 'vendeur', '$2y$13$/gwC0Iv6ssewrr9JeUDDuOcRTWD.uIEjJpH1HUWPAxe.5EwY98OEO','vendeur', 'vendeur@gmail.com','ROLE_VENDEUR','2');
INSERT INTO Compte VALUES (3, 'client', '$2y$13$bhuMlUWdfc5mAhVumuKUG.etahlJ399DEwuQPhbdXjiCdKIeX2nii', 'client', 'client@gmail.com','ROLE_CLIENT','3');

CREATE TABLE Reservation(
	id_reservation int AUTO_INCREMENT NOT NULL,
	prix_reservation float (4,2),
	heure_fin_vente DateTime,
	id_compte int,
	PRIMARY KEY (id_reservation),
	CONSTRAINT fk_Compte_Reservation FOREIGN KEY (id_compte) REFERENCES Compte(id_compte)
)DEFAULT CHARSET=utf8;
INSERT INTO Reservation VALUES (1,'9.00','2017/10/12 18:05:40','2');
INSERT INTO Reservation VALUES (2,'12.00','2017/02/03 18:00:00','3');

CREATE TABLE Panier(
	id_panier int AUTO_INCREMENT NOT NULL,
	quantite_panier int,
	prix_panier float (6,2),
	heure_fin_vente DateTime,
	id_produits int,
	utilisateur_id int ,
	id_reservation int DEFAULT NULL,

	PRIMARY KEY (id_panier),
	CONSTRAINT fk_Utilisateur_Panier FOREIGN KEY (utilisateur_id) REFERENCES Utilisateur(utilisateur_id),
	CONSTRAINT fk_Produits_Panier FOREIGN KEY (id_produits) REFERENCES Produits(id_produits),
	CONSTRAINT fk_Reservation_Panier FOREIGN KEY (id_reservation) REFERENCES Reservation(id_reservation)
)DEFAULT CHARSET=utf8;
INSERT INTO Panier VALUES (1,'1','9.00','2017/10/12 18:05:40','1','1',null);
INSERT INTO Panier VALUES (2,'2','12.00','2017/02/03 18:00:00','2','2',null);
