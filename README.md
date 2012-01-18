# Phuby

some Ruby goodies for PHP 5.3+

Huge credit goes to [Phuby](https://github.com/speedmax/phuby) for such an awesome library.

## What's it got?

 - Base class `Object`. Classes utilizing Phuby should extend this.
 - Mixins with `Person::extend('Model');`
 - alias_method `Person::alias_method('job','occupation');`
 - `send` `respond_to` 
 - `initialize` `finalize`
 - `is_a` `is_an`
 - Some standard Ruby classes like `Enumerable`, `Arr`, `File`, etc.

## Usage

`require_once 'phuby/Phuby.php';`

```php
<?php

class Person extends \Phuby\Object
{

}
```
