create database candidato1;
use candidato1;

CREATE TABLE estudiantes (
    id INT PRIMARY KEY,
    nombre_completo VARCHAR(200),
    carnet VARCHAR(20),
    fecha_nacimiento DATE
);

CREATE TABLE materias (
    id INT PRIMARY KEY,
    nombre VARCHAR(200),
    codigo VARCHAR(20)
);

CREATE TABLE matriculas (
    id INT PRIMARY KEY,
    id_estudiante INT,
    id_materia INT,
    carnet VARCHAR(20),
    fecha_nacimiento DATE,
    FOREIGN KEY (id_estudiante) REFERENCES estudiantes(id),
    FOREIGN KEY (id_materia) REFERENCES materias(id)
);

-- ejecutar procedure
EXEC sp_matricular_estudiante 'CARNET002', 'MAT001';

--estudiantes
INSERT INTO estudiantes (id, nombre_completo, carnet, fecha_nacimiento) VALUES
(1, 'Juan Pérez', 'CARNET001', '2000-05-15'),
(2, 'María Gómez', 'CARNET002', '1999-08-22'),
(3, 'Luis Martínez', 'CARNET003', '2001-12-03');

--materias
INSERT INTO materias (id, nombre, codigo) VALUES
(1, 'Matemáticas', 'MAT001'),
(2, 'Historia', 'HIS001'),
(3, 'Química', 'QUI001');

-- matricula
INSERT INTO matriculas (id, id_estudiante, id_materia, carnet, fecha_nacimiento) VALUES
(1, 1, 1, 'CARNET001', '2000-05-15'),
(2, 2, 2, 'CARNET002', '1999-08-22');




CREATE PROCEDURE sp_matricular_estudiante
    @carnet VARCHAR(20),
    @codigo_materia VARCHAR(20)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @id_estudiante INT;
    DECLARE @id_materia INT;
    DECLARE @fecha_nacimiento DATE;

    -- verificando si existe el estudiante
    SELECT @id_estudiante = id, @fecha_nacimiento = fecha_nacimiento
    FROM estudiantes
    WHERE carnet = @carnet;

    IF @id_estudiante IS NULL
    BEGIN
        THROW 50001, 'Error: El estudiante no existe.', 1;
    END

    -- Verificando si la materia existe
    SELECT @id_materia = id
    FROM materias
    WHERE codigo = @codigo_materia;

    IF @id_materia IS NULL
    BEGIN
        THROW 50002, 'Error: La materia no existe.', 1;
    END

    -- Verificando que no esté ya matriculado
    IF EXISTS (
        SELECT 1 FROM matriculas
        WHERE id_estudiante = @id_estudiante AND id_materia = @id_materia
    )
    BEGIN
        THROW 50003, 'Error: El estudiante ya está matriculado en esta materia.', 1;
    END

    -- Insertando la matrícula
    INSERT INTO matriculas (id, id_estudiante, id_materia, carnet, fecha_nacimiento)
    VALUES (
        (SELECT ISNULL(MAX(id), 0) + 1 FROM matriculas),
        @id_estudiante,
        @id_materia,
        @carnet,
        @fecha_nacimiento
    );
END


--creando vista para poder observar las 3 tablas
CREATE VIEW vista_matriculas_completa AS
SELECT
    m.id AS id_matricula,
    e.id AS id_estudiante,
    e.nombre_completo,
    e.carnet,
    e.fecha_nacimiento,
    ma.id AS id_materia,
    ma.nombre AS nombre_materia,
    ma.codigo AS codigo_materia
FROM matriculas m
INNER JOIN estudiantes e ON m.id_estudiante = e.id
INNER JOIN materias ma ON m.id_materia = ma.id;

select * from vista_matriculas_completa;



