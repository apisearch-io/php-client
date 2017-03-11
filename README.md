# SEARCH PHP library

This library aims to provide a php developer nicely interface to manage all the
processes related to the Search API using basic domain objects.

- [Model objects](#model-objects)
- [Query object](#query-object)
- [Result object](#result-object)
- [Index API](#index-api)
- [Query API](#query-api)
- [Integrations](#integrations)
- [Examples](#examples)

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
| slug  | string  | Slug of the product  | **yes**  |   |
| description  | string  | Product's description  | **yes**  |   |
| long_description  | string  | Product's long description. By default, main description is used  | no  |   |
| price  | float   | Main product's price, without possible discount  | **yes**  | -  |
| reduced_price  | float  | Reduced product's price  | no  | -  |
| currency  | string | Your price currency. Can follow any format  | **yes**  | -  |
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
    'christmas-pack',
    'This is the great Christmas pack',
    null,
    40,50,
    35,90,
    'EUR',
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
    'slug' => 'christmas-pack',
    'description' => 'This is the great Christmas pack',
    'price' => 40,50,
    'reduced_price' => 35,90,
    'currency' => 'EUR',
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
| slug  | string  | Slug of the category  | **yes**  |   |
| level  | string  | Category level. By default 1  | no  |   |

You can create a new Category instance by using the simple Category's constructor.
This is an example of how you can create a category with random data.

``` php
$category = new Category(
    '12345',
    'Shoes',
    'shoes',
    2
)
```

You can build a category as well by using the Category's static factory method
named `createFromArray`.

``` php
$array = new [
    'id' => '12345',
    'name' => 'Shoes',
    'slug' => 'shoes',
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
            'slug' => 'shoes',
            'level' => 2
        ],
        [
            'id' => '67890',
            'name' => 'Kids Shoes',
            'slug' => 'kids-shoes',
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
| slug  | string  | Slug of the manufacturer/brand  | **yes**  |   |

Both entities can be built, as well, using the constructor

``` php
$manufacturer = new Manufacturer(
    '12345',
    'Adidas',
    'adidas'
)
```

or by using the static factory method `createFromArray`

``` php
$brand = Brand::createFromArray([
    'id' => '12345',
    'name' => 'Nestlé',
    'slug' => 'nestle',
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
        'slug' => 'adidas',
    ],
    'brand' => [
        'id' => '67890',
        'name' => 'Nestle',
        'slug' => 'nestle',
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

Once a new Query is created you can start by filtering your results. This
library provides a developer friendly way for defining filters by exposing you a
nice set of public methods.

Before starting by using filters, let's explain what an application type is and
the different values we have.

An **application type** is the way a filter is applied in your data set. For
example, if we want to filter our results by two categories, we want all the
results containing all the categories? We want all results containing at least
one of the defined categories? That's the application type.

Let's see all types

* Filter::MUST_ALL - All results must match all filter elements
* Filter::MUST_ALL_WITH_LEVELS - All results must match all filter elements, but
when aggregating, only facets with the minor level encountered will be shown.
E.j. categories.
* Filter::AT_LEAST_ONE - At least one element must match.

Every time we create a new filter, we must determine the type of this filter
application. Depending on that value, the filter will cause different values and
the resulting aggregation (facet) will change, even on your screen. Let's take a
look at the different filters we can apply.

* Filter by families

Remember that your Product can have a family? This is because your domain
Product can be a set of different purchasable objects, for example products,
variants of these products or products packs. All of them may be purchasable in
your domain model and should be treated like that in this API. Having different
kind of product families is the way you can manage different type of products in
this project.

You can , then, filter your results by using these values.

```php
Query::create('')->filterByFamilies(['products', 'packs']);
```

By default, this filter is defined as *MUST_ALL* but you can change this 
behavior by adding a second method parameter.

```php
Query::create('')->filterByFamilies(
    ['products', 'packs'],
    Filter::AT_LEAST_ONE
);
```

* Filter by Types

This API works with 5 different object types. Products, Categories,
Manufacturers, Brands and Tags. Even if the main object type is the Product, and
when we apply all filters, they all are applied only to Product, we could have
the need of searching across one single type by search string. Lets look for all
manufacturers matching "Adidas".

```php
Query::create('adidas')->filterByTypes(['manufacturer']);
```

That simple. Because all objects have indexed fields you can make searches
across them all, but only single searches. Applying extra filters would cause
empty results.

* Filter by Categories.

Let's start by thinking about our app. There are three ways of filtering by
category, but, and because Category is the only element here that should be
considered as tree-architectured, the most common way of using it is by using
Filter::MUST_ALL_WITH_LEVELS. The application of this filter is exactly the same
that the Filter::MUST_ALL but, when we show the results to the final user, if in
our aggregations we have different categories with different levels, we want
only the categories with lowest level to be shown.

Using it in that way, we create the experience of navigation with levels. Of
course you can change the behavior of the filter by using the second parameter.

```php
Query::create('')->filterByCategories(['Shoes']);
Query::create('')->filterByCategories(
    ['Shoes'],
    Filter::MUST_ALL_WITH_LEVELS
);
```

This method will automatically create an aggregation called categories. Please,
go to the aggregations part to know a little bit about that. You can disable it
by using a third parameter.

```php
Query::create('')->filterByCategories(
    ['Shoes'],
    Filter::MUST_ALL_WITH_LEVELS,
    false
);
```

* Filter by Manufacturers and Brands

Because both experience is the same, let's explain both at the same time. As
said with categories, you can filter by manufacturer and by brand. Because a
product can have only of each one, it doesn't make sense to work with exclusive
filters (MUST_ALL*), so in that case we will treat this filter as the default
behavior: Filter::AT_LEAST_ONE

```php
Query::create('')->filterByManufacturers(['Adidas', 'Nike']);
Query::create('')->filterByBrands(['Nestlé']);
```

In the first case, we will only take products with manufacturer *Adidas* OR
*Nike*. Is important to know that using *AT_LEAST_ONE* filters, the more options
we have, the bigger result set we have.

Of course, again, you can change the behavior of the filter by using a second
parameter, and disable the aggregation generation by using a third parameter.
This works for both filters.

```php
Query::create('')->filterByManufacturers(
    ['Adidas', 'Nike'],
    Filter::MUST_ALL,
    false
);
```

* Filter by Tags

Your products may contain tags. At first sight, tags are simple tags, and when
indexing, they don't have meaning at all by themselves, so they are not grouped
by families and related with others.

When filtering, these relations are made, so changing the way they are related
should not change your dataset at all.

Let's work with the example where we have these tags available in our products.

- Express
- Two hours delivery
- Bio
- Regular delivery
- Vegan

They are tags and we must treat this way, but when filtering we can group them.
As you can see, we can see two different groups here. The first one related to
the shipping and the second to food properties. OK, lets filter by two groups,
and let's name each one of them.

```php
Query::create('')
    ->filterByTags(
        'shipping',
        ['Express', 'Two hours delivery', 'Regular delivery'],
        ['Express']
    )
    ->filterByTags(
        'food_type',
        ['Bio', 'Vegan'],
        ['Vegan']
    );
```

We have created two groups, each one defining the set of tags that compound the
group, and the set of tags that we want to apply (this last one will come from
the browser, are the tags selected by the user). By default, tags are selected
as *Filter::MUST_ALL*. It make sense because a product can have as many tags as
desired.

Across all different group filters, they are applied with an *AND* philosophy.
This means that applying two or more filters, results must satisfy all of them.

Again, you can change the filter behavior by using a fourth parameter. Defining
a tag group filter, you are creating as well an aggregation for each one of
them, by you can still disable it by using a fifth parameter.

```php
Query::create('')
    ->filterByTags(
        'shipping',
        ['Express', 'Two hours delivery', 'Regular delivery'],
        ['Express'],
        Filter::AT_LEAST_ONE,
        false
    );
```

* Filter by range

This filter is considerably useful when filtering by price, by rating or by any
other numeric value (discount percentage...). Let's work with the example of
price.

Let's consider that we want all products from 50 to 60 euros, and the products
from 90 to 100 euros. Let's build the filter.

```php
Query::create('')
    ->filterByRange(
        'price',
        'real_price',
        [],
        ['50..60', '90..100']
    );
```

Let's analyze what we created here. First of all, the name of the filter.
Because this is an open filter, we must define the filter field by hand. In our
case the range will be applied over the `real_price` field, but could be applied
over the `price` or the `price_discount` as well.

The third option is for faceting, we will check it later.
The fourth option is the important one. Is an array of ranges, and each range is
defined that way, separated by the string `..`.

By default, a range is defined as Filter::AT_LEAST_ONE, so in that case, each
option adds results to the final set. We can change the behavior by changing the
fifth parameter, and we can disable the auto-generated aggregation by changing
the sixth one.

```php
Query::create('')
    ->filterByRange(
        'price',
        'real_price',
        [],
        ['50..60', '90..100'],
        FILTER::MUST_ALL,
        false
    );
```

As you can see, this last example would return an empty set of elements as we
don't have any product with a price lower than 60 euros and, at the same time,
higher than 90. Basics of logic of sets.

* Filter by price range

This is an implementation of the last filter type. It is applied over the 
Product field `real_price`. It works the same way that the last filter, but the
first two fields are omitted. Optional parameters can be defined as well.

```php
Query::create('')
    ->filterByPriceRange(
        [],
        ['50..60', '90..100']
    );
```

### Aggregations

Once we have applied our filters, part of the result set is what we call
aggregations. This concept is usually understood as well as facets and is the
part of your application where filters are dynamically generated by using the
total number of results in the data set.

For example, if we can filter by the manufacturer 'Nike', but with the current
set of filters, there is not Nike elements available, Nike should'nt be
available. Otherwise, if it is, then we should have the capability of showing
the final user the real number of Nike elements available.

This is whan we call aggregations.

Each filter applied creates, unless you say otherwise, an aggregation group with
all available options for this filter. If you filter by the manufacturer Nike,
your result will come with a group called *manufacturers* and with all
other manufacturers available to be filtered, each one with the elements total
in your database.

The aggregation object will be explained under the Result object properly.

### Sorting

You can sort your results, of course. The Query object provides one method for
this, and the SortBy object defines a prebuilt set of sorting types ready to be
used by you. You can define the sorting field and the type by yourself.

```php
Query::create('')->sortBy(['manufacturer.name', 'asc']);
Query::create('')->sortBy(['name', 'desc']);
Query::create('')->sortBy(['updated_at', 'desc']);
```

We can use prebuilt sorts. The first one is the one applied by default when no
sorting is defined. The better score given a query, the earlier in results.
This is the list of all of them.

```php
Query::create('')
    ->sortBy(SortBy::SCORE)
    ->sortBy(SortBy::PRICE_ASC)
    ->sortBy(SortBy::PRICE_DESC)
    ->sortBy(SortBy::DISCOUNT_ASC)
    ->sortBy(SortBy::DISCOUNT_DESC)
    ->sortBy(SortBy::DISCOUNT_PERCENTAGE_ASC)
    ->sortBy(SortBy::DISCOUNT_PERCENTAGE_DESC)
    ->sortBy(SortBy::UPDATED_AT_ASC)
    ->sortBy(SortBy::UPDATED_AT_DESC)
    ->sortBy(SortBy::MANUFACTURER_ASC)
    ->sortBy(SortBy::MANUFACTURER_DESC)
    ->sortBy(SortBy::BRAND_ASC)
    ->sortBy(SortBy::BRAND_DESC)
    ->sortBy(SortBy::RATING_ASC)
    ->sortBy(SortBy::RATING_DESC)
;
```

When you define a sort element, you override the existing one.

## Result object

A Query instance creates a Result instance. A Result is not only a set of basic
elements from our dataset (Product, Category...) but as well a set of
aggregations, result of our filters.

```php
$result = $repository->query($query);
```

One one hand, you can retrieve all the types produced by the query. Remember
that you have 5 types of elements (Products, Categories, Manufacturers, Brands
and Tags). The last four will be empty if at least one filter is applied (you
can have results if only a query string is applied), so the most important
element here is the first one.

```php
$products = $result->getproducts();
$categories = $result->getCategories();
$manufacturers = $result->getManufacturers();
$brands = $result->getBrands();
$tags = $result->getTags();
```

Each of these methods will return an array of hydrated instances of our model (
not yours. If you want your model instances, you need to create manual
transformers, but in that case it won't be probably necessary, so even if are
not your model instances, you have enough information to build urls).

The Result instance have some other interesting methods to retrieve some extra
information of your dataset.

- getTotalElements() - get the total elements in your dataset, including all
types
- getTotalProducts() - get the total products in your dataset
- getTotalHits() - get the total hits produced by your query
- getMinPrice() - get the minimum price of your query
- getMaxPrice() - get the maximum price of your query

### Result Aggregations

The other important part of the Result object is the Aggregation set. In order
to iterate over all Aggregations you can make a simple foreach over the result
of the `->getAggregations()` result (returns an implementation of the PHP
interface Traversable). You can access directly to an aggregation by using the
`->getAggregation($name)` method and the aggregation assigned name.

### Result Aggregation

Let's analyze what a result Aggregation instance is and how useful can be in our
filtering application.

- ->getName() - The name of the aggregation. For example, when we create the
manufacturer filter, we create a new aggregation called manufacturers. This is
the used name.
- ->getCounters() - This method return an array of Counter instances. Explained
later, but as a TL;DR, this is an object where each option returned by the
aggregation (in the last example, each manufacturer available for filtering) has
the information like totals.
- ->isFilter() - The applied filter application type is an exclusive one, like
MUST_ALL*
- ->hasLevels() - The applied filter application type is a leveled one, like
MUST_ALL_WITH_LEVELS
- ->getTotalElements - Total elements of the aggregation.
- ->getActiveElements - Array of all the elements active (all elements passed
through the filter). Each active element is defined as a counter as well.
- ->sortByName() - Sort all counters by its name. This method has only internal
effects and no result is provided

### Aggregation Counter

Each aggregation, mainly, is composed by a name and a set of counters. Each one
has these methods.

- ->getId() - Id of the counter
- ->getName() - Name of the counter. You might want to print this value after a
possible translation (for example for tags).
- ->getLevel() - Useful for leveled filters. In that case, each counter (for
example, each returned available category will have the level). By default, 1.
- ->isUsed() - Does this counter belongs to an active element?
- ->getN() - Number of results in your database having this element (for
example, number of products with the category Adidas). This is the number
commonly printed in your app.

```
[x] Rebook (73)
[x] Nike (12)
[ ] Adidas (34)
```

This is all you need to know about the Result objects. This objects architecture
will allow you to print all the final information for your final user.

## Index API

Let's dig into the first available API, the index one.

This API aims to let you add your entities in the database. Of course, to
understand how easy is to do that, first of all you need to understand how the
model is modeled (the first chapter of this documentation is about that).

Both the index API and the query API are managed by a single Repository called
HttpRepository. Lets check all available methods this repository provides to
make the Index API possible.

```php
$repository->setKey(string $key);
```

The HttpRepository works with an API on the cloud. To make it work, you need to
specify the API key you want to work with. With this method you will be able to
add it.

```php
$repository->reset();
```

Reset and prepare your database. Think that this API should work as a backup,
never as a main database. The persistence is not important here, so your
database should be able to be restored once and again, and the result would be
the same.

This method is the one you must call before loading fixtures and before indexing
any object. Otherwise, your database will not be created yet, and your queries
won't work.

```php
$repository->addProduct(Product $product);
```

Adds a new product in the flushable bucket. To understand this it is important
to understand that at the moment you add a new type through the index API, for
example addProduct(), nothing happens indeed. Until you explicitly flush all
changes nothing will happen.

Once you add a new product, internally the api will look for related categories,
manufacturers, brands and tags, and per each one, it will add them all one by
one. This means that if you have a product with relations inside, you don't need
to add them all one by one. This action is done by default.

Of course, if you have isolated elements for updating, for example a
Manufacturer with an existing ID but a different name, and you don't have any
product that reflects that, don't hesitate to add it by hand.

```php
$repository->addCategory(Category $category)
$repository->addManufacturer(Manufacturer $manufacturer)
$repository->addBrand(Brand $brand)
$repository->addTag(Tag $tag)
```

Once all your model is inserted, or all your desired instances are now under the
API control, it's time to flush.

```php
$repository->flush(int $bulkNumber);
```

In order to make small queries to the API (imagine a query with 100K elements at
the same time...), there is a parameter called $bulkNumber. The default value
for this element is 500.

What does this number means? Well, that simple. It will make packages of 500
products for sending through the Http API. Because we consider that the number
of products is the one that can be really high, only products are sliced into n
blocks. At the first API call, all other minor elements (Categories,
Manufacturers, Brands and Tags) will be sent.

If the number of elements in the repository is smalled than the defined as bulk,
then only one query will be executed.

## Query API

The second API is the query one. This is mainly one method that will allow you
to make queries against your database.

```php
$result = $repository->query(Query $query) : Result
```

That's it. The result of the query method is a Result instance. To know a little
bit more about this object, check the documentation chapter.

## Integrations

This PHP library has these Framework integrations

[Symfony Bundle](https://github.com/puntmig/search-bundle)

With this bundle you will be able to integrate with your Symfony applications in
a very easy an intuitive way