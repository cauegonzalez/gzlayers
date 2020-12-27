# gzlayers
This package creates a layer structure to organize your laravel projects

More explanatory material available at: 
  - https://cauegonzalez.medium.com/utilizando-uma-arquitetura-de-camadas-no-desenvolvimento-laravel-60b3152d7be9 (portuguese version)

### How to Install
From the root of a laravel project, type the composer command:

```composer require cauegonzalez/gzlayers```


Once installed, the following artisan commands will be available:
```
gzlayers:makebo
gzlayers:makecontroller
gzlayers:makecrud
gzlayers:makemodel
gzlayers:makerepository
gzlayers:makerequest
gzlayers:makeresource
gzlayers:maketrait
```
You can call each one separately or choose the command that does the entire structure at once:

```php artisan gzlayers:makecrud --all```

The makecrud command with **--all** option will read the configured database (.env) and create all the files of the proposed structure for each table found
