# ProjectBaseV2

Rewrite ProjectBase



ProjectBase V2
===========

That the second big rewrite of ProjectBase

I started this project for learn how to build something like a CMS from scratch, with plugins support, and other things for learning
more deeply PHP.

This code are in very early stage, have bugs, security problems and messy code. This code can change completely and probably will change. 

EXAMPLE SITE
============
Early example XXXX (sometimes not work since its the production web)

BUGS
=========
Android: JQUERY/JS Login, not prompt for remember.
Cyclic problem, Blocks need session (later use) but sessions need frontend and frontend blocks, works if we remove sessions as depend of blocks.
and many other...

LICENSING
=========
TODO

REQUIREMENTS
============
TODO
/cache: (if CSS_OPTIMIZE)

chown www-data:www-data ???? && chmod 755 ????

INSTALLATION
============
TODO

DEVELOPEMENT
============

Author
------

* Diego Garcia <diego@envigo.net>

Lastest add/canges? first latest
================================
* NewsMediaUpload rewrite and works.
* seperating UI lang from news lang (news_lang)
* Improved frontend menus
* Added News Search, NewsComments
* Added stdComments
* News / Frontend - Improve
* News - rewrite phase.3
* CORE.inc && frontend, 
use frontent for load pages, each plugin must register the pages allowed to get
added the option for load a page on disk or a virtual page (function based)
* SMBasic * Perms
* News * Perms
* Links/Core - Table for keep links
  Added to core
* MiniEditor - Text editor and formating for news and others
  added.
* News
  added, rewrite...
* CORE
  added install, upgrade, uninstall procedure, 
  added plugin managaner
* SMBasic - Support sessions (simple at this momment)
  Rewrite to V2  
* tplbasic: Template system... not just better a code separator
   Rewrite V2
   Support for switch load CSS inline files or build and cache in unique file.
* Frontend - Provided the main interface to display the page and logic
  add index style support
  add blocks supports
  New plugin 
* Blocks - Provide blocks and blocks logic for configure index and other pages
  New plugin
  Add few standard blocks.
* Google Analytcs - Add news analytcs code 
  Rewrite to V2    
* SimpleCategories -   Categories features
  Rewrite to V2
* SimpleACL -Support por ACL (Access Control List), simple and not tested enough
  Rewrite to V2
* Admin - Admin features for  core and plugins
  Rewrite to V2
* DebugWindow - Debug textbox for debug messages
  Rewrite to v2
* ExampleTemplate - Plugin template
  Rewrite to v2
* Multilang  - Support website in multiple languages
  Rewrite to v2
* MysqlDB (Core) Mysql database 
  Rewrite to v2
* Plugin support (Core) Plugins
  Rewrite to v2

  