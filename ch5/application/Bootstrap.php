<?php
use Doctrine\DBAL\Types\Type; 

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap { 
 
 
  protected function _initCaching()
  {
   /*
    * We inform the cache frontend that we want automatic serialization with a lifetime (in seconds) of 3600. This is the
    * default if omitted. This will apply to all items. We can also specify per-item expiration times. We want the
    * backend to be a file.
    * 
    * Note: If navigation support is added to this application, consult wikiw/application/modules/wiki/Bootstrap.php
    * as an example of using an input file with navigation stored in a .xml file. I may need another cache frontend, and
    * therefore the cache manager.
    * 
    * Comment: If you want your cache to be invalid whenever application/Bootstrap.php has been changd, which will happen if
    * new routes are added to the Bootstrap's _iniRoutes() method, you must use the 'master_files' frontend option. You 
    * must also specify 'File' as the first parameter rather than 'Core' to Zend_Cache::factory().
    */
    
    $frontendOptions = array('lifetime' => 3600, 'automatic_serialization' => true);
    $backendOptions = array('cache_dir' => APPLICATION_PATH . '/../data/cache');
    
    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
                                                 
    Zend_Registry::set('Zend_Cache', $cache);   

  }
  
  // Do not rename this method _initDoctrine() this will result in a circular dependency error.
  protected function _initDoctrineExtra()
  {
        
      $doctrine = $this->bootstrap('doctrine')->getResource('doctrine');
          
      $em = $doctrine->getEntityManager();
      
      Zend_Registry::set('em', $em);
  }

      
  protected function _initRoutes()
  {
        // Ensure the frontController has been bootstrapped.
        $this->bootstrap('frontController');

        $router = $this->frontController->getRouter();
        
        // Make sure that _initCaching() has been done.
        $this->bootstrap('caching');
        
        // We will programmatically build the routes in the array $myroutes, which we then cache.
        $cache = Zend_Registry::get('Zend_Cache');
                
        $myroutes = $cache->load('myroutes');
        
        if ($myroutes === false) { // Does cache exist already?

            $myroutes = array();

            // route to locate home page.
            $route = new Zend_Controller_Router_Route(
                'home',
                array(
                    'action'        => 'index',
                    'controller'    => 'index',
                    'module'        => 'default'
                )
            );
    
            //$router->addRoute('home', $route);
            $myroutes['home'] = $route;
    
            $route = new Zend_Controller_Router_Route(
                '/content/:page',
                array(
                    'action'        => 'display',
                    'controller'    => 'static-content', // will map tp StaticContentController
                    'module'        => 'default'
                )
            );
    
            //$router->addRoute('static-content', $route);
            $myroutes['static-content'] =  $route;
            
    
            $route = new Zend_Controller_Router_Route(
                    '/contact',
                    array(
                        'action' => 'index',
                        'controller' => 'contact',
                        'module' => 'default'
                        )
                    );
            
            //$router->addRoute('contact', $route);
            $myroutes['contact'] = $route;
            
            $route = new Zend_Controller_Router_Route(
                    '/catalog/item/display/:id',
                    array(
                        'action' => 'display',
                        'controller' => 'item',
                        'module' => 'catalog'
                        )
                    );
            
            //$router->addRoute('catalog-display', $route);
            $myroutes['catalog-display'] =  $route;
            
              
            // TODO: Change this to work with Doctrine 2 paginator.
            $route = new Zend_Controller_Router_Route(
                   '/admin/catalog/item/index/',
                   array(
                       'action' => 'index',
                       'controller' => 'admin.item',
                       'module' => 'catalog'
                      )
                   );

            $myroutes['admin-catalog-index'] = $route;
            
            $route = new Zend_Controller_Router_Route(
                   '/admin/catalog/item/display/:id',            
                   array(
                       'action' => 'display',
                       'controller' => 'admin.item',
                       'module' => 'catalog'
                       )
                   );

            $myroutes['admin-catalog-display'] = $route;

            $route = new Zend_Controller_Router_Route(
                   '/admin/catalog/item/update/:id',
                    array(
                          'action' => 'update',
                          'controller' => 'admin.item',
                          'module' => 'catalog',
                           'id' => ""
                         )
                      ); 


            $myroutes['admin-catalog-update'] = $route;

            $route = new Zend_Controller_Router_Route(
                             '/admin/catalog/item/delete',
                    array(
                          'action' => 'delete',
                          'controller' => 'admin.item',
                          'module' => 'catalog'
                         )
                      ); 

            $myroutes['admin-catalog-delete'] = $route;

            $route = new Zend_Controller_Router_Route(
                              '/admin/catalog/item/success',
                    array(
                          'action' => 'success',
                          'controller' => 'admin.item',
                          'module' => 'catalog'
                         )
                      ); 

            $myroutes['admin-catalog-success'] = $route;

            /*
             * Note: /admin/login is handled by the default module's login action, as if the user had typed
             * /login or /index/login.
             */
            
            $route = new Zend_Controller_Router_Route(
                           '/admin/login',
                    array(
                          'action' => 'login',
                          'controller' => 'login',
                          'module' => 'default'
                         )
                      ); 

            $myroutes['login'] = $route;

            $route = new Zend_Controller_Router_Route(
                          '/admin/login/success',
                    array(
                          'action' => 'success',
                          'controller' => 'login',
                          'module' => 'default'
                         )
                      ); 
            $myroutes['login-success'] = $route;

            $route = new Zend_Controller_Router_Route(
                           '/admin/logout',
                    array(
                          'action' => 'logout',
                          'controller' => 'login',
                          'module' => 'default'
                         )
                      ); 

            $myroutes['logout'] = $route;
            
             
            // By default, the second parameter to save -- if not spcified -- will be 'myroutes', the parameter
            // which was passed on the most recent load().
            $cache->save($myroutes);
 
         } // endif:  if ($myroutes === false) 

         $router->addRoutes($myroutes); 
      }                
      
}

