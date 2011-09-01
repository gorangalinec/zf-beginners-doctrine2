### Setup 

1. The Bisna library was used to integrate Zend Framework with Doctrine 2. It can be downloaded at https://github.com/guilhermeblanco/ZendFramework1-Doctrine2 
   See http://www.kurttest.com/zfa/bisna.html for documentation on configuring your application.ini to work with Bisna.

2. The country table needs to be populated using ./scripts/insert-countries.php:

    ~/square/scripts$ php -f insert-countries.php
   
3. The Country entity is read-only. Country entities cannot be updated, only deleted or new Countries inserted. 

4. public/captcha must be readable and writeable by the webserver.

5. data/cache must be readable writeable by the webserver (see \#4 below).

6. The "zenddate" type from DoctrineExtensions is used in the StampItem entity. DoctrineExtensions is available at https://github.com/beberlei/DoctrineExtensions.
   DoctrineExtensions should be installed under one of your include_path directories. 
   Note: Use of "zenddate" will generate erroneous ALTER TABLE messages whenever "php doctrine.php orm:schema-tool:update --dump-sql" is done. 
   These can be ignored.

### Differences with book.

1. Unlike the book, the default module was not moved to application/modules/default. When the book was written, the "appnamespace" application.ini
   setting was not part of Zend Framework. "appnamespace" provides advantages that outweight moving the default module. In addition, the Bisna library
   relies on "appnamespace".

2. The contact emails are not sent immediately. Instead they are saved in a Zend queue database, using a Zend_Queue-derived class.
   You can uncomment the original code, if you don't want to use zend queue; otherwise, create the queue database schema using Zend/Queue/Adapter/Db/mysql.sql.
   Then suppy the necessary queue database setting ina application.ini.  You will also need a cron job that calls ./scripts/queue-processor, which reads
   the queue and sends the emails.
   Note: Change the define('APPLICATION_PATH', ...) at the top of queue-processor.php, to correspond to your particular application path.

3. The forms used exclusively in the default module begin with of Applicaton_ (the "appnamespace" prefix )rather than Square_, which the book uses, and
   they reside in application/forms not in library/Square/Form. Likewise, forms classes used exclusively in the catalog module begin with Catalog_Form prefix, 
   and they reside in application/modules/catalog/forms. 

5. Unlike the book, routes were not configured in application.ini, but instead are programmatically created in application/Bootstrap.php,
   where they are cached. 

6. Due to #3, caching support was added to cache the routes array. ./data/cache should be both read- and writeable by the webserver.  
   Important: If you add new routes to Bootstrap.php, you must erase the cache files under ./data/cache; otherwise, your new routes won't
   be recognized until the cache expires. 

7. A service layer, in application/modules/catalog/services, is used by the the catalog controllers.


kurt krueckeberg (kurtk at pobox dot com)
