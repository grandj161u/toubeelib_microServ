-- Adminer 4.8.1 PostgreSQL 16.4 (Debian 16.4-1.pgdg120+1) dump

DROP TABLE IF EXISTS "rdv";
CREATE TABLE "public"."rdv" (
    "id" text NOT NULL,
    "id_praticien" character varying(5) NOT NULL,
    "id_patient" character varying(5) NOT NULL,
    "id_spe" character varying(5) NOT NULL,
    "date_rdv" timestamp NOT NULL,
    "statut" character varying(20) NOT NULL,
    "type_rdv" character varying(20) NOT NULL,
    CONSTRAINT "rdv_id" PRIMARY KEY ("id")
) WITH (oids = false);

INSERT INTO "rdv" ("id", "id_praticien", "id_patient", "id_spe", "date_rdv", "statut", "type_rdv") VALUES
('r2',	'p1',	'pa1',	'A',	'2024-09-02 10:00:00',	'a payer',	'5'),
('r3',	'p2',	'pa1',	'A',	'2024-09-02 09:30:00',	'en attente',	'4'),
('r4',	'p2',	'pa2',	'C',	'2024-09-02 10:30:00',	'confirmer',	'3'),
('925da57b-de47-4af3-8014-92e5a31cfd12',	'p1',	'pa2',	'A',	'2024-11-01 10:00:00',	'confirmer',	'1'),
('r1',	'p1',	'pa8',	'A',	'2024-09-02 09:00:00',	'annule',	'1');