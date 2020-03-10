# Survey system
## To get started:
1. `git clone https://github.com/evaldasNe/mokyklos_dienynas.git`
2. `cd mokyklos_dienynas`
3. `composer install`
4. If Symfony is not installed:
-   Install PHP 7.1 or higher and these PHP extensions (which are installed and enabled by default in most PHP 7 installations): [Ctype](https://php.net/book.ctype), [iconv](https://php.net/book.iconv), [JSON](https://php.net/book.json), [PCRE](https://php.net/book.pcre), [Session](https://php.net/book.session), [SimpleXML](https://php.net/book.simplexml), and [Tokenizer](https://php.net/book.tokenizer);
-   [Install Composer](https://getcomposer.org/download/), which is used to install PHP packages; (Should have been installed with <i><b>composer install</i></b> command)
-   [Install Symfony](https://symfony.com/download), which creates in your computer a binary called `symfony` that provides all the tools you need to develop your application locally.
7. Go back to your project folder and git bash there.
6. To run application you can use the local web server provided by Symfony.
```sh
symfony server:start
```
Open your browser and navigate to `http://localhost:8000/`. If everything is working, you'll see a home page. Later, when you are finished working, stop the server by pressing `Ctrl+C` from your terminal.
