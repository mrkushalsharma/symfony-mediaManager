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

<p>In your layout file also add following file ps download bootbox and dropzone versiongreater than 5,</p>

```
<script src="{{asset('assets/Dropzone/bootbox.js')}}"></script>
<script src="{{asset('assets/Dropzone/dropzone.js')}}"></script>
```

<p>Basic Example</p>

```
<a href="#" class="mediaImage" onclick="insertImageUrl(this, 'mediaImage')"> Click here to open media manager</a>
```