=== Backup Scheduler ===

Author: SedLex
Contributors: SedLex
Author URI: http://www.sedlex.fr/
Plugin URI: http://wordpress.org/plugins/backup-scheduler/
Tags: backup, schedule, plugin, save, database, zip
Requires at least: 3.0
Tested up to: 4.8.2
Stable tag: trunk
License: GPLv3

With this plugin, you may plan the backup of your entire website (folders, files and/or database).

== Description ==

With this plugin, you may plan the backup of your entire website (folders, files and/or database).

You can choose: 

* which folders you want to save; 
* the frequency of the backup process; 
* whether your database should be saved; 
* whether the backup is stored on the local website, sent by email or stored on a distant FTP (support of multipart zip files)

This plugin is under GPL licence

= Multisite - Wordpress MU =

This plugin is compatible with Multisite installation. 

Each blog administrator may save their own data. 

The super-admin may save either its data or the whole website. By saving the whole site, the admin may create different SQL files for the subsite in order to ease the restoration of a single sub-site.

= Localization =

* Czech (Czech Republic) translation provided by Mik013, Mik013
* German (Switzerland) translation provided by PeterDbbert, BernhardKnab, scream
* German (Germany) translation provided by agent-test, agent, bartdev2000, Ditoran, GLassnig
* English (United States), default language
* Spanish (Spain) translation provided by Javier, AVfoto, charliechin, IgnacioCalvo, JordiVives, FelipeJAG, Sebas
* Farsi (Iran) translation provided by sehrama.ir
* Finnish (Finland) translation provided by AnttiSilvola
* French (France) translation provided by SedLex, wkpixearts, Matthieu, mutmut, anonymous, noaneo, TonyLand, AlexGulphe
* Indonesian (Indonesia) translation provided by ceceparif
* Indonesian (Indonesia) translation provided by Faleddo
* Italian (Italy) translation provided by SedLex, PuntoCon, Emilio, GiovanniCaputo
* Dutch (Netherlands) translation provided by Matrix, WybAnema, Jay
* Polish (Poland) translation provided by Opti, Lukasz, pablo, Misiek, MarekMackiewicz, Darbo, darbo, adam
* Portuguese (Brazil) translation provided by RainilsonRodriguis, GuiBeloto
* Portuguese (Portugal) translation provided by FranciscoRocha
* Russian (Russia) translation provided by GerinG, Slawka, Berdych
* Swedish (Sweden) translation provided by 
* Thai (Thailand) translation provided by tontan
* Turkish (Turkey) translation provided by SedLex
* Chinese (People's Republic of China) translation provided by YiscaJoe, jeffli

= Features of the framework =

This plugin uses the SL framework. This framework eases the creation of new plugins by providing tools and frames (see dev-toolbox plugin for more info).

You may easily translate the text of the plugin and submit it to the developer, send a feedback, or choose the location of the plugin in the admin panel.

Have fun !

== Installation ==

1. Upload this folder backup-scheduler to your plugin directory (for instance '/wp-content/plugins/')
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the 'SL plugins' box
4. All plugins developed with the SL core will be listed in this box
5. Enjoy !

== Screenshots ==

1. A list of all backup files
2. The configuration page of the plugin

== Changelog ==

= 1.5.13 =
* BUG: link to malware site in a submitted translation

= 1.5.12 =
* NEW: Various improvement of the core

= 1.5.11 =
* BUG: some installation have some difficulties to identify the type of the column of the SQL table, thus now the quote are set by default one all column

= 1.5.10 =
* NEW: deletion of temp files upon desinstall

= 1.5.9 =
* NEW: Add icons

= 1.5.8 =
* NEW: Exclusion folder is now possible with regexp

= 1.5.7 =
* NEW: Exclusion folder enabled
* NEW: Detailed HOW TO

= 1.5.6 =
* BUG : Problem of activation with version of PHP below 5.2

= 1.5.5 =
* NEW : Take into account blogs.dir and site

= 1.5.4 =
* NEW : By saving the whole site, the admin may create different SQL files for the subsite in order to ease the restoration of a single sub-site.

= 1.5.3 =
* BUG: On some configuration, &lt;? is not supported 

= 1.5.2 =
* NEW: You may now create subfolder in the FTP directory
* NEW: improve the look of the configuration page

= 1.5.1 =
* BUG: improve the summary mail
* NEW: indicate if the FTP transfer has been successful in the backend
* NEW: few enhancement in the framework

= 1.5.0 =
* Major improvement of the database backup
* the summary mail now displays the issues with the ftp transfer

= 1.4.0 -&gt; 1.4.4 =
* Change the URL of the plugin on Wordpress
* Some modification
* Some issues in the framework
* Cleaning the framework to avoid unnecessarly code
* A bug that do not delete the lock file when reseting the backup process
* Enhance the performance of the backup process and ensure error protection
* Improve the mail summary
* Enhance the feedback tab
* Improve the core

= 1.3.0 -&gt; 1.3.7 =
* FTP bug with some webhosting service
* FTP port may be changed
* The error message is muck more explicit
* Add a drop if exist in SQL table
* Bug with multisite and remove a false positive error with wordfence
* There was a bug in the regexp when the ftp were directed to the root folder without any slash at the end.
* Add deletion features when uninstalling the plugin
* Multisite compatible
* Improve the zip compatibilities
* Add log features

= 1.2.0 -&gt; 1.2.8 =
* Some spanned zip files were corrupted due to a bug in the index
* Remove short_open_tag 
* Tuning to be able to work with very huge database
* Bug with NULL values in the database
* FTP support
* Full site backup is now possible
* Bug correction when SQL has NULL value
* Add a link to delete manually the backup (feature requested by Mirza)
* You can also force a new update without sending the emails
* Improve error management and memory leakage

= 1.1.0 -&gt; 1.1.5 =
* Bug in the sql file : date and time managements were incorrect
* Add a time option for choosing the best moment to perform an automatic backup
* Display bug correction
* Add instructions to restore the backup :)
* Improve memory and time management for database extraction
* Add error messages if it is impossible to read/delete/modify files
* Add time and memory management for constrained configuration
* Improving zip decompression and path 
* Correction of a bug that occurs when server refuse to access / directory "open_basedir" restriction
* Update of the core

= 1.0.1 =
* First release in the wild web (enjoy)

== Frequently Asked Questions ==

= Forced backup never ends (but there is no displayed error) =

Be sure to stay on the configuration page : if you quit the page, the forced backup process will be killed !

= Scheduled backup is stucked =

Scheduled backup only works on website that have traffic.

Indeed, each visits triggers a piece of the backup process. 

Thus, if there is no traffic, the schedule backup process wont't occur. If there is very little traffic, the backup will be very long, etc

= I have an error message indicating that another backup is running =

This message may happen if the chunk size is set quite high. For instance, 40 Mo is clearly too big and server  server configuration of many webhosters will kill scripts which use too much memory.

Most of the case 5Mo is ok.

If you get this error, set the chunk size to 1Mo and if it solves your problem, increase this chunk size.

= Compatible Archive Software =

The backup will be in a multi-part format. In order to uncompress it, you should put all the backup in the same folder and open the .zip file with Winzip.

You may experience some "corruption" error. It is mainly due that archive software are not compatible with multi-part archives. I have tried with success:

* Winzip (version 16.0 tested),
* WinRar (some issue with UTF8 characters), and 
* IZArc (some issue with UTF8 characters). 

= NOT-Compatible Archive Software =

These software are *not* compatible with multi-part archives: 

* 7-zip, and 
* the Windows Explorer embedded function.

Do not hesitate to contact me if you face some issues.

= To restore the backups =

* install a fresh version of Wordpress on your server ; 
* unzip the backup (actually, the zip file comprises a plurality of files i.e. a multi-part zip (zip, z01, z02, etc.). These files should be saved in a same folder and your zip program (such as IZArc, Winzip, Winrar, ...) will do the job for you...
* If you have configured to save the entire installation, replace all the wordpress files by the one in the zip file and import the SQL files (at the root of the zip file, the files named *.sql1, *sql2, etc.) in your database (with for instance phpmyadmin). It is recommended to save your database first ;
* In other cases, replace the 'plugins',  'themes', 'uploads' folders (in the wp-content folder) with the one in the archive, replace the wp-config.php (at the root of your wordpress repository) with the one at the root of the zip file and  import the SQL files (at the root of the zip file, the files named *.sql1, *sql2, etc.) in your database (with for instance phpmyadmin). It is recommended to save your database first.

= The backup files are corrupted =

Be sure that all thz zip files (i.e. .zip, .z01, z02, etc.) are in the same folder.
If you have still this issue, please try with Winzip software.

* Where can I read more?

Visit http://www.sedlex.fr/cote_geek/
 
 
InfoVersion:6903da7dfd178da373fa0ca5fe9670e2eaca9d8a