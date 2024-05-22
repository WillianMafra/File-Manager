# File Manager 

### 📝 This project is similar to Google Drive. In File Maganer, you can upload files, download and share with another user. File Manager have login/register pages and use nested sets for better archives management. 
<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

## 🛠️ Made with Laravel, VueJS, Composer, NPM, PHP, Postgres, Docker
<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

## What you need to use File Manager:

- Docker
- Docker Compose
- Git

<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

## Initial Configuration 🚀

1. Clone this repository
   
```bash
    git clone https://github.com/WillianMafra/File-Manager.git
```

2. Access the project directory.

``` bash
    cd File-Manager
```

3. Install Composer dependencies.

```docker
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php83-composer:latest \
        composer install --ignore-platform-reqs
```
4. Create an alias to simplify using Sail.

```bash
    alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

5. Rename the .env copy file to .env.

```bash
    mv '.env.example' .env
```

6. Initialize the Docker environment using Sail.

```bash
    sail up -d
```

7. Access the application container.

```bash
    sail shell
```
8. Generate an application key.
   
```bash
    php artisan key:generate
```

9. Run database migrations.
    
```bash
    php artisan migrate
```

10. Create the link to storage files.
    
```bash
    php artisan storage:link
```

11.  Run npm install to install Node.js dependencies and npm run dev to compile assets.
```bash
    npm install
    npm run dev
```
<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

## Accessing the Application

After following the steps above, the File manager app should be up and running and accessible through the web browser at the following address:

```
http://localhost/register
```

After you made your registration, an email with confirmation was sent to
```
http://localhost:8025
```
<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

## Stopping Sail Environment

To stop the Sail environment, exit the application shell by typing

```bash
    exit
```
and then execute the following command in the project directory:

```bash
sail down
```
<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">
## 😄 Made by Willian Mafra and supported by TheCodeholic's youtube video<img src="https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif">

<a href="https://linkedin.com/in/willnmafra" target="_blank">
<img src="https://img.shields.io/badge/linkedin:  willnmafra-%2300acee.svg?color=405DE6&style=for-the-badge&logo=linkedin&logoColor=white" alt=linkedin style="margin-bottom: 5px;"/>
</a><br>
</a>

