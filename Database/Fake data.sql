INSERT INTO Companies(
	AdminID, Name, Location)
	VALUES (1, 'Fake News', 'NY');

INSERT INTO Persons(
	First_Name, Last_Name, Email, Cell, Date_of_Birth)
	VALUES ('Chris', 'Siena', 'Chris@ChrisSiena.com', '5166067217', '1996-02-21');
INSERT INTO Persons(
	Prefix, First_Name, Last_Name, Email, Cell, Work, Extension, Date_of_Birth)
	VALUES ('Dr.', 'Pablo', 'Rivas', 'Pablo.Rivas@Marist.edu', '8455839503', '8455753000', '2086', '1981-08-04');
INSERT INTO Persons(
	First_Name, Last_Name, Email, Cell, Date_of_Birth)
	VALUES ('Brad', 'Harris', 'Brad.Harris1@Marist.edu', '3159539832', '1995-09-29');
INSERT INTO Persons(
	First_Name, Last_Name, Email, Cell, Date_of_Birth)
	VALUES ('Marisa', 'Proscia', 'Marisa.Proscia1@Marist.edu', '8459303845', '1996-03-06');
INSERT INTO Persons(
	First_Name, Last_Name, Email, Cell, Date_of_Birth)
	VALUES ('Patrick', 'Zambri', 'Patrick.Zambri1@Marist.edu', '9084839943', '1995-08-22');
INSERT INTO Persons(
	First_Name, Last_Name, Email, Cell, Date_of_Birth)
	VALUES ('Jacob', 'Elevenson', 'Jacob.Elevenson1@Marist.edu', '2125398930', '1996-12-14');


INSERT INTO Relationship_Types(
	Type)
	VALUES ('Son');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Daughter');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Wife');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Husband');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Mother');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Father');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Brother');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Sister');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Mother-in-Law');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Father-in-Law');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Sister-in-Law');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Brother-in-Law');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Grandfather');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Grandson');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Daughter-in-Law');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Nephew');
INSERT INTO Relationship_Types(
	Type)
	VALUES ('Uncle');


INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (1, 6, 4);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (1, 10, 5);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (1, 13, 6);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (1, 6, 7);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (4, 1, 1);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (4, 4, 5);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (4, 6, 6);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (4, 7, 7);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (5, 3, 4);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (5, 15, 1);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (5, 5, 6);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (5, 11, 7);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (6, 1, 4);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (6, 1, 5);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (6, 14, 1);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (6, 16, 7);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (7, 7, 4);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (7, 12, 5);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (7, 17, 6);

INSERT INTO Relationships(
	PersonID1, Relationship, PersonID2)
	VALUES (7, 1, 1);

INSERT INTO Addresses(
	AdressLine1, City, Subdivision, Zip, Country)
	VALUES ('3399 North Rd', 'Poughkeepsie', 'New York', '12601', 'USA');

INSERT INTO Clients(
	PersonsID, CompaniesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('3', '1', 'Marist', 'Professor', '1', 'CS/ITS');
    
INSERT INTO Coaches(
	PersonsID, ClientsID, CompaniesID, Superviser, Password)
	VALUES ('3', '1', '1', '1', 'Password');
    
INSERT INTO Clients(
	PersonsID, CompaniesID, CoachesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('1', '1', '1', 'Marist', 'Student', '1', 'Comnputer Science');
    
INSERT INTO Clients(
	PersonsID, CompaniesID, CoachesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('4', '1', '1', 'Marist', 'Student', '1', 'Information Systems');
    
INSERT INTO Clients(
	PersonsID, CompaniesID, CoachesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('5', '1', '1', 'Marist', 'Student', '1', 'Comnputer Science');
    
INSERT INTO Clients(
	PersonsID, CompaniesID, CoachesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('6', '1', '1', 'Marist', 'Student', '1', 'Information Technology');
    
INSERT INTO Clients(
	PersonsID, CompaniesID, CoachesID, Work_Company, Work_Address”, Work_Title, Work_Field)
	VALUES ('7', '1', '1', 'Marist', 'Student', '1', 'Comnputer Science');



INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-08-30 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-09-06 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-09-13 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-09-20 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-09-27 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-10-04 18:30:00', 'In Person', 'General');
    
INSERT INTO Visits(
	Date, Type, Reason)
	VALUES ('2017-10-11 18:30:00', 'In Person', 'General');

INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','1','2017-10-11 17:30:00','Class');

INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','2','2017-10-11 17:30:00','Class');
    
INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','3','2017-10-11 17:30:00','Class');

INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','4','2017-10-11 17:30:00','Class');
    
INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','5','2017-10-11 17:30:00','Class');

INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','6','2017-10-11 17:30:00','Class');

INSERT INTO Notes(
	ClientID, CoachID, VisitID, Date_Added, Description)
	VALUES ('5','1','7','2017-10-11 17:30:00','Class');

INSERT INTO Events(
	ClientsID, CoachesID, Name, Description, Date)
	VALUES ('5', '1', 'New born son', 'Patrick was born', '1995-08-22 00:00:00');