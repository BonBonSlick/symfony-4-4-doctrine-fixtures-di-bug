Run
```composer dump-autoload & composer cache clean```
 to see this error

***"Warning: This development build of composer is over 60 days old. It is recommended to update it by running "/usr/local/bin/composer self-update" to get the latest version.
Generated autoload files containing 580 classes

In DefinitionErrorExceptionPass.php line 54:

  Cannot autowire service "App\Fixtures\PostFixtures": argument "$factory" of method "__construct()" references interface "App\Entity\PostFactoryInterface" but no such service exists. Did you create a class that implements this  
  interface?"***
