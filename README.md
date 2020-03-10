# Survey system
## To get started:
1. Install Yarn `https://yarnpkg.com/getting-started/install`
2. `git clone https://github.com/evaldasNe/survey_system.git`
3. `cd survey_system`
4. `composer install`
5. `yarn install`
6. If Symfony is not installed:
-   Install PHP 7.1 or higher and these PHP extensions (which are installed and enabled by default in most PHP 7 installations): [Ctype](https://php.net/book.ctype), [iconv](https://php.net/book.iconv), [JSON](https://php.net/book.json), [PCRE](https://php.net/book.pcre), [Session](https://php.net/book.session), [SimpleXML](https://php.net/book.simplexml), and [Tokenizer](https://php.net/book.tokenizer);
-   [Install Composer](https://getcomposer.org/download/), which is used to install PHP packages; (Should have been installed with <i><b>composer install</i></b> command)
-   [Install Symfony](https://symfony.com/download), which creates in your computer a binary called `symfony` that provides all the tools you need to develop your application locally.
7. Go back to your project folder and git bash there.
8. Edit `28` row of `.env` file to set your database data: `DATABASE_URL=mysql://username:password@127.0.0.1:3306/DB_NAME?serverVersion=5.7`
9. To run application you can use the local web server provided by Symfony.
```sh
symfony server:start
```
Open your browser and navigate to `http://localhost:8000/`. If everything is working, you'll see a home page. Later, when you are finished working, stop the server by pressing `Ctrl+C` from your terminal.
