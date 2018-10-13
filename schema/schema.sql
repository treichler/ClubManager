-- ****************************************************************************
-- Current Database: 'club_manager'
-- ****************************************************************************

/* Adapt the name of the database according to your reqirements */
DROP DATABASE IF EXISTS club_manager;
CREATE DATABASE club_manager;
USE club_manager;

-- ****************************************************************************
--                            User Administration
-- ****************************************************************************

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  email VARCHAR(50),
  username VARCHAR(50), /* by convention */
  password VARCHAR(50), /* by convention */
  ticket VARCHAR(50),
  ticket_created DATETIME DEFAULT NULL,
  blog_count INT COMMENT 'Holds the number of created blogs',
  comment_count INT COMMENT 'Holds the number of created comments',
  event_count INT COMMENT 'Holds the number of created events',
  gallery_count INT COMMENT 'Holds the number of created galleries',
  photo_count INT COMMENT 'Holds the number of uploaded photos',
--  privileg_count INT COMMENT 'Holds the number of assigned privilegs',
  upload_count INT COMMENT 'Holds the number of uploaded files',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'This table contains data for web-user authentication';

CREATE TABLE privilegs_users (
/*  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, */
  privileg_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL
--  created DATETIME DEFAULT NULL,
--  modified DATETIME DEFAULT NULL
)COMMENT 'This join-table resolves the HABTM relation between users and privilegs';

CREATE TABLE privilegs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'This table holds privilegs for user\'s permissions';

CREATE TABLE titles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50) COMMENT 'Full name of the title',
  acronym VARCHAR(50) COMMENT 'Acronym of the title',
  placement INT(1) COMMENT 'Distance to the name. Values near 0 are close to the name, negative is left, positive is right',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'This table holds the titles for academic degrees';

CREATE TABLE profiles_titles (
/*  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, */
  profile_id INT(11) NOT NULL,
  title_id INT(11) NOT NULL
--  created DATETIME DEFAULT NULL,
--  modified DATETIME DEFAULT NULL
)COMMENT 'This join-table resolves the HABTM relation between profiles and titles';

CREATE TABLE profiles (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL COMMENT 'Foreign key to users',
  storage_id INT(11) NOT NULL COMMENT 'Foreign key to storages to save a picture',
  salutation_id int(11),
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  birthday DATE,
  phone_private VARCHAR(50),
  phone_mobile VARCHAR(50),
  phone_office VARCHAR(50),
  phone_mobile_opt VARCHAR(50),
  email_opt VARCHAR(50),
  postal_code VARCHAR(50),
  street VARCHAR(50),
  show_name TINYINT(1) COMMENT 'If true, person\'s name may be shown public',
  show_photo TINYINT(1) COMMENT 'If true, person\'s photo may be shown public',
  is_composer TINYINT(1) COMMENT 'Set true, if person is composer',
  is_arranger TINYINT(1) COMMENT 'Set true, if person is arranger',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'This table contains people\'s personal data';

CREATE TABLE salutations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  article INT,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);


/*****************************************************************************/
/*                            Club Administration                            */
/*****************************************************************************/

CREATE TABLE memberships (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  profile_id INT(11) NOT NULL COMMENT 'Foreign key to states',
  state_id INT(11) NOT NULL COMMENT 'Foreign key to states',
  calendar_link VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Interface between profiles and the club';

CREATE TABLE states (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50) COMMENT 'A meaningful name for the state',
  description TEXT COMMENT 'A short description of the state',
  is_member TINYINT(1) COMMENT 'Is true if this is a real club membership',
  is_available TINYINT(1) COMMENT 'Is true if member is available for the club',
  set_availability TINYINT(1) COMMENT 'Is true if member has to be set on the event list',
  show_public TINYINT(1) COMMENT 'Set true if member may be shown public',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Describes the state of a membership';

/*
CREATE TABLE officials (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  membership_id INT(11) NOT NULL COMMENT 'Foreign key to profiles',
  name VARCHAR(50) COMMENT 'A meaningful description for the kind of official',
  name_female VARCHAR(50) COMMENT 'just in case that the kind of official has to be gendered',
  phone_private_is_public TINYINT(1),
  phone_mobile_is_public TINYINT(1),
  phone_office_is_public TINYINT(1),
  contact TINYINT(1),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);
*/

CREATE TABLE kinds (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  is_public TINYINT(1) COMMENT 'Set to true, if this kind of group should be shown on the public web',
  is_official TINYINT(1) COMMENT 'Set to true, if this kind of group is official (club administration, ...)',
  show_officials TINYINT(1) COMMENT 'If true membership\'s groups, where Group->Kind.is_official is set, may be shown public',
  show_in_availability_list TINYINT(1) COMMENT 'If true related groups are shown next to the meberships name in the availability list',
  show_contact TINYINT(1) COMMENT 'If true related groups membership\'s contacts are shown on start-page',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Describes the kind of the group';

CREATE TABLE groups (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  privileg_id INT(11) NOT NULL
    COMMENT 'The corresponding privileg allows granted users to manage the group\'s calendar',
  storage_id INT(11) NOT NULL,
  kind_id INT COMMENT 'Foreign key to kind of group',
  name VARCHAR(50),
  info TEXT,
  show_members TINYINT(1),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'The club\'s groups';

CREATE TABLE groups_memberships (
/*  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, */
  group_id INT(11) NOT NULL,
  membership_id INT(11) NOT NULL
--  created DATETIME DEFAULT NULL,
--  modified DATETIME DEFAULT NULL
);

CREATE TABLE availabilities (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  membership_id INT(11) NOT NULL,
  event_id INT(11) NOT NULL,
  is_available TINYINT(1),
  was_available TINYINT(1),
  info VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE events (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  group_id INT(11) NOT NULL,
  mode_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  customer_id INT(11) DEFAULT NULL COMMENT 'Points to the customer of an event.',
  location_id INT(11) DEFAULT NULL COMMENT 'Points to the location of an event.',
  official_start DATETIME DEFAULT NULL COMMENT 'The official beginning of an event.',
  show_official_start TINYINT(1) COMMENT 'Set true if official beginning has to be shown',
  start DATETIME DEFAULT NULL COMMENT 'The beginning of an event related to the club\'s members',
  stop DATETIME DEFAULT NULL,
  name VARCHAR(50),
  info TEXT,
  expiry INT COMMENT 'Number of days to calculate the deadline of an event.\
                      Usually derived from \'modes\'',
  quota TINYINT(1) COMMENT 'Set true if the event\'s fee is measured by quota.',
  availabilities_checked TINYINT(1),
  tracks_checked TINYINT(1),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE modes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50) COMMENT 'A meaningful name to describe the mode of an event',
  is_public TINYINT(1) COMMENT 'If true this kind of an event is public visible',
  set_availability TINYINT(1) COMMENT 'Is true if availabilities have to be set',
  set_track TINYINT(1) COMMENT 'Is true if tracks have to be set',
  expiry INT COMMENT 'Number of days to calculate the deadline to an event.\
                      Set to 0 if members do not need to be available',
  is_important TINYINT(1) COMMENT 'Is true if event has high priority',
  quota_default TINYINT(1) COMMENT 'Event\'s quota default value',
  location_default VARCHAR(50) COMMENT 'Event\'s default location',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Describes the mode of an event';

CREATE TABLE events_resources (
  event_id INT(11) NOT NULL,
  resource_id INT(11) NOT NULL
);

CREATE TABLE resources (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  is_location TINYINT(1) COMMENT 'Set true if ressource is a location e.g. a room.',
  info TEXT,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  street VARCHAR(50),
  postal_code VARCHAR(10),
  town VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'The events\' customers respectively organisers';

CREATE TABLE locations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  longitude DOUBLE,
  latitude DOUBLE,
  radius DOUBLE,
  show_on_map TINYINT(1) COMMENT 'Set true if location should be shown on map',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'The events\' locations';

CREATE TABLE tracks (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id INT(11) NOT NULL,
  musicsheet_id INT(11) NOT NULL,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Every piece of music played on every event';

CREATE TABLE musicsheets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  title VARCHAR(50),
  details TEXT,
  publisher_id INT(11),
  archives VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Holds all the music sheets';

CREATE TABLE composers_musicsheets (
  composer_id INT(11) NOT NULL,
  musicsheet_id INT(11) NOT NULL
);

CREATE TABLE arrangers_musicsheets (
  arranger_id INT(11) NOT NULL,
  musicsheet_id INT(11) NOT NULL
);

CREATE TABLE publishers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  details TEXT,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'The publisher of a music sheet';

CREATE TABLE sheets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  book_id INT(11) NOT NULL,
  musicsheet_id INT(11) NOT NULL,
  page INT,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Describes every page of each music book';

CREATE TABLE books (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  title VARCHAR(50),
  description TEXT,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'A book contains many music sheets';

CREATE TABLE contactpeople (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  profile_id INT(11) NOT NULL,
  footer_phone TINYINT(1) COMMENT 'If true show name and phone number in webside\'s footer',
  contactlist_email TINYINT(1) COMMENT 'If true show name and e-mail adress in contact-list',
  contactlist_phone TINYINT(1) COMMENT 'If true show name and phone number in contact-list',
  contact_recipient TINYINT(1) COMMENT 'If true message from contact-form is forwarded',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);


/*****************************************************************************/
/*                             Optional features                             */
/*****************************************************************************/

CREATE TABLE contactprotocols (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  name VARCHAR(50),
  report VARCHAR(100),
  profiles_selected INT COMMENT 'Holds the number of selected profiles',
  profiles_delivered INT COMMENT 'Holds the number of profiles which where contacted',,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE smsprotocols (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  contactprotocol_id INT(11) NOT NULL,
  profile_id INT(11) NOT NULL,
  msgid VARCHAR(50) COMMENT 'Message ID provided by the SMS-Gateway API',
  phone VARCHAR(50) COMMENT 'Phone number of the recipient',
  report VARCHAR(255) COMMENT 'Report of the SMS-Gateway interface',
  costs FLOAT COMMENT 'Costs of the SMS delivery',
  status VARCHAR(50) COMMENT 'Status of the delivery (set by callback)',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE storages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50),
  extension VARCHAR(50),
  size VARCHAR(50),
  type VARCHAR(50),
  folder VARCHAR(50),
  uuid VARCHAR(50),
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE types (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(50) COMMENT 'A name to describe the file-type',
  mime_type VARCHAR(50) COMMENT 'Valid mime-types for this specific file-type',
  file_extension VARCHAR(50) COMMENT 'Valid file-extensions for this specific file-type',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
) COMMENT 'Describes the file types';

CREATE TABLE uploads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  type_id INT(11) NOT NULL,
  storage_id INT(11) NOT NULL,
  title VARCHAR(50),
  date_stamp DATE DEFAULT NULL,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE photos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  gallery_id INT(11) NOT NULL,
  user_id INT(11) NOT NULL,
  is_creator TINYINT(1) COMMENT 'Set true if uploader is the creator of the picture.',
  title VARCHAR(50),
  original_id INT(11) NOT NULL COMMENT 'Original - this id points to storages',
  marked_id INT(11) NOT NULL COMMENT 'Marked - this id points to storages',
  thumbnail_id INT(11) NOT NULL COMMENT 'Thumbnail - this id points to storages',
  good INT COMMENT 'Positive votes on the photo',
  bad INT COMMENT 'Negative votes on the photo',
  sum INT COMMENT 'Sum of all votes on the photo',
  median FLOAT COMMENT 'Median of all votes on the photo',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE galleries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  photo_id INT(11) NOT NULL comment 'The Photo which should be used for the gallery\'s title',
  title VARCHAR(50),
  good INT COMMENT 'Positive votes on the gallery',
  bad INT COMMENT 'Negative votes on the gallery',
  sum INT COMMENT 'Sum of all votes on the gallery',
  median FLOAT COMMENT 'Median of all votes on the gallery',
  date_stamp DATE DEFAULT NULL comment 'A date close to the time when gallery\'s photos where taken',
  photo_count INT COMMENT 'Holds the value of attached photos',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE blogs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  title VARCHAR(50),
  body TEXT,
  storage_id INT(11) NOT NULL,
  good INT COMMENT 'Positive votes on the blog',
  bad INT COMMENT 'Negative votes on the blog',
  sum INT COMMENT 'Sum of all votes on the blog',
  median FLOAT COMMENT 'Median of all votes on the blog',
  time_stamp DATETIME DEFAULT NULL comment 'A date-time that fits to the blog',
  expiry DATE DEFAULT NULL comment 'Expiry date to give dedicated blogs a higher priority',
  comment_count INT COMMENT 'Holds the value of comments',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE blogs_tags (
  blog_id INT(11) NOT NULL,
  tag_id INT(11) NOT NULL
);

CREATE TABLE tags (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  name VARCHAR(255),
  blog_count INT DEFAULT NULL,
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE comments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  blog_id INT(11) NOT NULL,
  body TEXT,
  good INT COMMENT 'Positive votes on the comment',
  bad INT COMMENT 'Negative votes on the comment',
  sum INT COMMENT 'Sum of all votes on the comment',
  median FLOAT COMMENT 'Median of all votes on the comment',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE votes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key',
  user_id INT(11) NOT NULL,
  blog_votes INT COMMENT 'Count of votes on blogs',
  comment_votes INT COMMENT 'Count of votes on comments',
  gallery_votes INT COMMENT 'Count of votes on galleries',
  photo_votes INT COMMENT 'Count of votes on photos',
  created DATETIME DEFAULT NULL,
  modified DATETIME DEFAULT NULL
);

CREATE TABLE blogs_votes (
  blog_id INT(11) NOT NULL,
  vote_id INT(11) NOT NULL
);

CREATE TABLE commtents_votes (
  comments_id INT(11) NOT NULL,
  vote_id INT(11) NOT NULL
);

CREATE TABLE galleries_votes (
  gallery_id INT(11) NOT NULL,
  vote_id INT(11) NOT NULL
);

CREATE TABLE photos_votes (
  photo_id INT(11) NOT NULL,
  vote_id INT(11) NOT NULL
);


/*****************************************************************************/
/*                        Write some data to database                        */
/*****************************************************************************/

-- source data.sql
--source /opt/lampp/htdocs/stadtkapelle/additional_project_data/data.sql

