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
* `/app/config/bootstrap.php`  (application specific settings)
* `/app/config/core.php`       (debug-level, cache, security values,...)
* `/app/config/database.php`   (database settings)
* `/app/config/email.php`      (email settings)
* `/app/webroot/css/style.css` (the web-application's appearance)

Also copy the directory `/app/webroot/img.example` to `/app/webroot/img`.


Adapt database settings according to your provider's requirements:
* `/schema/schema.sql`
* `/app/config/database.php`


Create database by loading:
* `/schema/schema.sql`
* `/schema/data.sql`


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

