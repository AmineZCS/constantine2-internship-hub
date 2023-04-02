# Constantine2  Internship  Hub [REST API]


This is a Laravel REST API of an internship management system

## Installation

1. Clone the repository: 
```
git clone https://github.com/AmineZCS/constantine2-internship-hub
```
2. Install dependencies: 
```
composer install
```
3. Create a copy of the `.env.example` file and rename it to `.env` and update the environment variables as necessary:
```
cp .env.example .env
```

4. Generate the application key: 
```
php artisan key:generate
```
5. Run the database migrations: 
```
php artisan migrate
```
6. (Optional) Run the database seeder to populate the database with some initial data: 
```
php artisan db:seed
```

## Usage

1. Run
 ```
 php artisan serve
 ```
2. Visit `http://localhost:8000`
3. Login with the default user
4. Email: `student@email.com`
5. Password: `password`
6. Enjoy!

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

The Laravel Sanctum project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
