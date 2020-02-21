/* !! PostgreSQL !! */

CREATE TABLE "users"
(
    id       SERIAL PRIMARY KEY, /* auto increment */
    name     VARCHAR(255),
    email    VARCHAR(255),
    password VARCHAR(255),
    tel      VARCHAR(255),
    role     INT,
    token    INT
);

CREATE TABLE "companies"
(
    id   SERIAL PRIMARY KEY, /* auto increment */
    name VARCHAR(255)
);

CREATE TABLE "rooms"
(
    id   SERIAL PRIMARY KEY, /* auto increment */
    name VARCHAR(255)
);

CREATE TABLE "equipment"
(
    id   SERIAL PRIMARY KEY, /* auto increment */
    name VARCHAR(255)
);

CREATE TABLE "room_equipment"
(
    id           SERIAL PRIMARY KEY, /* auto increment */
    room_id      INT REFERENCES rooms (id) ON DELETE CASCADE,
    equipment_id INT REFERENCES equipment (id) ON DELETE CASCADE
);

CREATE TABLE "meetings"
(
    id      SERIAL PRIMARY KEY, /* auto increment */
    user_id INT REFERENCES users (id) ON DELETE SET NULL,
    room_id INT REFERENCES rooms (id) ON DELETE SET NULL,
    duration INT,
    date    TIMESTAMP
);

CREATE TABLE "visitors"
(
    id         SERIAL PRIMARY KEY, /* auto increment */
    name       VARCHAR(255),
    email      VARCHAR(255),
    tel        VARCHAR(255),
    company_id INT REFERENCES companies (id) ON DELETE SET NULL,
    meeting_id INT REFERENCES meetings (id) ON DELETE CASCADE,
    check_in   TIMESTAMP DEFAULT NULL,
    check_out  TIMESTAMP DEFAULT NULL
);
