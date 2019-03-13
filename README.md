### TLOG - simple blog and cms-software ###

## Compatibility Issues ##
This Version is not backwards compatible to the versions downloaded before 1st of december. First of all, the Databaselayout has changed completely. We use *utf8mb4* now by default to allow emojis and more. The appcode is not in the webroot-directory anymore, the same counts for caches, inis, templates and so on.

## Disclaimer

## Installation ##
- ~~This software depends on some bower-components so please install bower (www.bower.io) and do a bower update before uploading to FTP~~
- This software depends on some composer-components as well. Composer install will help
- Upload this code to a standard php/mysql webserver capable of mod_rewrite and .htaccess. 
- Set Directory-Root to *public* 
- chmod the folder cache to write access for php. 
- edit config.ini.php (You'll find this in ``tf3framework/config/``)
- currently there's no install routine for SQL. 
- Import SQL in Folder SQL. After importing SQL IMMEDIATLY edit tff_users and setup another password for your admin (you can create a md5-hashed password. Add SALT infront of your pass.)
- Install yarn (npm i -g yarn)
- run ``yarn`` to install dependencies
- run ``yarn webpack run`` to compress assets


## Intro ##
TLOG is a dead simple content-management/blog-software with some funny scripts that make your working easier. But it might be buggy as hell. I use it in production and I have waited a loooooong time to release this stuff. In fact I am not even sure, if this program runs at your environment.

## Features ##
- Blazing fast
- Some sort of template-engine
- quite simple to use (but hard installation)
- mobile-friendly (through skeleton.css)

## Further Info ##
This is the work of several years with PHP. In fact, I'd do stuff different now, especially when it comes to separate logics from data and views (MVC) and when it comes to modularize this. Creating a full package, which is even compatible to "real" php-standards would be a step for a coming release but for now I am really happy with this software. I did my blog with it. I even did forums or shops with this babe. So, somehow this might be cool software. Some PHP-Devs just might think, this is bullshit. Nevertheless I enjoy using it and hopefully, you enjoy it, too.

## Final Infos ##

Please, please, please keep in mind that although I use Opensource a lot and I work as a dev all my life, I never released something on my own. So please don't fight me. Thankies. KTHXBYE


Live-Version is running at https://www.trancefish.de/
