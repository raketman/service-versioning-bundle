
##Versioning Symfony Services


    composer require raketman/service-versioning-bundle

####Usage :

    
	services:
	    
	  #Specify the tag 'raketman.version.factory' - now this service will have versions
      #resolver - service that will determine the version
      
      app.validation.test:
        class: Symfony\Component\Form\FormTypeInterface
        tags:
          - {name: 'raketman.version.factory', resolver: 'app.version.resolver'}
    
      #The version is indicated through tags that correspond to the name of the service we want to version
      #version - value of version 
    
      app.validation.test_v1:
        class: AppBundle\Validation\V1
        tags:
          - {name: 'app.validation.test', version: 1 }
          - {name: 'app.validation.test', version: 3 }
    
      app.validation.test_v2:
        class: AppBundle\Validation\V2
        tags:
          - {name: 'app.validation.test', version: 2 }
		
		
	  # Version resolver
	  	
      app.version.resolver:
        class: AppBundle\Resolver\Version
        arguments:
            - '@request_stack'
            
            
            
Now you have the service "app.validation.test", upon receipt of which, depending on which version it will return
"app.version.resolver", if 1 then "app.validation.test_v1" or if 2 then "app.validation.test_v2 will return

#####For example, the implementation of "app.version.resolver" is as follows:

     namespace AppBundle\Resolver;
    
     use Raketman\Bundle\ServiceVersioningBundle\Resolver\VersionResolverInterface;
     use Symfony\Component\HttpFoundation\RequestStack;

     class Version implements VersionResolverInterface
     {
         /** @var RequestStack  */
         private  $request;
     
         public function __construct(RequestStack $request)
         {
             $this->request = $request;
         }
     
         public function getVersion()
         {
             return $this->request->getMasterRequest()->get('version') ? : 1;
         }
     }
    

Now we can use "app.validation.test", and get different versions depending on $ _GET ['version']

	 $validation = $this->get('app.validation.test');
	 
	 if $_GET['version'] === 1, then get_class($validation) - AppBundle\Validation\V1 // сервис app.validation.test_v1
	 if $_GET['version'] === 2, then get_class($validation) - AppBundle\Validation\V2 // app.validation.test_v2
	 if $_GET['version'] === 3, then get_class($validation) - AppBundle\Validation\V1 // app.validation.test_v1


The implementation of resolver can be any, for example, based on the current user, which will allow for various users
on the go to issue the necessary service implementations



If you use versioning in daemons, then do not forget to set the service mode
     
      shared: false
      
otherwise you will always be given the same instance after the first call