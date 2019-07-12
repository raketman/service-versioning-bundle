
##Версионирование сервисов symfony


    composer require raketman/service-versioning-bundle

####Примеры использования :

    
	services:
	    
	  #Указываем тэг 'raketman.version.factory' - теперь этот сервис будет обладать версиями
      #resolver - сервис, который будет определять версию
      
      app.validation.test:
        class: Symfony\Component\Form\FormTypeInterface
        tags:
          - {name: 'raketman.version.factory', resolver: 'app.version.resolver'}
    
      #Версии указывается через тэги, которые соответсвуют название сервиса, которого мы хоти версионировать
      #свойство version - значение версии
    
      app.validation.test_v1:
        class: AppBundle\Validation\V1
        tags:
          - {name: 'app.validation.test', version: 1 }
          - {name: 'app.validation.test', version: 3 }
    
      app.validation.test_v2:
        class: AppBundle\Validation\V2
        tags:
          - {name: 'app.validation.test', version: 2 }
		
		
	  # Определитель версии
	  	
      app.version.resolver:
        class: AppBundle\Resolver\Version
        arguments:
            - '@request_stack'
            
            
            
Теперь вы обладается сервисом app.validation.test, при получении которого в зависимости от того, какую версию вернет
app.version.resolver, вернется либо app.validation.test_v1, либо app.validation.test_v2

#####Например, реализация app.version.resolver следующая:

     namespace AppBundle\Resolver;
    
     use Raketman\Bundle\ServiceVersioningBundle\Resolver\IVersion;
     use Symfony\Component\HttpFoundation\RequestStack;

     class Version implements IVersion
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
    

Теперь мы можем использовать app.validation.test, и получать различные версии в зависимости от $_GET['version']

	 $validation = $this->get('app.validation.test');
	 
	 if $_GET['version'] === 1, then get_class($validation) - AppBundle\Validation\V1 // сервис app.validation.test_v1
	 if $_GET['version'] === 2, then get_class($validation) - AppBundle\Validation\V2 // app.validation.test_v2
	 if $_GET['version'] === 3, then get_class($validation) - AppBundle\Validation\V1 // app.validation.test_v1

	
Реализация resolver может быть любой, например на основе текущего пользователя, что позволит для различных пользователей 
на ходу выдавать нужные реализации сервисов