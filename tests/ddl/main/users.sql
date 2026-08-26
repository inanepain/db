CREATE TABLE users (
	id       INTEGER           NOT NULL
		PRIMARY KEY AUTOINCREMENT,
	username TEXT(20)          NOT NULL,
	password TEXT              NOT NULL,
	email    TEXT(20)          NOT NULL,
	details  JSON DEFAULT "{}" NOT NULL);

CREATE UNIQUE INDEX idx_email
	ON users (email);

CREATE UNIQUE INDEX idx_username
	ON users (username);
