
/*****************************************************************************/
/*  special german characters:                                               */
/*                                                                           */
/*  ä: Ã¤                                                                    */
/*  Ä: Ã„                                                                    */
/*  ö: Ã¶                                                                    */
/*  Ö: Ã–                                                                    */
/*  ü: Ã¼                                                                    */
/*  Ü: Ãœ                                                                    */
/*  ß: ÃŸ                                                                    */
/*****************************************************************************/


-- ****************************************************************************
--                            User Administration
-- ****************************************************************************

-- Insert some basic privilegs:
INSERT INTO privilegs (name,created) VALUES ('Administrator', NOW());       /*  1 */

INSERT INTO privilegs (name,created) VALUES ('Availability', NOW());        /*  2 */

INSERT INTO privilegs (name,created) VALUES ('Blog create', NOW());         /*  3 */
INSERT INTO privilegs (name,created) VALUES ('Blog modify', NOW());         /*  4 */
INSERT INTO privilegs (name,created) VALUES ('Blog expiry', NOW());         /*  5 */
INSERT INTO privilegs (name,created) VALUES ('Blog delete', NOW());         /*  6 */

INSERT INTO privilegs (name,created) VALUES ('Contact export', NOW());      /*  7 */
INSERT INTO privilegs (name,created) VALUES ('Contact email', NOW());       /*  8 */
INSERT INTO privilegs (name,created) VALUES ('Contact sms', NOW());         /*  9 */

INSERT INTO privilegs (name,created) VALUES ('File download', NOW());       /* 10 */
INSERT INTO privilegs (name,created) VALUES ('File upload', NOW());         /* 11 */
INSERT INTO privilegs (name,created) VALUES ('File modify', NOW());         /* 12 */
INSERT INTO privilegs (name,created) VALUES ('File delete', NOW());         /* 13 */

INSERT INTO privilegs (name,created) VALUES ('Gallery upload', NOW());      /* 14 */
INSERT INTO privilegs (name,created) VALUES ('Gallery modify', NOW());      /* 15 */
INSERT INTO privilegs (name,created) VALUES ('Gallery delete', NOW());      /* 16 */

INSERT INTO privilegs (name,created) VALUES ('Resource create', NOW());     /* 17 */
INSERT INTO privilegs (name,created) VALUES ('Resource modify', NOW());     /* 18 */
INSERT INTO privilegs (name,created) VALUES ('Resource delete', NOW());     /* 19 */

INSERT INTO privilegs (name,created) VALUES ('Track', NOW());               /* 20 */

INSERT INTO privilegs (name,created) VALUES ('Music book', NOW());          /* 21 */

INSERT INTO privilegs (name,created) VALUES ('Music database', NOW());      /* 22 */

INSERT INTO privilegs (name,created) VALUES ('Profile create', NOW());      /* 23 */
INSERT INTO privilegs (name,created) VALUES ('Profile modify', NOW());      /* 24 */
INSERT INTO privilegs (name,created) VALUES ('Profile delete', NOW());      /* 25 */

INSERT INTO privilegs (name,created) VALUES ('Customer create', NOW());     /* 26 */
INSERT INTO privilegs (name,created) VALUES ('Customer modify', NOW());     /* 27 */
INSERT INTO privilegs (name,created) VALUES ('Customer delete', NOW());     /* 28 */

INSERT INTO privilegs (name,created) VALUES ('Location create', NOW());     /* 29 */
INSERT INTO privilegs (name,created) VALUES ('Location modify', NOW());     /* 30 */
INSERT INTO privilegs (name,created) VALUES ('Location delete', NOW());     /* 31 */

-- set groups privilegs id to 101, to reserve the ids 1..100 for system privilegs
ALTER TABLE privilegs AUTO_INCREMENT = 101;


-- Insert some basic titles:
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Doktor', 'Dr.', -1, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Doktor der Technik', 'Dr.techn.', -1, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Magister', 'Mag.', -2, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Magister, Fachhochschule', 'Mag.(FH)', -2, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Diplom-Ingenieur', 'Dipl.-Ing.', -2, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Diplom-Ingenieur, Fachhochschule', 'Dipl.-Ing.(FH)', -3, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Bachelor of art', 'BA', 2, NOW());
INSERT INTO titles (name,acronym,placement,created)
  VALUES ('Bachelor of science', 'BSc', 2, NOW());

-- Insert salutations; article: 1='der', 2='die', 3='das':
INSERT INTO salutations (name,article,created)
  VALUES ('Frau', 2, NOW());
INSERT INTO salutations (name,article,created)
  VALUES ('Herr', 1, NOW());


/*****************************************************************************/
/*                            Club Administration                            */
/*****************************************************************************/

-- Insert some membership states:
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Aktiv',
'Wie die Bezeichnung schon sagt, wirken diese Mitglieder aktiv am geschehen des Vereins mit.
Sie sind daher in der Anwesenheitsliste eingetragen.
Zu den aktiven Mitgliedern zÃ¤hlen MusikerInnen, Marketenderinnen und FunktionÃ¤re.
Zugriffsberechtigungen:
Eigener Terminplan, gesamter Terminplan, Anwesenheitsliste, Mitgliederdaten, Kontaktliste, Geburtstagsliste.', 1, 1, 1, 1, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Ruhend',
'Ruhende Mitglieder wirken nicht aktiv am Geschehen des Vereins mit.
Sie sind daher auch nicht in der Anwesenheitsliste eingetragen.
Dennoch haben sie Ã¤hnliche Zugriffsberechtigungen wie aktive Mitglieder:
Gesamter Terminplan, Anwesenheitsliste, Mitgliederdaten, Kontaktliste, Geburtstagsliste.', 1, 0, 0, 0, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Ehrenmitglied',
'Ehrenmitglieder zeichnen sich durch langjÃ¤hrige verdienstvolle TÃ¤tigkeiten zum Wohle des Vereins aus.
Sie wirken aktiv nicht mehr am Vereinsgeschehen mit und sind daher auch nicht in der Anwesenheitsliste eingetragen.
Sie haben jedoch Ã¤hnliche Zugriffsberechtigungen wie aktive Mitglieder:
Gesamter Terminplan, Anwesenheitsliste, Mitgliederdaten, Kontaktliste, Geburtstagsliste.', 1, 0, 0, 1, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('UnterstÃ¼tzendes Mitglied', '', 0, 0, 0, 0, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Junior',
'Wie man aus der Bezeichnung vermuten kann, handelt es sich hier um junge Menschen, die sich aus Sicht des Vereins
in einer sogenannten EingewÃ¶hnungsphase befinden.
...
Die zugriffsberechtigungen sind im Gegensatz zu Mitgliedern stark eingeschrÃ¤nkt:
Eigener Terminplan', 0, 1, 1, 0, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Aushilfe',
'Aushilfen sind (meist bezahlte) "Retter in der Not".
Sie sind keine Mitglieder und haben daher nur eingeschrÃ¤nkte Zugriffsberechtigungen:
Eigener Terminplan', 0, 0, 1, 0, NOW());
INSERT INTO states (name,description,is_member,is_available,set_availability,show_public,created)
  VALUES ('Inaktiv', '', 0, 0, 0, 0, NOW());


-- Insert group types:
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Musikgruppe', 1, 0, 0, 0, 0, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Instrument', 0, 0, 0, 1, 0, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Register', 0, 0, 0, 0, 0, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('organisatorisch', 0, 0, 0, 0, 0, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Administration', 1, 0, 1, 0, 0, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Funktion (Kontaktperson)', 0, 1, 0, 0, 1, NOW());
INSERT INTO kinds (name,is_public,is_official,show_officials,show_in_availability_list,show_contact,created)
  VALUES ('Funktion', 0, 1, 0, 0, 0, NOW());

-- Insert some typical event modes:
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Probe', 0, 1, 0, 1, 0, 0, 'Thermenarena', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Gruppenprobe', 0, 1, 0, 1, 0, 0, 'Thermenarena', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Veranstaltung privat', 0, 1, 0, 5, 0, 0, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Veranstaltung Ã¶ffentlich', 1, 1, 1, 5, 0, 1, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Veranstaltung sehr wichtig privat', 0, 1, 0, 100, 1, 0, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Veranstaltung sehr wichtig Ã¶ffentlich', 1, 1, 1, 100, 1, 1, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Information', 0, 0, 0, 0, 0, 0, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Information Ã¶ffentlich', 1, 0, 0, 0, 0, 0, '', NOW());
INSERT INTO modes (name,is_public,set_availability,set_track,expiry,is_important,quota_default,location_default,created)
  VALUES ('Ausschusssitzung', 0, 1, 0, 1, 0, 0, 'Thermenarena', NOW());


/*****************************************************************************/
/*                             Optional features                             */
/*****************************************************************************/

-- Insert some file types:
INSERT INTO types (name,mime_type,file_extension,created)
  VALUES ('Protokolle Ausschusssitzung', 'application/pdf', 'pdf', NOW());
INSERT INTO types (name,mime_type,file_extension,created)
  VALUES ('Protokolle Jahreshauptversammlung', 'application/pdf', 'pdf', NOW());
INSERT INTO types (name,mime_type,file_extension,created)
  VALUES ('Musik Demoaufnahmen', 'audio/x-mpeg, application/zip', 'mp2, mp3, zip', NOW());



