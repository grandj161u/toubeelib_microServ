-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "patient";
CREATE TABLE "public"."patient" (
    "id" character varying(5) NOT NULL,
    "nom" character varying(30) NOT NULL,
    "prenom" character varying(30) NOT NULL,
    "ville" character varying(30) NOT NULL,
    CONSTRAINT "patient_id" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "patient" ("id", "nom", "prenom", "ville") VALUES
('pa1',	'Durand',	'Paul',	'nancy'),
('pa2',	'Petitjean',	'Olivier',	'vandeuve');