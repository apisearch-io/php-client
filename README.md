# SEARCH PHP library

This library aims to provide a php developer nicely interface to manage all the
processes related to the Search API using basic domain objects.

* Model objects
* Query object
* Response object
* Index api
* Query api


## Model objects

The library provides you a set of model objects. Both the Index and the Query
Apis will understand these objects and will work with them in both sides (the
client and the server side), so make sure you know how these objects are built.

### Product

A simple view of what a purchasable object is. A product can be anything you
want, but take in account that this is the object in the middle, and the main
Query api is going to work mainly against it.

| Field  | Type  | What is that  | Mandatory?  | Boost  |
|---|---|---|---|---|
| id  | string  | Unique id of the product  | **yes**  | -  |
| family  | string   | Family of products. Will let you work with several kind of purchasables  | **yes**  |   |
| ean  | string   | Product EAN. Is not considered unique, even if should be. Some purchasables may have not EAN  | **yes**  |   |
| name  | string  | Name of the product  | **yes**  |   |
| description  | string  | Product's description  | **yes**  |   |
| long_description  | string  | Product's long description. By default, main description is used  | no  |   |
| price  | float   | Main product's price, without possible discount  | **yes**  | -  |
| reduced_price  | float  | Reduced product's price  | no  | -  |
| stock  | int  | Product stock. By default is considered infinite  | no  |   |
| manufacturer  | Manufacturer  | Product's manufacturer  | no  |   |
| brand  | Brand  | Product's brand  | no  |   |
| image  | string | Image for the product  | no  | -  |
| rating  | string  | Product's rate in a float scale  | no  |   |
| updated_at  | DateTime  | Last time when the product was updated  | no  |   |

You can create a new Product instance by using the simple Product's constructor.
This is an example of how you can create a product with random data.

``` php
$product = new Product(
    '12345',
    'pack',
    '4738947832',
    'Christmas pack',
    'This is the great Christmas pack',
    null,
    40,50,
    35,90,
    100,
    $myManufacturer,
    null,
    'http://example.com/image/12345.jpg',
    4.5,
    new DateTime()
)
```

You can build a product as well by using the Product's static factory method
named `createFromArray`.

``` php
$array = new [
    'id' => '12345',
    'family' => 'pack',
    'ean' => '4738947832',
    'name' => 'Christmas pack',
    'description' => 'This is the great Christmas pack',
    'price' => 40,50,
    'reduced_price' => 35,90,
    'stock' => 100,
    'manufacturer' => $myManufacturer,
    'image' => 'http://example.com/image/12345.jpg',
    'rating' => 4.5,
    'updated_at' => new DateTime()
];
$product = Product::createFromArray($array);
```

Both ways will cause the same result.
A product can have categories and tags as well. Because the relation with both
are of many to many, we will use the add methods to make it work.

``` php
$product->addCategory($category1);
$product->addCategory($category2);
$product->addTag($tag1);
$product->addTag($tag2);
```

The product object have some extra methods a part of the properties getters.
Some of them are internal operations, mainly related to the pricing part.

``` php
$product->getRealPrice();
```

The real price will return the minimum price between the price and the reduced
price. If the reduced price is null, then the price is considered as the real
price.

``` php
$product->getDiscount();
$product->getDiscountPercentage();
```

The discount applied between the price and the real price. If both are different
then the difference will be considered as the discount. The percentage applied
between the price and the real price is the value returned when the method
`getDiscountPercentage` is called. This last value will always be between 0 and 100.

### Category

The way we have to categorize what a purchasable is. The category model is
configured always by levels. This means that even if they are not related
between them, they are organized by levels.

| Field  | Type  | What is that  | Mandatory?  | Boost  |
|---|---|---|---|---|
| id  | string  | Unique id of the category  | **yes**  | -  |
| name  | string  | Name of the category  | **yes**  |   |
| level  | string  | Category level. By default 1  | no  |   |

You can create a new Category instance by using the simple Category's constructor.
This is an example of how you can create a category with random data.

``` php
$category = new Category(
    '12345',
    'Shoes',
    2
)
```

You can build a category as well by using the Category's static factory method
named `createFromArray`.

``` php
$array = new [
    'id' => '12345',
    'name' => 'Shoes',
    'level' => 2
];
$category = Category::createFromArray($array);
```

Given an array of arrays defining several product categories, you can still use
the Product's `createFromArray` method to hydrate Categories inside a Product.

``` php
$array = new [
    'id' => '12345',
    'family' => 'pack',
    // ...
    'categories' => [
        [
            'id' => '12345',
            'name' => 'Shoes',
            'level' => 2
        ],
        [
            'id' => '67890',
            'name' => 'Kids Shoes',
            'level' => 3
        ]
    ]
];
$product = Product::createFromArray($array);
```

### Manufacturer & Brand

Both entities are explained the same way because both entities are built with
the same basic architecture.

| Field  | Type  | What is that  | Mandatory?  | Boost  |
|---|---|---|---|---|
| id  | string  | Unique id of the manufacturer/brand  | **yes**  | -  |
| name  | string  | Name of the manufacturer/brand  | **yes**  |   |

Both entities can be built, as well, using the constructor

``` php
$manufacturer = new Manufacturer(
    '12345',
    'Adidas'
)
```

or by using the static factory method `createFromArray`

``` php
$brand = Brand::createFromArray([
    'id' => '12345',
    'name' => 'Nestle'
])
```

And the usage inside a Product defined as an array is exactly the same than the
array of categories

``` php
$array = new [
    'id' => '12345',
    'family' => 'pack',
    // ...
    'manufacturer' => [
        'id' => '12345',
        'name' => 'Adidas',
    ],
    'brand' => [
        'id' => '67890',
        'name' => 'Nestle',
    ]
];
$product = Product::createFromArray($array);
```

### Tags

A even simpler entity, so a Tag can have only a name. This name will work as a
unique id, so make sure that you use it as such.


| Field  | Type  | What is that  | Mandatory?  | Boost  |
|---|---|---|---|---|
| name  | string  | Name of the tag  | **yes**  |   |

Built your tags using, again, both the constructor method

```
$tag = new Tag('heavy');
```

or by using the static factory method `createFromArray` 

```
$tag = Tag::createFromArray([
    'name' => 'heavy'
]);
```

And in product, you can define your tags in the Product's array definition as
well.

``` php
$array = new [
    'id' => '12345',
    'family' => 'pack',
    // ...
    'tags' => [
        ['name' => 'heavy'],
        ['name' => 'crazy'],
    ],
];
$product = Product::createFromArray($array);
```

## Query object

Knowing how our model is defined, let's start by knowing how to make a simple
Query Request.

```
$query = Query::create("something");
```

That simple. This small query will look for all entities in the repository,
not containing the word "something" but just scoring each of the results with
this word and returning them all by scoring, the best the first.

Let's make something a little bit harder. Let's take only the first 100 elements
of the second page (from the result 101 to the 200).

```
$query = Query::create("something", 2, 100);
```

That's it, that easy :)

If you want to match all elements, you can just pass an empty string as the
first parameter.

```
$query = Query::create('');
```

### Filters


### Aggregations


### Sorting

## Response object

## Index API

## Query API