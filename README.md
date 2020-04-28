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
<p>Create a Directory inside public folder name uploads and inside upload directory create media and inside it create 3 media named large, medium and thumbnail.</p>

<p>Run command</p>

```
bin/console asset:install
```
<p>In your layout file also add css and script file</p>

```
    <link href="{{ asset('bundles/mrkushalsharmamediamanager/css/dropzone.css') }}" rel="stylesheet" type="text/css"></link>
    <link href="{{ asset('bundles/mrkushalsharmamediamanager/css/style.css') }}" rel="stylesheet" type="text/css"></link>

    <script src="{{ asset('bundles/mrkushalsharmamediamanager/js/dropzone.js') }}"></script>
    <script src="{{ asset('bundles/mrkushalsharmamediamanager/js/bootbox.js') }}"></script>
    
    <script src="{{ asset('bundles/mrkushalsharmamediamanager/js/common.js') }}"></script>
    or {% include 'MrkushalSharma/Scripts/commonJs.html.twig' %}

    <script src="{{ asset('bundles/fintechmediamanager/js/media.js') }}"></script>
    or  {% include '@Fintech/Scripts/mediaJs.html.twig' %}
```
<p>Basic Example</p>

```
  <a href="#"  data-popup="tooltip" class="changeCover" data-original-title="Click here to upload image" class="cursor-pointer btn bg-fn-pink" >change</a>
  
$('.changeCover').fnMediaModal({
    onSuccess(res,_self){            
    
    }
});
```

<p>for CKEditor : onclick="insertCkeditorImage(this,'post_description')</p>
``` 
    <a href="#" data-textarea="post_description" class="btn btn-info btn-sm addmedia"
        id="addmedia" onclick="insertCkeditorImage(this,'post_description')">Add Media</a>
```
<p> Input type example : onclick="insertImageUrl(this, 'featuredImage')"</p>
```
<input type="text" id="url" 
name="url" maxlength="255" 
class="form-control featuredImage form-control" 
onclick="insertImageUrl(this, 'featuredImage')" 
placeholder="Click here to browse image.">
```

<p> to List Media : fn_media_list or /media/list</p>

<p> to List Media : fn_media_list or /media/list </p>

