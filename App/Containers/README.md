## CONTAINER

```
### DO NOT CHANGE ANYTHING HERE!! ###
```

Creating Container file guide

1. Follow PHP7 coding guide : 
    **PSR-2 Coding Standards**

2. Must be Contain namespace { //... code }

```php
<?php
namespace {
    // this is the code
}
```

3. Must be returning values that need to require ( must be has return values as include file )

```php
<?php
namespace {
    /**
     * Closure Object Return Value 
     */
    return function () : string // return type
    {
        return 'Example Return Value';
    };
}
```

### NOTE

- Context Container can be access this, but it will be returning :

`object \PentagonalProject\ProjectSeventh\Arguments`

- Get Instance application use the :

`$this[\PentagonalProject\ProjectSeventh\Application::APP_KEY]` 

or

`$this->get(\PentagonalProject\ProjectSeventh\Application::APP_KEY)`
