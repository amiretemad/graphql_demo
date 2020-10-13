# graphql_demo

```
composer install
```

# run project

```
php -S localhost:8080
```

# Sample Requets with POSTMAN  

```qraphql
query {
    getBooks {
        id
        title
    },
    getAuthors {
        id 
        name
    }
}
```


```qraphql
query {
    getBooks {
        title
    },
    getAuthors {
        id 
    }
}
```
