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

> | Authorization:                  |
> | :---                            |
> | HEADER email:{email@domain.com} |
> |                                 |
> | HEADER password:{mypassword}    |
> |                                 |

---

*POST /api/createTask*

| Parameter              | Description | Comment                  |
| ---                    | ---         | ---                      |
| `pid`                  | `{integer}` | parentID, create subtask |
| `*title`               | `{string}`  |                          |
| `*description`         | `{text}`    |                          |
| `*priority`            | `{integer}` | 1 - 5                    |

>\* - required fields 

---

*GET /api/getTasks*

| Parameter              | Description | Comment                               |
| ---                    | ---         | ---                                   |
| [filter] `status`      | `{integer}` | 0 or 1                                | 
| [filter] `priority`    | `{integer}` | 1 - 5                                 |
| [filter] `title`       | `{string}`  |                                       |
| [orders] `order`       | `{string}`  | createdAt \/ completedAt \/ priority  |                      |
| [sort]   `sort`        | `{string}`  | ASC or DESC (Default ASC)             |

> Example:
```
{{host}}/api/getTasks?status=0&priority=2&title=task 1&order=createdAt&sort=DESC
```    

---

*PUT /api/updateTask*

| Parameter              | Description | Comment                  |
| ---                    | ---         | ---                      |
| `*id`                  | `{integer}` |                          |
| `title`                | `{string}`  |                          |
| `description`          | `{text}`    |                          |
| `priority`             | `{integer}` | 1 - 5                    |

>\* - required fields

---

*PUT /api/completeTask*

| Parameter              | Description | Comment                  |
| ---                    | ---         | ---                      |
| `*id`                  | `{integer}` |                          |

>\* - required fields

---

*DELETE /api/deleteTask*

| Parameter              | Description | Comment                  |
| ---                    | ---         | ---                      |
| `*id`                  | `{integer}` |                          |

>\* - required fields

---    
