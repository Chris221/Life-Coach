-- Database: Life_Coach

-- DROP DATABASE "Life_Coach";

CREATE DATABASE "Life_Coach"
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Table: public."Companies"

-- DROP TABLE public."Companies";

CREATE TABLE public."Companies"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Companies_RowID_seq"'::regclass),
    "AdminID" bigint NOT NULL,
    "Name" character varying(500) COLLATE pg_catalog."default" NOT NULL,
    "Location" character varying(500) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "CompanyID" PRIMARY KEY ("RowID"),
    CONSTRAINT "AdminID" FOREIGN KEY ("AdminID")
        REFERENCES public."Persons" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Companies"
    OWNER to postgres;

-- Table: public."Addresses"

-- DROP TABLE public."Addresses";

CREATE TABLE public."Addresses"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Addresses_RowID_seq"'::regclass),
    "AdressLine1" character varying(200) COLLATE pg_catalog."default" NOT NULL,
    "AdressLine2" character varying(200) COLLATE pg_catalog."default",
    "City" character varying(200) COLLATE pg_catalog."default" NOT NULL,
    "Subdivision" character varying(200) COLLATE pg_catalog."default",
    "Zip" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "Country" character varying(200) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "AddressID" PRIMARY KEY ("RowID")
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Addresses"
    OWNER to postgres;

-- Table: public."Photos"

-- DROP TABLE public."Photos";

CREATE TABLE public."Photos"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Photos_RowID_seq"'::regclass),
    "UploadDate" timestamp without time zone,
    "MIMEType" character varying(250) COLLATE pg_catalog."default",
    "File" bytea,
    CONSTRAINT "PhotoID" PRIMARY KEY ("RowID")
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Photos"
    OWNER to postgres;

-- Table: public."Visits"

-- DROP TABLE public."Visits";

CREATE TABLE public."Visits"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Visits_RowID_seq"'::regclass),
    "Date" timestamp without time zone,
    "Type" character varying COLLATE pg_catalog."default" NOT NULL,
    "Reason" character varying COLLATE pg_catalog."default",
    "Location" bigint,
    CONSTRAINT "VisitID" PRIMARY KEY ("RowID"),
    CONSTRAINT "LocationID" FOREIGN KEY ("Location")
        REFERENCES public."Addresses" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Visits"
    OWNER to postgres;

-- Table: public."Relationship_Types"

-- DROP TABLE public."Relationship_Types";

CREATE TABLE public."Relationship_Types"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Relationship_Types_RowID_seq"'::regclass),
    "Type" character varying(250) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "Relationship_TypeID" PRIMARY KEY ("RowID")
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Relationship_Types"
    OWNER to postgres;

-- Table: public."Relationships"

-- DROP TABLE public."Relationships";

CREATE TABLE public."Relationships"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Relationships_RowID_seq"'::regclass),
    "PersonID1" bigint NOT NULL,
    "Relationship" bigint NOT NULL,
    "PersonID2" bigint NOT NULL,
    CONSTRAINT "RelationshipID" PRIMARY KEY ("RowID"),
    CONSTRAINT "PersonID1" FOREIGN KEY ("PersonID1")
        REFERENCES public."Persons" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PersonID2" FOREIGN KEY ("PersonID2")
        REFERENCES public."Persons" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "Relationship_TypeID" FOREIGN KEY ("Relationship")
        REFERENCES public."Relationship_Types" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Relationships"
    OWNER to postgres;

-- Table: public."Notes"

-- DROP TABLE public."Notes";

CREATE TABLE public."Notes"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Notes_RowID_seq"'::regclass),
    "ClientID" bigint NOT NULL,
    "CoachID" bigint NOT NULL,
    "VisitID" bigint NOT NULL,
    "PhotoID" bigint,
    "Description" text COLLATE pg_catalog."default" NOT NULL,
    "Date_Added" timestamp without time zone,
    CONSTRAINT "NoteID" PRIMARY KEY ("RowID"),
    CONSTRAINT "ClientID" FOREIGN KEY ("ClientID")
        REFERENCES public."Clients" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "CoachID" FOREIGN KEY ("CoachID")
        REFERENCES public."Coaches" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PhotoID" FOREIGN KEY ("PhotoID")
        REFERENCES public."Photos" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "VisitID" FOREIGN KEY ("VisitID")
        REFERENCES public."Visits" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Notes"
    OWNER to postgres;

-- Table: public."Persons"

-- DROP TABLE public."Persons";

CREATE TABLE public."Persons"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"People_RowID_seq"'::regclass),
    "PhotoID" bigint,
    "Prefix" character varying COLLATE pg_catalog."default",
    "First_Name" character varying COLLATE pg_catalog."default" NOT NULL,
    "Last_Name" character varying COLLATE pg_catalog."default" NOT NULL,
    "Suffix" character varying COLLATE pg_catalog."default",
    "Email" character varying COLLATE pg_catalog."default" NOT NULL,
    "Cell" bigint NOT NULL,
    "Home" bigint,
    "Work" bigint,
    "Extension" integer,
    "Date_of_Birth" date,
    "Address" bigint,
    "Middle_Name" character varying(250) COLLATE pg_catalog."default",
    CONSTRAINT "PeopleID" PRIMARY KEY ("RowID"),
    CONSTRAINT "AddressID" FOREIGN KEY ("Address")
        REFERENCES public."Addresses" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PhotoID" FOREIGN KEY ("PhotoID")
        REFERENCES public."Photos" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Persons"
    OWNER to postgres;

-- Table: public."Coaches"

-- DROP TABLE public."Coaches";

CREATE TABLE public."Coaches"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Coaches_RowID_seq"'::regclass),
    "PersonsID" bigint NOT NULL,
    "ClientsID" bigint NOT NULL,
    "CompaniesID" bigint NOT NULL,
    "Superviser" boolean NOT NULL,
    "Password" character varying(150) COLLATE pg_catalog."default" NOT NULL,
    CONSTRAINT "CoachesID" PRIMARY KEY ("RowID"),
    CONSTRAINT "Companies ID" FOREIGN KEY ("CompaniesID")
        REFERENCES public."Companies" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PersonsID" FOREIGN KEY ("PersonsID")
        REFERENCES public."Persons" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Coaches"
    OWNER to postgres;

-- Table: public."Clients"

-- DROP TABLE public."Clients";

CREATE TABLE public."Clients"
(
    "RowID" bigint NOT NULL DEFAULT nextval('"Clients_RowID_seq"'::regclass),
    "PersonsID" bigint NOT NULL,
    "CompaniesID" bigint NOT NULL,
    "CoachesID" bigint,
    "Work_Company" character varying(200) COLLATE pg_catalog."default",
    "Work_Address" bigint,
    "Work_Title" character varying(200) COLLATE pg_catalog."default",
    "Work_Field" character varying(200) COLLATE pg_catalog."default",
    "Favorite_Book" character varying(500) COLLATE pg_catalog."default",
    "Favorite_Food" character varying(500) COLLATE pg_catalog."default",
    "Visit_Time_Preference_Start" time with time zone,
    "Visit_Time_Preference_End" time with time zone,
    "Call_Time_Preference_Start" time with time zone,
    "Call_Time_Preference_End" time with time zone,
    "Goals" text COLLATE pg_catalog."default",
    "Needs" text COLLATE pg_catalog."default",
    CONSTRAINT "ClientsID" PRIMARY KEY ("RowID"),
    CONSTRAINT "AddressID" FOREIGN KEY ("Work_Address")
        REFERENCES public."Addresses" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "CoachesID" FOREIGN KEY ("CoachesID")
        REFERENCES public."Coaches" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "CompaniesID" FOREIGN KEY ("CompaniesID")
        REFERENCES public."Companies" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PersonsID" FOREIGN KEY ("PersonsID")
        REFERENCES public."Persons" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Clients"
    OWNER to postgres;

-- Table: public."Events"

-- DROP TABLE public."Events";

CREATE TABLE public."Events"
(
    "RowIDs" bigint NOT NULL DEFAULT nextval('"Events_RowIDs_seq"'::regclass),
    "ClientsID" bigint NOT NULL,
    "PhotoID" bigint,
    "CoachesID" bigint NOT NULL,
    "Name" character varying(250) COLLATE pg_catalog."default" NOT NULL,
    "Description" text COLLATE pg_catalog."default" NOT NULL,
    "Date" timestamp without time zone,
    CONSTRAINT "EventID" PRIMARY KEY ("RowIDs"),
    CONSTRAINT "ClientID" FOREIGN KEY ("ClientsID")
        REFERENCES public."Clients" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "CoachID" FOREIGN KEY ("CoachesID")
        REFERENCES public."Coaches" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT "PhotoID" FOREIGN KEY ("PhotoID")
        REFERENCES public."Photos" ("RowID") MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."Events"
    OWNER to postgres;