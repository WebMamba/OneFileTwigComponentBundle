# OneFileTwigComponent Bundle

> [!CAUTION]
> This Bundle not really aim for to be production ready and run on professional website, it's more like a POC.

This Bundle allow you to define Twig Component with his HTML, CSS, and Javascript in one file.

```twig
<div {{ attributes }}>
    <h3>Counter</h3>
    <button data-action='click->avatar#increment'>Click</button>
    <p>count <span data-avatar-target="count">0</span></p>
</div>

<style>
    h3 {
        color: yellow;
    }
</style>

<script>
    import { Controller } from "@hotwired/stimulus"

    export default class extends Controller {
        connect() {
            this.count = 0;
        }

        static targets = ['count'];

        increment() {
            this.count++;
            this.countTarget.textContent = this.count;
        }
    }
</script>
```
This Bundle make also your CSS and your Javascript scoped to this component, so by  using this bundle your sure that your CSS or Javascript will conflic with the rest of the page.

## Install

In your Symfony application install the bundle :

```bash
composer require webmamba/one-file-twig-component-bundle
```

then add the bundle to your `config/bundles.php`:

```php
return [
    //
    Webmamba\OneFileTwigComponentBundle\OneFileTwigComponentBundle::class => ['all' => true],
];
```

you need to tweak a bit the `assets/bootstrap.js`:

```js
import { startStimulusApp } from '@symfony/stimulus-bundle';
import Clipboard from 'stimulus-clipboard';

const app = startStimulusApp();

app.register('clipboard', Clipboard);

const elements = document.querySelectorAll('[data-ux-component-controller-files]');

console.log(elements);

elements.forEach((element) => {
    let file = element.getAttribute('data-ux-component-controller-files');
    let id = element.getAttribute('data-ux-component-id');

    import(file).then((result) => {
        app.register(id, result.default) ;
    });
});
```

and last step add the following config lines in your `config/packages/twig.yaml`

```yaml
twig:
  // ...
  paths: '%kernel.project_dir%/var/component-assets': ~
```

Congrats 🍾 the bundle is now fully installed.

## How to use it ?

You can now go on any component template you want and run the following : 

```twig
<p>Hello world</p>

<style>
{# The CSS you need #}
</style>

<script>
{# the JavaScript your need #}
</script>
```

## Why it's not on Symfony UX ?

I am not happy with the implementation right now. Few issue :
- We don't leverage AssetMapper
- I listen to the kernel.response event to modify the importmap
- We should have more cache
- We should have a command or something so when we deploy we compile all the CSS and Javascript from the TwigComponents template
- ...

## How could you help ?

You think you have a better implemenatation, you know how to fix the issues above, or anything, do a pull request or an issue. Everything here is open to discution. Thanks! See you soon! 
