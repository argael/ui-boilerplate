# Simple Frontend Boilerplate

I use this tools to prepare my Twig views on larger project. 
I still must add compilation tools and folder structure for JS and CSS. 

## The App

Use Docker to point the `public` folder or start internal PHP server :

    php -S 0.0.0.0:8080 -t public/

## `src` folder

Place the source files in this folder : 

- `data` : the data files corresponding the templates.
- `views` : the Twig files you can organise has you wish but with the main templates in the root.

### Example

    views/
        layouts/
            main.twig       - The main layout to extends in templates
        homepage.twig       - The Homepage to display
    data/
        homepage.php        - Return an array with the variables use in the Homepage.