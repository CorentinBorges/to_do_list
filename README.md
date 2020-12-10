# ToDo&Co

The ToDo&Co application

## Required

* Symfony 5.1
* MAMP Server
    * PHP 7.4.1
    * MySQL 5.7
    * composer 2.0.4

## Installing project

1.  Download:
    ```bash
    $ git clone https://github.com/CorentinBorges/to_do_list.git
    ```

2.  Install:
    ```
    $ composer install
    ```

3.  Configure your database in [.env](.env)
    ```
    DATABASE_URL= mysql://username:password@host:port/dbName?serverVersion=mysqlVersion
    ```

4.  Create the database
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migration:migrate
    ```
5. You can export fixtures **in dev environment**, if you want to try the project
   (UserIds: username='Jhon Doe', pass= 'userPass';
   AdminIds: username='Admin', pass='adminPass)
   ```bash
    php bin/console doctrine:fixtures:load
    ```

5. Connect the website locally with symfony:
    ```
    symfony serve -d
    ```
Your url must be 127.0.0.1:8000 if you don't have any other projects running with symfony at the same time.


## Built With
*   [Symfony 4.4](https://symfony.com/)
*   [Composer 2.0.4](https://getcomposer.org/)
*   [Twig 3.1.1](https://twig.symfony.com/)
*   [Doctrine 2.2](https://www.doctrine-project.org/index.html)