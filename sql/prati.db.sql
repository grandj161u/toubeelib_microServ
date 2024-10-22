-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "praticien";
CREATE TABLE "public"."praticien" (
    "id" text NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "tel" character(10) NOT NULL,
    "adresse" character varying(30) NOT NULL,
    "specialite_id" character varying(3) NOT NULL,
    CONSTRAINT "praticien_id" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "praticien" ("id", "nom", "prenom", "tel", "adresse", "specialite_id") VALUES
('p1',	'Dupont',	'Jean',	'0123456789',	'nancy',	'A'),
('p2',	'Durand',	'Pierre',	'0123456789',	'vandeuve',	'B'),
('p3',	'Martin',	'Marie',	'0123456789',	'3lassou',	'C'),
('p4',	'Boulanger',	'Paul',	'0123456789',	'mazeville',	'D'),
('p5',	'Pere',	'Tom',	'0123456789',	'villers',	'E');

DROP TABLE IF EXISTS "specialite";
CREATE TABLE "public"."specialite" (
    "id" character varying(3) NOT NULL,
    "label" character varying(30) NOT NULL,
    "description" character varying(75) NOT NULL,
    CONSTRAINT "specialite_pkey" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "specialite" ("id", "label", "description") VALUES
('A',	'Dentiste',	'Spécialiste des dents'),
('B',	'Ophtalmologue',	'Spécialiste des yeux'),
('C',	'Généraliste',	'Médecin généraliste'),
('D',	'Pédiatre',	'Médecin pour enfants'),
('E',	'Médecin du sport',	'Maladies et traumatismes liés à la pratique sportive');

ALTER TABLE ONLY "public"."praticien" ADD CONSTRAINT "praticien_specialite_id_fkey" FOREIGN KEY (specialite_id) REFERENCES specialite(id) NOT DEFERRABLE;
