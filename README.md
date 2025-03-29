# Retro

Retro is a library that ties together different components that are needed to build web apps that use backend to generate all the content. It is named *Retro* because it lets us build apps like we did in early 2000s, but with far superior development experience. Wait, what? What was good about web application development back in early 2000s? Several things:

1. You create a file, and web server picks it up, no route to define.
2. Things make sense even without JavaScript, but JavaScript makes them so much better through progressive enhancement.
3. Database is the state, and HTML that server returns is rendered representation of that state at the moment.
4. No JSON between frontend and backend, web servers serve HTML, and browsers render it.
5. API as RPC, instead of every page begin an API endpoint.

Building blocks of Retro:

1. **Sitemap** enables file system based routing, provided by [Sitemap package](https://github.com/activecollab/sitemap),
1. **Form processing** using a simple mechanism to capture data from forms, validate it, and re-render the form with errors when needed.

To be continued…

## Project Structure

* /app/current/src - where application is being built
* /cache
* /config
* /logs
* /public/assets
* /public/index.php
* /test/unit/log
* /test/unit/src
* /test/unit/boostrap.php
* /upload
* .gitignore
* .php_cs.php
* .php_qc.php
* composer.json
* composer.lock
* phpunit.xml
* README.md
* VERSION
