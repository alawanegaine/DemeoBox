CREATE TABLE Module
(
	id_module 		int 									NOT NULL,
	sonType 		ENUM('METEO','COMMANDE','AUTRE')		NOT NULL,
	saDescription 	VARCHAR(100)							NULL,
	nbBroche 		INT 									NOT NULL, 
	PRIMARY KEY (id_module)
);

CREATE TABLE BrocheModule
(
	id_module 		int 									NOT NULL,
	id_broche 		int 									NOT NULL,
	sonType 		ENUM('CAPTEUR','INTERRUPTEUR','AUTRE') 	NOT NULL,
	sonModuleMeteo	INT 									NULL,
	saDescription 	VARCHAR(100)							NULL,
	PRIMARY KEY (id_broche, id_module),
	FOREIGN KEY (id_module) REFERENCES Module(id_module)
);

CREATE TABLE ValeurBroche
(
	id_module 		int 									NOT NULL,
	id_broche 		int 									NOT NULL,
	saDate			datetime 								NOT NULL DEFAULT '0000-00-00 00:00:00',
	saValeur 		FLOAT 									NOT NULL,
	PRIMARY KEY (id_broche, id_module, saDate),
	FOREIGN KEY (id_module,id_broche) REFERENCES BrocheModule(id_module,id_broche)
);

CREATE TABLE ProgrammationJour
(
	Jour 			VARCHAR(30) 							NOT NULL,
	id_module		INT 									NOT NULL,
	id_broche 		int 									NOT NULL,
	dateDebut		time 									NOT NULL DEFAULT '00:00',
	dateFin			time 									NOT NULL DEFAULT '00:00',
	humidite		INT   									NULL,
	temperature		INT 									NULL,
	luminosite		INT 									NULL,
	PRIMARY KEY (Jour, id_module, id_broche),
	FOREIGN KEY (id_module,id_broche) REFERENCES BrocheModule(id_module,id_broche)
);