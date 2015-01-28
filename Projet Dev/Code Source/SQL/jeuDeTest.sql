INSERT INTO Module (id_module, sonType, saDescription, nbBroche)
 VALUES
 (1, 'METEO', 'station meteo', 4),
 (2, 'COMMANDE', 'controle des vannes', 4);


 INSERT INTO BrocheModule (id_module, id_broche, sonType, sonModuleMeteo, saDescription)
 VALUES
 (1, 1,'CAPTEUR','','Temperature'),
 (1, 2,'CAPTEUR','','Humidite'),
 (2, 1,'INTERRUPTEUR','1','vanne1'),
 (2, 2,'INTERRUPTEUR','1','vanne2');

INSERT INTO ValeurBroche (id_module, id_broche, saDate, saValeur )
 VALUES
 (1, 1, '2012-11-19 19:47:00', 4.5),
 (1, 2, '2012-11-19 20:47:00', 5),
 (2, 1, '2012-11-19 19:47:00', 0),
 (2, 1, '2012-11-19 20:47:00', 1),
 (2, 1, '2012-11-19 21:47:00', 1),
 (2, 1, '2012-11-19 22:47:00', 1),
 (2, 2, '2012-11-19 20:47:00', 1);
