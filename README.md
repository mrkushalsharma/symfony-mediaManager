<p>A Symfony 4 Media Manager</p>
**Basic Usage**

<p>In your routes.yaml file add,</p>

```
media_manager:
  resource: "@MrkushalSharmaMediaManagerBundle/Resources/config/routing.yaml"
  prefix:   /

```
<p>In your twig.yaml file, add</p>

```
paths:
    '%kernel.project_dir%/vendor/mrkushalsharma/symfony-media-manager/src/Templates': MrkushalSharma
```

<p>In your layout file add,</p>

```
{% include 'MrkushalSharma/Scripts/commonJs.html.twig' %}
```
<p>Run command</p>

```
bin/console asset:install
```
<p>In your layout file also add css and script file</p>

```
    <link href="{{ asset('bundles/mrkushalsharmamediamanager/css/dropzone.css') }}" rel="stylesheet" type="text/css"></link>
    <script src="{{ asset('bundles/mrkushalsharmamediamanager/js/dropzone.js') }}"></script>
    <script src="{{ asset('bundles/mrkushalsharmamediamanager/js/bootbox.js') }}"></script>
```

<p>Basic Example</p>

```
<a href="#" class="mediaImage" onclick="insertImageUrl(this, 'mediaImage')"> Click here to open media manager</a>
```