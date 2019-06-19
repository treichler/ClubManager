ClubManager
===========

ClubManager is a web-application to represent and organize a club, respectively a music-club.
The application is powered by CakePHP (current version: 2.10.14).


Features
--------

Here are just a few highlights of ClubManager:
* Blog, gallery and public event calendar to keep the world informed about your club's activities
* Organize all events within a single calendar
* Dedicated calendar for every club-member
* Organize your club's resources and its usage
* Optionally organize musicsheets and playlists
* Interface to SMS gateway and email to contact members
* Login area for members only

Take a look at [Stadtkapelle Bad Radkersburg](https://stadtkapellebadradkersburg.at)
to see an example in production.


Setup
-----

Create following files according to your requirements, take a look at the *.example files:
* `/app/Config/bootstrap.php`  (application specific settings)
* `/app/Config/core.php`       (debug-level, cache, security values,...)
* `/app/Config/database.php`   (database settings)
* `/app/Config/email.php`      (email settings)
* `/app/webroot/css/style.css` (the web-application's appearance)

Also copy the directory `/app/webroot/img.example` to `/app/webroot/img`.


Adapt database settings according to your provider's requirements:
* `/schema/schema.sql`
* `/app/Config/database.php`


Create database by loading:
* `/schema/schema.sql`
* `/schema/data.sql`


### Avoiding attacks from bots

There are three forms which could be potentially a target to bots:
* **Registration:** `https://<link_to_your_ClubManager>/users/add`
* **Rest Password:** `https://<link_to_your_ClubManager>/users/create_ticket`
* **Contact Form:** `https://<link_to_your_ClubManager>/contacts/contact`

To reduce the chance for bots to successfully send those forms, there are two mechanisms implemented:

**Legitimation Password**
This approach relies on a secret legitimation which is only known by club members.
Therefore the *legitimation* variable has to be set in the ClubManager system settings in `/app/config/bootstrap.php`.
The Legitimation Password works only for *Registration* and *Reset Password* forms.

**~~Recaptcha (Google)~~**
This is currently work in progress.
~~This approach uses Google's ReCaptcha and protects the three previously listed forms.
To get it up and running you have to create an account where you get your public and private key.
Write these keys to Settings for ReCaptcha in `/app/config/bootstrap.php`~~


First Run
---------

`https://<link_to_your_ClubManager>/privilegs`

the first user to be created `admin`


ClubManager's appearance
------------------------

Adapt the style-sheet and the style related images according to your requirements
* `/app/webroot/css/style.css`
* `/app/webroot/img/*`


Some Handy Links
----------------

[CakePHP](http://www.cakephp.org) - The rapid development PHP framework.

[Cookbook](http://book.cakephp.org) - THE Cake user documentation; start learning here!

[Galleria](http://galleria.io/) - JavaScript gallery

[Leaflet](https://leafletjs.com/) - an open-source JavaScript library for interactive maps

[TCPDF](http://www.tcpdf.org/) - PHP class for generating PDF documents

[CKEditor](http://ckeditor.com/) - Web text editor

[s3Slider](http://www.serie3.info/s3slider/) - A jQuery-based image slider

[d3](http://d3js.org/) - Data-Driven Documents 

[jQuery](http://jquery.com/) - JavaScript library

