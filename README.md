## ToDo test case

#### Setup

```php
composer install
```

```php
php artisan migrate
```

```php
npm install && npm run dev
```

-- Enjoy --

---
#### API Reference

> Authorization:
> + HEADER email:email@domain.com
> + HEADER password:mypassword


+ POST /api/createTask
    + *title        {string}
    + *description  {text}
    + *priority     {integer} 1-5

     \* - required fields 


+ GET /api/getTasks
    + (filter)  'status'    0|1 (todo|done)
    + (filter)  'priority'  1-5
    + (filter)  'title'     {search string}
    + (orders)  createdAt|completedAt|priority
    + (sort)    ASC|DESC    (Default ASC)

    \- Example:
```
{{host}}/api/getTasks?status=0&priority=2&title=task 1&order=createdAt&sort=DESC
```    
___

+ PUT /api/updateTask
    + *ID   {integer}
    + title        {string}
    + description  {text}
    + priority     {integer} 1-5
      
    \* - required fields
    
+ PUT /api/completeTask
    + *ID   {integer}
      
    \* - required fields
    
+ DELETE /api/deleteTask
    + *ID   {integer}
      
    \* - required fields
