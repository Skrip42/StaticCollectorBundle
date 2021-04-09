symfony bundle that simplifies the work with statics

## premise:
Often your included widgets or custom form UI elements need their own styles and scripts.
The standard statics connection system offers 3 options for their connection:
- include statics in the template of a included widget or form element, which leads to duplicates and "splashing" of static across your final html
- include all static in the constructor template, which makes control more difficult and violates the dependency principle
- include all the statics in the base template, which leads to an unjustified increase in the size of the loaded statics and also violates the principle of dependencies
 
this bundle offers an alternative without these disadvantages

## how in works:
The StaticCollector is a global storage. During template execution, scripts, styles and assets are registered in the StaticsCollector.
After the template is compiled into html, the statics collector places the statics in the marked places

## install:
```shel
composer require Skrip42/static-collector-bundle
```
## usage:
- just put "{% static_place 'script' %}" and "{% static_place 'style' %}" tags in your base template where you want to see your included scripts and styles:
```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}PonBase{% endblock %} - PonBase</title>
        <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
        {% static_place 'style' %}
    </head>
    <body>
        ...
        {% static_place 'script' %}
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
### static type
you can specify which statics should be placed (styles, scripts or all)
```twig
    {% static_place 'style' %}
    {% static_place 'script' %}
    {% static_place 'all' %}
```

### static order control
you can define the order of connecting statics, specifying it manually
```twig
  {% static 'some_important_static' order 0 %}
```
the order number must be any integer. By default all statics have the order number 1000


### division static into group
you can mark statics as belonging to some group
```twig
  {% static 'some_static' group 'some_group' %}

  ...

  {% static_place all group 'some_group' %}
```
by default all statics are placed in the "default" group


### work with static from php
you can connect and receive statics from your php code:
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
