CREATE TABLE `usuarios` (
                            `id` int NOT NULL AUTO_INCREMENT,
                            `username` varchar(50) NOT NULL,
                            `password` varchar(50) NOT NULL,
                            `nombres` varchar(200) NOT NULL,
                            `apellidos` varchar(200) NOT NULL,
                            `tipo_id` INT(200) NOT NULL,
                            PRIMARY KEY (`id`)
);

CREATE TABLE `actas` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `creador_id` int NOT NULL,
                         `asunto` varchar(200) NOT NULL,
                         `fecha_creacion` varchar(45) NOT NULL,
                         `hora_inicio` TIME NOT NULL,
                         `hora_final` TIME NOT NULL,
                         `responsable_id` int NOT NULL,
                         `orden_del_dia` TEXT NOT NULL,
                         `descripcion_hechos` TEXT NOT NULL,
                         `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`)
);

CREATE TABLE `asistentes` (
                              `id` int NOT NULL AUTO_INCREMENT,
                              `asistente_id` int NOT NULL,
                              `acta_id` int NOT NULL,
                              PRIMARY KEY (`id`)
);

CREATE TABLE `compromisos` (
                               `id` int NOT NULL AUTO_INCREMENT,
                               `acta_id` int NOT NULL,
                               `responsable_id` int NOT NULL,
                               `descripcion` TEXT NOT NULL,
                               `fecha_inicio` DATE NOT NULL,
                               `fecha_final` DATE NOT NULL,
                               PRIMARY KEY (`id`)
);

ALTER TABLE `actas` ADD CONSTRAINT `actas_fk0` FOREIGN KEY (`creador_id`) REFERENCES `usuarios`(`id`);

ALTER TABLE `actas` ADD CONSTRAINT `actas_fk1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios`(`id`);

ALTER TABLE `asistentes` ADD CONSTRAINT `asistentes_fk0` FOREIGN KEY (`asistente_id`) REFERENCES `usuarios`(`id`);

ALTER TABLE `asistentes` ADD CONSTRAINT `asistentes_fk1` FOREIGN KEY (`acta_id`) REFERENCES `actas`(`id`);

ALTER TABLE `compromisos` ADD CONSTRAINT `compromisos_fk0` FOREIGN KEY (`acta_id`) REFERENCES `actas`(`id`);

ALTER TABLE `compromisos` ADD CONSTRAINT `compromisos_fk1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios`(`id`);



INSERT INTO usuarios (nombres, apellidos, username, password, tipo_id) VALUES ('admin', 'admin', 'admin', 'admin', 1);
