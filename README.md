# Yii2 Blog

Example yii2-blog

> The project is under construction.

## INSTALLATION

clone the repository for `pull` command availability:

```
git clone https://github.com/Dominus77/yii2-blog.git public_html
cd public_html
composer install
```

Init an environment:

```
cd public_html
php init
```

Select: Development

### Yii Application Requirement Checker 

See `http://yii2-blog.loc/requirements.php`

> Note: For production version it is recommended to delete the `frontend\web\requirements.php` file

If everything is in order, move on, otherwise configure the server configuration.

## Continue
Create a database, default configure yii2_advanced_start in `common\config\main-local.php`

```
//...
'components' => [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=yii2_blog',
        //...
    ],
    //...
],
//...
```

Apply migration:

```
php yii migrate
php yii migrate -p=@modules/blog/migrations
php yii migrate -p=@modules/comment/migrations
```

See all available commands:

```
php yii
```

Initialization RBAC:

```
php yii rbac/init
```

Create user, enter the command and follow the instructions:

```
php yii users/user/create
```

- Username: set username (admin);
- Email: set email (`admin@example.com`);
- Password: set password (min 6 symbol);
- Status: set status (0 - blocked, 1 - active, 2 - wait, ? - Help);

Assign role admin:

```
php yii rbac/roles/assign
```

- Username: set username (admin);
- Role: set role (admin, editor, manager, super_admin, user, ? - Help); (This set configure rbac module models Role.php, Permission.php and in folder components to RbacInit.php)


If you are installing the server into the public_html folder on the server with Apache, you must configure redirection.
At the root folder, create a public_html .hitaccess with the following content:

```
Options FollowSymLinks
AddDefaultCharset utf-8

<IfModule mod_rewrite.c>
    RewriteEngine On

    # the main rewrite rule for the frontend application
    RewriteCond %{REQUEST_URI} !^/(backend/web|admin)
    RewriteCond %{REQUEST_URI} !^/(api/web|api)
    RewriteRule !^frontend/web /frontend/web%{REQUEST_URI} [L]

    # redirect to the page without a trailing slash (uncomment if necessary)
    #RewriteCond %{REQUEST_URI} ^/admin/$
    #RewriteRule ^(admin)/ /$1 [L,R=301]
    # the main rewrite rule for the backend application
    RewriteCond %{REQUEST_URI} ^/admin
    RewriteRule ^admin(.*) /backend/web/$1 [L]

    # redirect to the page without a trailing slash (uncomment if necessary)
    #RewriteCond %{REQUEST_URI} ^/api/$
    #RewriteRule ^(api)/ /$1 [L,R=301]
    # the main rewrite rule for the api application
    RewriteCond %{REQUEST_URI} ^/api
    RewriteRule ^api(.*) /api/web/$1 [L]

    # if a directory or a file of the frontend application exists, use the request directly
    RewriteCond %{REQUEST_URI} ^/frontend/web
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # otherwise forward the request to index.php
    RewriteRule . /frontend/web/index.php [L]

    # if a directory or a file of the backend application exists, use the request directly
    RewriteCond %{REQUEST_URI} ^/backend/web
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # otherwise forward the request to index.php
    RewriteRule . /backend/web/index.php [L]

    # if a directory or a file of the api application exists, use the request directly
    RewriteCond %{REQUEST_URI} ^/api/web
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # otherwise forward the request to index.php
    RewriteRule . /api/web/index.php [L]

    RewriteCond %{REQUEST_URI} \.(htaccess|htpasswd|svn|git)
    RewriteRule \.(htaccess|htpasswd|svn|git) - [F]
</IfModule>
```

The web folder, the backend, frontend and api parts also add .hitaccess:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . index.php
```

Now frontend is available at `http://sitename.com`, and backend at `http://sitename.com/admin`, and api `http://sitename.com/api/v1/users`

## TESTING

Create a database, default configure yii2_advanced_start_test in `common\config\test-local.php`

```
//...
'components' => [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=yii2_blog_test',
    ],
]
//...
```

Apply migration:

```
php yii_test migrate
php yii_test migrate -p=@modules/blog/migrations
php yii_test migrate -p=@modules/comment/migrations
```

#### Run in console
Windows:
```
vendor\bin\codecept build
vendor\bin\codecept run
```
Other:
```
vendor/bin/codecept build
vendor/bin/codecept run
```
