# Retro

Retro is a library that ties together different components that are needed to build web apps that use backend to generate all the content. It is named *Retro* because it lets us build apps like we did in early 2000s, but with far superior development experience. Wait, what? What was good about web application development back in early 2000s? Several things:

1. You create a file, and web server picks it up, no route to manually define.
2. Things make sense even without JavaScript, but JavaScript makes them so much better through progressive enhancement.
3. Database is the state, and HTML that server returns is rendered representation of that state at the moment.
4. No JSON between frontend and backend, web servers serve HTML, and browsers render it.
5. API as RPC, instead of every page begin an API endpoint.

Building blocks of Retro:

1. **Sitemap** enables file system based routing, provided by [Sitemap package](https://github.com/activecollab/sitemap),
1. **Form processing** using a simple mechanism to capture data from forms, validate it, and re-render the form with errors when needed.
1. **Controllers don't do any processing**, they just route the data to appropriate services, and return the result of their execution.
1. Services can optionally be exposed and used as JSON-RPC endpoints with minimal effort.

To be continued…

## Project Structure

* /app/current/src - where application is being built
* /cache
* /config
* /logs
* /public/assets
* /public/index.php
* /test/unit/log
* /test/unit/src
* /test/unit/boostrap.php
* /upload
* .gitignore
* .php_cs.php
* .php_qc.php
* composer.json
* composer.lock
* phpunit.xml
* README.md
* VERSION

## CRUD

To easily generate boilerplate code for entities, run following command in this sequence:

```bash
php app/current/bin/console.php retro:create_crud_service plural_entity_name BundleName
```

This command creates add, edit and delete services, as well as supporting result classes that can be recorded in service execution history.

```bash
php app/current/bin/console.php retro:create_crud_form plural_entity_name BundleName
```

This is not a requirement step, but it follows the naming convention that `create_crud_controller` command will pick up and use automatically.

```bash
php app/current/bin/console.php retro:create_crud_controller plural_entity_name BundleName
```

This command creates a set of controllers, one for working with collections of entities, and one for working with individual entities.

## Service Transactions

Services provide a simple structure to run all execution code within a transaction, and return service result:

```php
use ActiveCollab\Retro\Service\Service;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use ActiveCollab\Retro\Service\Result\Success\Success;

class MyService extends Service
{
    public function serviceMethod(): ServiceResultInterface
    {
        return $this->withinTransaction(
            function () {
                return new Success();
            },
            null,
        )
    }
}
```

`withinService()` method will commit the transaction on successful result, and roll it back on failure. While time saving for most scenario, this can be a problem if you want to keep something in the database even if the service fails. In that case, you can use:

1. `afterTransaction()` - to run code after the transaction,
2. `onTransactionException()` - to capture exceptions that are thrown within transaction closure.

```php
use ActiveCollab\Retro\Service\Service;
use ActiveCollab\Retro\Service\Result\RequestProcessingFailed\RequestProcessingFailed;
use ActiveCollab\Retro\Service\Result\ServiceResultInterface;
use ActiveCollab\Retro\Service\Result\Success\Success;
use Exception;

class LoginService extends Service
{
    public function __construct(private UsersRepositoryInterface $usersRepository)
    {
    }

    public function logUserIn(
        string $username, 
        string $password,
    ): ServiceResultInterface
    {
        return $this->withinTransaction(
            function () use ($username, $password) {
            
                // Capture any exceptions that are thrown during transaction execution.
                $this->onTransactionException(
                    function (Exception $e) use ($username) {
                        $this->usersRepository->logFailedLoginAttempt($e->getMessage(), $username);
                    }
                );
            
                if (!$this->usersRepository->validate($username, $password)) {
                
                    // This callback will be executed after the transaction is rolled back, due to failed service result.
                    $this->afterTransaction(
                        function () use ($username) {
                            $this->usersRepository->logFailedLoginAttempt('Invalid credentials', $username);
                        },
                    );
                    
                    return new RequestProcessingFailed(...);
                }
                
                // This call may throw an exception?
                $this->usersRepository->logUserIn($username);
                
                return new Success();
            },
            null,
        )
    }
}
```

## UI

Why would it be desirable to describe UI with PHP? Several reasons:

1. It's component library agnostic. When composed using basic elements, UI can be rendered using any component library. Shoelace is currently included, but it can be any UI kit that works with web components or plain HTML,
2. It's decoupled from template engine. Templating engine provides tags and blocks, like `{Button}`, or `{Badge}`, to make writing of templates easier. Actual HTML rendering is done by the component library specific renderer, and you can switch to a different component library simply by changing which renderer is being used, without touching the templates,
3. UI can be prepared in different layers of the application, not just in templates. Instead of having many checks, calculations, and manipulation in templates, you can simply "ask" entities, services, or utilities to provide you UI description that you just render in templates.
