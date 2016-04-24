CREATE DATABASE `mares` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE TABLE mares.estudiantes (
    nroCedula INT NOT NULL AUTO_INCREMENT, 
    nombre CHAR(100), 
    PRIMARY KEY (nroCedula) 
); 

CREATE TABLE mares.materia (
    id INT NOT NULL AUTO_INCREMENT,
    idMateria INT NOT NULL,
    fk_nroCedula INT NOT NULL,
    nombreMateria CHAR(30),
    nota DECIMAL(2,1) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(fk_nroCedula) REFERENCES Estudiantes(nroCedula)
);

CREATE TABLE mares.revision (
    id INT NOT NULL AUTO_INCREMENT,
    fk_idMateria INT NOT NULL,
    motivoRevision CHAR(30),
    PRIMARY KEY(id),
    FOREIGN KEY(fk_idMateria) REFERENCES Materia(id)
);

INSERT INTO estudiantes VALUES ( 1017224184, 'Alexis Rodríguez'); 
INSERT INTO estudiantes VALUES ( 1038481420, 'Joaquin Hernandez'); 

INSERT INTO materia VALUES (1,1,1017224184,'Fisica de Campos',5.2);
INSERT INTO materia VALUES (2,2,1017224184,'Fisica Mecanica',5.2);
INSERT INTO materia VALUES (3,3,1038481420,'VozIp',5.2);


