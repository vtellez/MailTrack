CREATE TABLE IF NOT EXISTS estados (
        codigo INT UNSIGNED NOT NULL AUTO_INCREMENT,
        descripcion VARCHAR (200),
	efrom VARCHAR(9) NOT NULL,
	email VARCHAR(9) NOT NULL,
	eto VARCHAR(9) NOT NULL,

        PRIMARY KEY(codigo)
        ) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS mensajes (
        mid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        message_id VARCHAR(150) NOT NULL,
        mfrom VARCHAR(120) NOT NULL,
        mto VARCHAR(120) NOT NULL,
        redirect VARCHAR(120) NULL,
        virus VARCHAR(150) NULL,
        ip VARCHAR(16) NOT NULL,
        fecha INT NOT NULL,
	asunto VARCHAR (255),
	estado INT UNSIGNED NOT NULL,

	UNIQUE(message_id,mto),
	INDEX(message_id),
	INDEX(mfrom),
	INDEX(mto),
	INDEX(fecha),
	INDEX(virus),

        PRIMARY KEY(mid)
	) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS aliases (
        aid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        local VARCHAR(150) NOT NULL,
        remote VARCHAR(150) NOT NULL,

        UNIQUE(local,remote),
        INDEX(local),

        PRIMARY KEY(aid)
        ) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS historial (
	hid INT UNSIGNED NOT NULL AUTO_INCREMENT,
	message_id VARCHAR(150) NOT NULL,
	estado INT UNSIGNED NOT NULL,
	hto VARCHAR(120) NOT NULL,
        fecha INT NOT NULL,
	maquina VARCHAR(15) NOT NULL,
	descripcion VARCHAR(255),
	adicional VARCHAR(255) NULL,

	UNIQUE(message_id,hto,estado),

	INDEX(message_id),

	PRIMARY KEY(hid),
 
        FOREIGN KEY(message_id) REFERENCES mensajes(message_id)
        ON DELETE CASCADE
	) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `ci_sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	session_start int(10) unsigned DEFAULT 0 NOT NULL,
	session_last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	session_ip_address varchar(16) DEFAULT '0' NOT NULL,
	session_user_agent varchar(50) NOT NULL,
	session_data text default '' NOT NULL,
	
	PRIMARY KEY (session_id)
	);


CREATE TABLE IF NOT EXISTS estadisticas (
	procesados INT UNSIGNED,
	ham INT UNSIGNED,
	spam INT UNSIGNED,
	virus INT UNSIGNED,
	politicas INT UNSIGNED,
	listas INT UNSIGNED,
	listas_error INT UNSIGNED,
	busquedas INT UNSIGNED,
	informes INT UNSIGNED,
	visitas INT UNSIGNED,
	accesos INT UNSIGNED,
	redirecciones INT UNSIGNED,
	alias INT UNSIGNED,
	externo INT UNSIGNED,
	interno INT UNSIGNED,
	pop INT UNSIGNED,
	imap INT UNSIGNED,
	buzonweb INT UNSIGNED,
	
	PRIMARY KEY (procesados)
	);

INSERT INTO estadisticas (accesos,pop,imap,buzonweb,redirecciones,alias,externo,interno,procesados,ham,spam,virus,politicas,listas,listas_error,busquedas,informes,visitas) VALUES (0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

CREATE TABLE IF NOT EXISTS accesos (
        aid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        usuario VARCHAR(200) NOT NULL,
        ip VARCHAR(16) NOT NULL,
        tipo ENUM('buzonweb', 'pop', 'imap') NOT NULL DEFAULT 'buzonweb',
        protocolo VARCHAR (15) NOT NULL,
        fecha INT NOT NULL,
        contador INT NOT NULL DEFAULT 0,
        estado INT UNSIGNED NOT NULL,

        INDEX(usuario),
        INDEX(fecha),
        INDEX(tipo),
        INDEX(ip),

        UNIQUE(usuario,estado,protocolo,ip),

        PRIMARY KEY(aid)
        ) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS est_horas (
        eid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        year INT UNSIGNED NOT NULL,
        month INT UNSIGNED NOT NULL,
        day INT UNSIGNED NOT NULL,
        hour INT UNSIGNED NOT NULL,
	
        totales INT UNSIGNED DEFAULT 0,
        ham INT UNSIGNED DEFAULT 0,
        spam INT UNSIGNED DEFAULT 0,
        virus INT UNSIGNED DEFAULT 0,
        errormta INT UNSIGNED DEFAULT 0,
        politicas INT UNSIGNED DEFAULT 0,

        interno INT UNSIGNED DEFAULT 0,
        externo INT UNSIGNED DEFAULT 0,
        
	pop INT UNSIGNED DEFAULT 0,
        imap INT UNSIGNED DEFAULT 0,
        buzonweb INT UNSIGNED DEFAULT 0,
        smtp INT UNSIGNED DEFAULT 0,

        INDEX(year),
        INDEX(month),
        INDEX(day),

	UNIQUE(year,month,day,hour),

        PRIMARY KEY(eid)
        ) ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS est_dias (
        eid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        year INT UNSIGNED NOT NULL,
        month INT UNSIGNED NOT NULL,
        day INT UNSIGNED NOT NULL,

        totales INT UNSIGNED DEFAULT 0,
        ham INT UNSIGNED DEFAULT 0,
        spam INT UNSIGNED DEFAULT 0,
        virus INT UNSIGNED DEFAULT 0,
        errormta INT UNSIGNED DEFAULT 0,
        politicas INT UNSIGNED DEFAULT 0,

        interno INT UNSIGNED DEFAULT 0,
        externo INT UNSIGNED DEFAULT 0,

        pop INT UNSIGNED DEFAULT 0,
        imap INT UNSIGNED DEFAULT 0,
        buzonweb INT UNSIGNED DEFAULT 0,
        smtp INT UNSIGNED DEFAULT 0,

        INDEX(year),
        INDEX(month),
        INDEX(day),

        UNIQUE(year,month,day),

        PRIMARY KEY(eid)
        ) ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS est_meses (
        eid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        year INT UNSIGNED NOT NULL,
        month INT UNSIGNED NOT NULL,

        totales INT UNSIGNED DEFAULT 0,
        ham INT UNSIGNED DEFAULT 0,
        spam INT UNSIGNED DEFAULT 0,
        virus INT UNSIGNED DEFAULT 0,
        errormta INT UNSIGNED DEFAULT 0,
        politicas INT UNSIGNED DEFAULT 0,

        interno INT UNSIGNED DEFAULT 0,
        externo INT UNSIGNED DEFAULT 0,

        pop INT UNSIGNED DEFAULT 0,
        imap INT UNSIGNED DEFAULT 0,
        buzonweb INT UNSIGNED DEFAULT 0,
        smtp INT UNSIGNED DEFAULT 0,

        INDEX(year),
        INDEX(month),

        UNIQUE(year,month),

        PRIMARY KEY(eid)
        ) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS est_anios (
        eid INT UNSIGNED NOT NULL AUTO_INCREMENT,
        year INT UNSIGNED NOT NULL,

        totales INT UNSIGNED DEFAULT 0,
        ham INT UNSIGNED DEFAULT 0,
        spam INT UNSIGNED DEFAULT 0,
        virus INT UNSIGNED DEFAULT 0,
        errormta INT UNSIGNED DEFAULT 0,
        politicas INT UNSIGNED DEFAULT 0,

        interno INT UNSIGNED DEFAULT 0,
        externo INT UNSIGNED DEFAULT 0,

        pop INT UNSIGNED DEFAULT 0,
        imap INT UNSIGNED DEFAULT 0,
        buzonweb INT UNSIGNED DEFAULT 0,
        smtp INT UNSIGNED DEFAULT 0,

        INDEX(year),
        UNIQUE(year),

        PRIMARY KEY(eid)
        ) ENGINE=InnoDB;




INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (1,'_ok','_warning','_warning','Correo aceptado en la estafeta de entrada de correo.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (31,'_ok','_ok','_wait','Correo válido (no virus, no spam) encolado en máquinas antivirus.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (51,'_ok','_ok','_wait','Correo válido encolado en la estafeta de listas de distribución.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (131,'_ok','_warning','_wait','Correo encolado en antivirus, marcado como SPAM.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (151,'_ok','_ok','_wait','Correo encolado en la estafeta de salida esperando a ser entregado al MTA destino.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (161,'_ok','_warning','_wait','Correo encolado la estafeta de salida, marcado como SPAM.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (171,'_ok','_ok','_wait','Correo encolado en la estafeta de buzones esperando a ser entregado al buzón del usuario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (181,'_ok','_warning','_wait','Correo encolado en la estafeta de buzones, marcado como SPAM.');

INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (331,'_ok','_warning','_warning','Correo SPAM entregado con éxito en el buzón del destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (332,'_ok','_warning','_warning','Correo SPAM entregado con éxito en el MTA remoto destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (333,'_ok','_warning','_warning','Correo SPAM redirigido a una cuenta de correo secundaria desde el buzón del destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (334,'_ok','_warning','_warning','Correo SPAM entregado con éxito al dominio virtual o alias de la dirección destino.');

INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (351,'_ok','_ok','_ok','Correo entregado con éxito en el buzón del destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (352,'_ok','_ok','_ok','Correo entregado con éxito al MTA remoto del destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (362,'_ok','_ok','_ok','Correo redirigido a una cuenta de correo secundaria desde el buzón del destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (372,'_ok','_ok','_ok','Correo entregado con éxito al dominio virtual o alias de la dirección destino.');

INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (431,'_ok','_error','_error','Correo marcado como virus o malware. No será entregado al destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (432,'_ok','_error','_error','Correo marcado como violación de política. No será entregado al destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (433,'_ok','_error','_error','Correo marcado como violación de política de mailman. No será entregado al destinatario.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (452,'_ok','_ok','_error','Correo no entregado al MTA destino.');
INSERT INTO estados (codigo,efrom,email,eto,descripcion) VALUES (472,'_ok','_ok','_error','Correo no entregado al buzón del usuario.');


