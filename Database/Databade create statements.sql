-- Database: Life_Coach

-- DROP DATABASE Life_Coach;

CREATE DATABASE Life_Coach
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.UTF-8'
    LC_CTYPE = 'en_US.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Table: Companies

-- DROP TABLE Companies;

CREATE TABLE Companies
(
    CompanyID Serial NOT NULL,
    AdminID bigint NOT NULL,
    Name character varying(500) COLLATE pg_catalog.default NOT NULL,
    Location character varying(500) COLLATE pg_catalog.default NOT NULL,
    CONSTRAINT CompanyID PRIMARY KEY (CompanyID)
);

-- Table: Addresses

-- DROP TABLE Addresses;

CREATE TABLE Addresses
(
    AddressID Serial NOT NULL,
    AdressLine1 character varying(200) COLLATE pg_catalog.default NOT NULL,
    AdressLine2 character varying(200) COLLATE pg_catalog.default,
    City character varying(200) COLLATE pg_catalog.default NOT NULL,
    Subdivision character varying(200) COLLATE pg_catalog.default,
    Zip character varying(50) COLLATE pg_catalog.default NOT NULL,
    Country character varying(200) COLLATE pg_catalog.default NOT NULL,
    CONSTRAINT AddressID PRIMARY KEY (AddressID)
);

-- Table: Photos

-- DROP TABLE Photos;

CREATE TABLE Photos
(
    PhotoID Serial NOT NULL,
    UploadDate timestamp without time zone,
    MIMEType character varying(250) COLLATE pg_catalog.default,
    File bytea,
    CONSTRAINT PhotoID PRIMARY KEY (PhotoID)
);

-- Table: Visits

-- DROP TABLE Visits;

CREATE TABLE Visits
(
    VisitID Serial NOT NULL,
    Date timestamp without time zone,
    Type character varying COLLATE pg_catalog.default NOT NULL,
    Reason character varying COLLATE pg_catalog.default,
    Location bigint,
    CONSTRAINT VisitID PRIMARY KEY (VisitID),
    CONSTRAINT AddressID FOREIGN KEY (Location)
        REFERENCES Addresses (AddressID) MATCH SIMPLE
);

-- Table: Relationship_Types

-- DROP TABLE Relationship_Types;

CREATE TABLE Relationship_Types
(
    Relationship_TypeID Serial NOT NULL,
    Type character varying(250) COLLATE pg_catalog.default NOT NULL,
    CONSTRAINT Relationship_TypeID PRIMARY KEY (Relationship_TypeID)
);

-- Table: Persons

-- DROP TABLE Persons;

CREATE TABLE Persons
(
    PersonID Serial NOT NULL,
    PhotoID bigint,
    Prefix character varying COLLATE pg_catalog.default,
    First_Name character varying COLLATE pg_catalog.default NOT NULL,
    Last_Name character varying COLLATE pg_catalog.default NOT NULL,
    Suffix character varying COLLATE pg_catalog.default,
    Email character varying COLLATE pg_catalog.default NOT NULL,
    Cell bigint NOT NULL,
    Home bigint,
    Work bigint,
    Extension integer,
    Date_of_Birth date,
    Address bigint,
    Middle_Name character varying(250) COLLATE pg_catalog.default,
    CONSTRAINT PersonID PRIMARY KEY (PersonID),
    CONSTRAINT AddressID FOREIGN KEY (Address)
        REFERENCES Addresses (AddressID) MATCH SIMPLE,
    CONSTRAINT PhotoID FOREIGN KEY (PhotoID)
        REFERENCES Photos (PhotoID) MATCH SIMPLE
);

-- Table: Relationships

-- DROP TABLE Relationships;

CREATE TABLE Relationships
(
    RelationshipID Serial NOT NULL,
    PersonID1 bigint NOT NULL,
    Relationship bigint NOT NULL,
    PersonID2 bigint NOT NULL,
    CONSTRAINT RelationshipID PRIMARY KEY (RelationshipID),
    CONSTRAINT PersonID1 FOREIGN KEY (PersonID1)
        REFERENCES Persons (PersonID) MATCH SIMPLE,
    CONSTRAINT PersonID2 FOREIGN KEY (PersonID2)
        REFERENCES Persons (PersonID) MATCH SIMPLE,
    CONSTRAINT Relationship_TypeID FOREIGN KEY (Relationship)
        REFERENCES Relationship_Types (Relationship_TypeID) MATCH SIMPLE
);

-- Table: Coaches

-- DROP TABLE Coaches;

CREATE TABLE Coaches
(
    CoachID Serial NOT NULL,
    PersonID bigint NOT NULL,
    ClientID bigint NOT NULL,
    CompanyID bigint NOT NULL,
    Superviser boolean NOT NULL,
    Password character varying(150) COLLATE pg_catalog.default NOT NULL,
    CONSTRAINT CoachID PRIMARY KEY (CoachID),
    CONSTRAINT CompanyID FOREIGN KEY (CompanyID)
        REFERENCES Companies (CompanyID) MATCH SIMPLE,
    CONSTRAINT PersonID FOREIGN KEY (PersonID)
        REFERENCES Persons (PersonID) MATCH SIMPLE
);

-- Table: Clients

-- DROP TABLE Clients;

CREATE TABLE Clients
(
    ClientID Serial NOT NULL,
    PersonID bigint NOT NULL,
    CompanyID bigint NOT NULL,
    Work_Company character varying(200) COLLATE pg_catalog.default,
    Work_Address bigint,
    Work_Title character varying(200) COLLATE pg_catalog.default,
    Work_Field character varying(200) COLLATE pg_catalog.default,
    Favorite_Book character varying(500) COLLATE pg_catalog.default,
    Favorite_Food character varying(500) COLLATE pg_catalog.default,
    Visit_Time_Preference_Start time with time zone,
    Visit_Time_Preference_End time with time zone,
    Call_Time_Preference_Start time with time zone,
    Call_Time_Preference_End time with time zone,
    Goals text COLLATE pg_catalog.default,
    Needs text COLLATE pg_catalog.default,
    CONSTRAINT ClientsID PRIMARY KEY (ClientID),
    CONSTRAINT AddressID FOREIGN KEY (Work_Address)
        REFERENCES Addresses (AddressID) MATCH SIMPLE,
    CONSTRAINT CompanyID FOREIGN KEY (CompanyID)
        REFERENCES Companies (CompanyID) MATCH SIMPLE,
    CONSTRAINT PersonID FOREIGN KEY (PersonID)
        REFERENCES Persons (PersonID) MATCH SIMPLE
);

-- Table: Events

-- DROP TABLE Events;

CREATE TABLE Events
(
    EventID Serial NOT NULL,
    ClientID bigint NOT NULL,
    PhotoID bigint,
    CoachID bigint NOT NULL,
    Name character varying(250) COLLATE pg_catalog.default NOT NULL,
    Description text COLLATE pg_catalog.default NOT NULL,
    Date timestamp without time zone,
    CONSTRAINT EventID PRIMARY KEY (EventID),
    CONSTRAINT ClientID FOREIGN KEY (ClientID)
        REFERENCES Clients (ClientID) MATCH SIMPLE,
    CONSTRAINT CoachID FOREIGN KEY (CoachID)
        REFERENCES Coaches (CoachID) MATCH SIMPLE,
    CONSTRAINT PhotoID FOREIGN KEY (PhotoID)
        REFERENCES Photos (PhotoID) MATCH SIMPLE
);


-- Table: Notes

-- DROP TABLE Notes;

CREATE TABLE Notes
(
    NoteID Serial NOT NULL,
    ClientID bigint NOT NULL,
    CoachID bigint NOT NULL,
    VisitID bigint NOT NULL,
    PhotoID bigint,
    Description text COLLATE pg_catalog.default NOT NULL,
    Date_Added timestamp without time zone,
    CONSTRAINT NoteID PRIMARY KEY (NoteID),
    CONSTRAINT ClientID FOREIGN KEY (ClientID)
        REFERENCES Clients (ClientID) MATCH SIMPLE,
    CONSTRAINT CoachID FOREIGN KEY (CoachID)
        REFERENCES Coaches (CoachID) MATCH SIMPLE,
    CONSTRAINT PhotoID FOREIGN KEY (PhotoID)
        REFERENCES Photos (PhotoID) MATCH SIMPLE,
    CONSTRAINT VisitID FOREIGN KEY (VisitID)
        REFERENCES Visits (VisitID) MATCH SIMPLE
);

