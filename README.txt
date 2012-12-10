Theis Playlist Manager [TPM] is a web-based program to help radio stations organize, 
archive, and publish a record of the material they broadcast.

WEBSITE
http://dev.ultramoderne.net

ENVIRONMENT
I wrote the software on the following platform:
Linux OS [Redhat 7.3]
PHP 4.3.x
MySQL 3.23.x

And I have it running on:
Linux OS [Debian Testing [currently Sarge]]
PHP 4.1.2
MySQL 4.0.16

From that we can probably deduce that it runs on PHP4+, and MySQL 3.23+


INSTALL
TPM is organized so that you can run different parts of it on different
computers.  For example, I run the DJ-input and database portions off of an 
in-studio computer, and the www-available portion off of my webhost.  Here are
instructions on what i figure will be the most common installation type -- 
everything running off of one server.

First, untar the distribution and put it in any web-accessible location:

		bash$ tar -xvzf theis

Then you'll need to create a database and a user within MySQL.  I'll leave it 
to google to help you with that.  Once you have your database created, read 
the table information into the database;

		bash$ mysql -u [username] -p < sqldump.txt

You'll be prompted for your password, and with any luck your database will be 
ready to go.  

Now it's time do some configuration files. First load up configure.php in the 
base theis/ directory.  Enter the path of the directory in which the
config.inc.php file you want to edit resides.  The configurator will load
the values in those files [if they exist] as defaults.  Make the changes and
additions you like, then hit submit.  You will be told whether or not the
configuration succeeded.  Repeat for the other configuration file, if necessary.
That easy.  The files you need to do this to are:

		playlist/lib/config.inc.php
		world/config.inc.php

If you are upgrading, running configure.php in your new version will upgrade
config file without removing your old values.  When you're finished configuring,
you will want to set the permissions back on your config files, plus make 
configure.php unreadable by anyone but owner to prevent others from tampering
with your config files.

At this point you should be able to point your web browser towards
http://yourhost.example.com/path/theis/playlist/ and login. The initial admin
password is set to "torroja".  You'll want to change that right away from the
admin main menu.

You have the option of making a guest account called "guest".  There are 
security measures in place to keep this account from being exploited too badly,
but the safest thing is to not create a guest account.  Depends
on how you run your station.


CREDITS
Aaron Forrest [author/maintainer]
Phil Davidson [RSS Feed]
Sean Dockray [class libraries]
Jon Solomon [web design]
LICENSE
Copyright (C) 2003 Aaron Forrest

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

The author may be contacted at the following locations:
email: aaron@ultramoderne.net
web: http://www.ultramoderne.net || http://owensriver.ultramoderne.net
land:
Aaron Forrest
50 Columbia St., Ste. 6
Newark, NJ 07102

$Id: README.txt,v 1.5 2004/02/27 06:21:36 admin Exp $
