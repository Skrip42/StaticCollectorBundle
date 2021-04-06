# StaticCollectorBundle
symphony bundle that simplifies the work with statics

## install:
```shel
composer require Skrip42/static-collector-bundle
```
## usage:
- just put "{% script_place %}" and "{% style_place %}" tags in your base template where you want to see your included scripts and styles:
```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}PonBase{% endblock %} - PonBase</title>
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        {% style_place %}
    </head>
    <body>
        ...
        {% script_place %}
    </body>
</html>
```
- now you can connect statics in any template you use
```twig
{% block custom_input_for_example %}
  ...
  {% static 'some_webpack_asset' %}
  {% static 'https://external.com/script.js?may_have=parameters' %}
  {% static 'https://external.com/style.css?may_have=parameters' %}
{% endblock %}
```

## advanced:
- you can connect and receive statics from your php code:
```php
namespace App\Some;

use Skrip42\Bundle\StaticCollectorBunde\StaticCollector;

class SomeClass
{
    public function someMethod(StaticCollector $staticCollector) 
    {
        //you can add some static to collector
        $staticCollector->addStatic('webpack_asset_or_external_link');
        //you can get style and script from staticCollector
        $staticCollector->getStyleTags();
        $staticCollector->getScriptTags();
        //you can remove all static from staticCollector
        $staticCollector->clear();
    }
}
```
