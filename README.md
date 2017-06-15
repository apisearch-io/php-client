# Search PHP library

> This repository is part of the Search API of the suite Indesky. To get
> more information about this library integrations, some examples and all the
> suite APIs, please visit {{ website }}.

This library aims to provide a php developer nicely interface to manage all the
processes related to the Search API using basic domain objects.

- [Model objects](#model-objects)
- [Query object](#query-object)
- [Result object](#result-object)
- [Index API](#index-api)
- [Query API](#query-api)
- [Delete API](#delete-api)
- [Integrations](#integrations)
- [Examples](#examples)

## Model objects

The library provides you a set of model objects. Both the Index and the Query
Apis will understand these objects and will work with them in both sides (the
client and the server side), so make sure you know how these objects are built.

### Item

A simple view of what a item object is. A item can be anything you
want, but take in account that this is the object in the middle, and the main
Query api is going to work mainly against it.

| Field  | Type  | What is that  | Mandatory?  |
|---|---|---|---|
| id  | string  | Unique id of the item  | **yes**  |
| type  | string   | Type of types  | **yes**  |
| metadata | array | Saved but not indexed item metadata | no |
| indexed_metadata | array | This data will be saved and indexed for filtering | no |
| searchable_metadata | string[] | Your item will be searchable by this data with a transformation. See searchable transformation chapter | no |
| exact_matching_metadata | string[] | Your item will be searchable by this data without transformation | no |
| suggest | string[] | This item will suggest these strings when this option is enabled | no |
| coordinate  | Coordinate  | Assign a coordinate to your item in order to filter and facet over location | no  |

Item's constructor is a private method, so in order to create a new Item
instance youb can use any of the available static constructor methods.
This is an example of how you can create a non located item with random data.
As you can see, both id and type fields are part of a value object called
ItemUUID.

``` php
$itemUUID = new ItemUUID('12345', 'product');
$item = Item::create($itemUUID);
```

If you want to create a located Item, then you can use the static construct
method `createLocated`.

``` php
$itemUUID = new ItemUUID('12345', 'product');
$itemCoordinate = new Coordinate(
    40.12, 
    -71.34
);
$item = Item::createLocated(
    $itemUUID,
    $itemCoordinate
);
```

By default, an empty Item is created. You can set any of the desired metadata
values and suggest data. This example works as well with `createLocated` method.

``` php
$itemUUID = new ItemUUID('12345', 'product');
$item = Item::create(
    $itemUUID,
    [], // Metadata
    [], // Indexed Metadata
    [], // Searchable Metadata
    [], // Exact Matching Metadata
    []  // Suggest elements
);
```

After the creation of an Item instance, or even after its retrieval from the
repository, you can manage all metadata items by using the getters and the
setters of the class.

```php
$metadata = $item->getMetadata();
$metadata['something'] = 'value';
$item->setMetadata($metadata);
$item->addMetadata('another_thing', 'another_value');
```

This works with any of the metadata arrays.

In order to provide an object as much resistant as possible in front of changes,
you can consider the metadata package as a unique package, even if internally
you have divided it in four different arrays. For example, if you have a field
called *price* and at the beginning of your project this value is not going to
be indexed, then you should store it inside metadata. Then, in your project, you
will access to this value by using the `getMetadata` getter, and accessing to
the desired position.

```php
$item->getMetadata()['price'];
```

But what happens if your price needs to be indexed? Then you should change your
indexing point, and instead of placing the element as a simple metadata, you
should place it as an indexed metadata. So, what happens with all the code
points where you've been requiring the price value? You should change it
well, right?

```php
$item->getIndexedMetadata()['price'];
```

Well, this would be something that may cause you too many code changes, where
should be something insignificant.

In order to avoid this, you should take some decisions in your model.

* When you index metadata in your Item, even if you place them in different
metadata packages, don't repeat metadata field names.
* When you request a metadata value, use the `->get($fieldName)` method. This
will return the metadata value accessing all metadata packages at the same time.

```php
$item->get('price');
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

``` php
$query = Query::create("something", 2, 100);
```

That's it, that easy :)

If you want to match all elements, you can just pass an empty string as the
first parameter or use the search-everything static factory method. In this
second method you will query the first 1000 elements.

``` php
$query = Query::create('');
$query = Query::createMatchAll();
```

Finally, you can create a query to find one ore more specific elements from your
database. For this reason, there are two special static factory methods
specifically create to make these two scenarios so easy.

We will use UUIDs here in both cases.

``` php
$query = Query::createByUUIDs(new ItemUUID('12', 'book'));
$query = Query::createByReferences([
    new ItemUUID('12', 'book'),
    new ItemUUID('123', 'book'),
    new ItemUUID('332', 'book'),
    new ItemUUID('555', 'book'),
    new ItemUUID('heavy', 'book'),
]);
```

The order is not important here, and the result format will be exactly the same
than any other type of queries.

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

#### Filter by

Generic filter action.

```php
Query::create('')
    ->filterBy(
        'uuid.id',
        ['1', '2', '3'],
        Filter::AT_LEAST_ONE
    );
```

Common filters are already wrapped by specific methods.

#### Filter by meta

Remember that a item can have some indexed metadata? This metadata is stored
and indexed properly so you can filter by these values using this method.

```php
Query::create('')
    ->filterByMeta(
        'field1',
        ['value1', 'value2']
    );
```

By default, this filter is defined as *AT_LEAST_ONE* but you can change this 
behavior by adding a third method parameter.

```php
Query::create('')
    ->filterByMeta(
        'field1',
        ['value1', 'value2'],
        Filter::MUST_ALL
    );
```

> This filter works with the indexed_metadata field. Remember that the metadata
> field stores non-indexable data

By default, when you filter by meta, specific metadata field aggregation will be
enabled. Disable this aggregation by adding a fourth and last parameter.

```php
Query::create('')
    ->filterByMeta(
        'field1',
        ['value1', 'value2'],
        Filter::AT_LEAST_ONE,
        false
    );
```

#### Filter by Types

You can filter by item types. Because the type value is part of the UUID, and
considering that this value will define your different type of entities inside
a unique repository, this filter will allow you to work with one or several
object types.

```php
Query::create('quijote')
    ->filterByTypes(
        ['book']
    );
```

#### Filter by range

This filter is considerably useful when filtering by price, by rating or by any
other numeric value (discount percentage...). Let's work with the example of
price.

Let's consider that we want all items with a price value from 50 to 60, and 
from 90 to 100 euros. Let's consider as well that this price value is part of
the indexed metadata. Let's build the filter.

```php
Query::create('')
    ->filterByRange(
        'price',
        'price',
        [],
        ['50..60', '90..100']
    );
```

Let's analyze what we created here. First of all, the name of the filter.
Because this is an open filter, we must define the filter field by hand. In our
case the range will be applied over the `price` field, but could be applied
over the `real_price` field, after some discount appliance, or the 
`price_discount` as well.

This will allow you to define several range filters over the same field.

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
don't have any item with a price lower than 60 euros and, at the same time,
higher than 90. Basics of logic of sets.

#### Filter by location

You can filter your results by location as well. Some apps should be able to
show only the nearest items given a location coordinate, or the items
located inside an specific zone.

To start using this feature, we must understand what a Coordinate is. Given a
latitude, specified by a float value, and a longitude, specified as well as a
simple float value, we can create a new Coordinate instance. This object will be
important in this filter feature.

```php
$coordinate = new Coordinate(40.9, -70.0);
```

We can filter our queries in three different ways.

##### Filter by location, given a center point and a distance

Given a center point, defined as a Coordinate instance, and a distance, defined
as an integer and a distance unit (km or mi) joined in a string, you can define
a simple filtering range. You must use an object called `CoordinateAndDistance`.

```php
$locationRange = new CoordinateAndDistance(
    new Coordinate(40.9, -70.0),
    '50km'
);

Query::create('')
    ->filterByLocation(
        $locationRange
    );
```

> This is useful when using, for example, a website with active localization.
> The browser can request the localization and send the coordinates to us, so we
> can provide a better experience to the final user

##### Filter by location, given two square sides

If you have the top-left coordinate and the bottom-right coordinate of a square,
inside of where you want to locate all the items, you can use this filter
type. In that case, you need both Coordinate instances.

```php
$locationRange = new Square(
    new Coordinate(40.9, -70.0),
    new Coordinate(39.4, -69.1),
);

Query::create('')
    ->filterByLocation(
        $locationRange
    );
```

> This is useful when working with maps. Maps are usually presented in a square
> visualization mode, so when the final user scrolls, having these two
> coordinates (top-left, bottom-right) we can look the items we want to show

##### Filter by location, given a finite set of coordinates (polygon)

You can build your own polygon having a set of coordinates. These coordinates
will draw a polygon, and all items inside the are of this polygon will be
considered as valid result.

All coordinates must be Coordinate instances.

```php
$locationRange = new Polygon(
    new Coordinate(40.9, -70.0),
    new Coordinate(40.9, -69.1),
    new Coordinate(39.4, -69.1),
    //...
);

Query::create('')
    ->filterByLocation(
        $locationRange
    );
```

You can add as many coordinates as you need in order to build the desired area.

> This is useful when the final user has any kind of drawing tool, so an
> specific polygon can be defined by any user. Useful as well when composing
> maps, for example, defining country areas as polygons.

### Aggregations

Once we have applied our filters, part of the result set is what we call
aggregations. This concept is usually understood as well as facets and is the
part of your application where filters are dynamically generated by using the
total number of results in the data set.

For example, if we can filter by the item's manufacturer 'Nike', but with the 
current set of filters, there is not elements manufactured by Nike available, 
Nike should'nt be available. Otherwise, if it is, then we should have the 
capability of showing the final user the real number of Nike elements available.

This is what we call aggregations.

Each filter applied creates, unless you say otherwise, an aggregation group with
all available options for this filter. If you filter by the item Nike,
your result will come with a group called *manufacturers* and with all
other manufacturers available to be filtered, each one with the elements total
in your database.

The aggregation object will be explained under the Result object properly.

### Sorting

You can sort your results, of course. The Query object provides one method for
this, and the SortBy object defines a prebuilt set of sorting types ready to be
used by you. You can define the sorting field and the type by yourself.

```php
Query::create('')
    ->sortBy(
        ['indexed_metadata.manufacturer', 'asc']
    );

Query::create('')
    ->sortBy(
        ['indexed_metadata.name', 'desc']
    );

Query::create('')
    ->sortBy(
        ['indexed_metadata.updated_at', 'desc']
    );
```

We can use prebuilt sorts. The first one is the one applied by default when no
sorting is defined. The better score given a query, the earlier in results.
This is the list of all of them.

```php
Query::create('')
    ->sortBy(SortBy::SCORE)
    ->sortBy(SortBy::ID_ASC)
    ->sortBy(SortBy::ID_DESC)
    ->sortBy(SortBy::TYPE_ASC)
    ->sortBy(SortBy::TYPE_DESC)
;
```

When you define a sort element, you override the existing one.

#### Sorting by location

A set of special sorting types can sort as well by location. In order to make
this sorting work, we must create our Query instance by using the method
`createLocated()` instead of `create()`. The only difference between both is
that the first one's first parameter is a `Coordinate` instance.

```php
$query = Query::createLocated(
    new Coordinate(40.0, -70.0),
    ''
);
```

Because the only way that could make sense when sorting by location is
requesting first of all the elements closer to us, we can only sort them by
location in an *asc* mode.

```php
Query::create('')
    ->sortBy(SortBy::LOCATION_KM_ASC)
    ->sortBy(SortBy::LOCATION_MI_ASC)
;
```

Both sorting types return exactly the same results in the same order, but both
return the distance of each hit in different units. The first of all in 
kilometers and the second one in miles.

Using this sort type, we will be able to know the distance of each of the
Product instances received by using the special Product method `->getDistance()`
defined and filled only in this scenario. The result of this method is a float
value.

```php
$item->getDistance();
```

### Enabling / disabling suggestions

Suggestions can be enabled or disabled by using these flag methods.

```php
Query::create('')
    ->enableSuggestions()
    ->disableSuggestions()
;
```

### Enabling / disabling aggregations

Aggregations can be enabled or disabled by using these flag methods. This flag
will override all behaviors from all filter methods (remember that when
filtering by some fields, for example Categories, you can enabled or disable
specific aggregation). If aggregations are enabled, then the behavior will not
change and each field specific behavior will be used. If disable, all field
specific behaviors will be disabled.

```php
Query::create('')
    ->enableAggregations()
    ->disableAggregations()
;
```

In this case, aggregations are specifically enabled by Categories, but disabled
by flag, so no aggregations will be requested.

```php
Query::create('')
    ->filterByTypes(
        ['product'],
        true
    )
    ->disabledAggregations()
;
```

### Excluding some elements

Having some kind of black list would be useful as well. For example, when
printing a related carousel given an item, and filtering by the type,
would be useful to exclude the current element from the list.

In order to do this, we will use UUIDs, so we can filter by any kind of
element only having the UUID.

```php
Query::create('')
    ->filterByTypes(
        ['product']
    )
    ->excludeUUID(new ItemUUID('10', 'product'))
;
```

In this example we are excluding the Item with ID 10 and 'product' as type.
Remember that an item is always referenced not only by the id but with a
composition between the ID and the type.

We can filter by several UUIDs as well.

```php
Query::create('')
    ->filterByTypes(
        ['product']
    )
    ->excludeReferences([
        new ItemUUID('10', 'product'),
        new ItemUUID('5', 'product'),
        new ItemUUID('100', 'product'),
        new ItemUUID('21', 'product'),
    ])
;
```

## Result object

A Query instance creates a Result instance. A Result is not only a set of basic
elements from our dataset (Product, Category...) but as well a set of
aggregations, result of our filters.

```php
$result = $repository->query($query);
```

You can retrieve as well all elements in a single array, respecting the order
defined by the Query.

```php
$results = $result->getItems();
```

If you queried by a UUID, for example, or even in any query, you can easily
retrieve the first (and more useful when is the only expected) result by using
this method

```php
$results = $result->getFirstItem();
```

Each of these methods will return an array of hydrated instances of our model (
not yours. If you want your model instances, you need to create manual
transformers.

The Result instance have some other interesting methods to retrieve some extra
information of your data set.

- getTotalElements() - get the total items in your data set
- getTotalHits() - get the total hits produced by your query

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
example, number of item with the category Adidas). This is the number
commonly printed in your app.

```
[x] Rebook (73)
[x] Nike (12)
[ ] Adidas (34)
```

This is all you need to know about the Result objects. This objects architecture
will allow you to print all the final information for your final user.

### Suggests

If your query had the suggests enabled, then you will find some suggestions in
your Result instance by using the getter method.

```php
$suggests = $result->getSuggests();
```

Each suggest is defined as an array of non unique strings.

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
$repository->addItem(Item $item);
```

Adds a new item in the bucket. To understand this it is important
to understand that at the moment you add a new type through the index API, for
example addItem(), nothing happens indeed. Until you explicitly flush all
changes nothing will happen.

Once all your model is inserted, or all your desired instances are now under the
API control, it's time to flush.

```php
$repository->flush(int $bulkNumber);
```

In order to make small queries to the API (imagine a query with 100K elements at
the same time...), there is a parameter called $bulkNumber. The default value
for this element is 500.

What does this number means? Well, that simple. It will make packages of 500
items for sending through the Http API. If the number of elements in the 
repository is smalled than the defined as bulk, then only one query will be 
executed.

## Query API

The second API is the query one. This is mainly one method that will allow you
to make queries against your database.

```php
$result = $repository->query(Query $query) : Result
```

That's it. The result of the query method is a Result instance. To know a little
bit more about this object, check the documentation chapter.

## Delete API

In order to work with the Delete API we need to work with the same Repository
than used in the Index API. A key must be defined the same way we did before.

```php
$repository->setKey(string $key);
```

### Delete an Item

A item is referenced by its ID and type.

```php
$book = new ItemUUID('10', 'book');
```

And to delete the item

```php
$repository->deleteItem(ItemUUID $book);
```

In order to flush all changes, remember to use the flush method.

```php
$repository->flush();
```

> The bulkNumber value will not affect when flushing deletions

## Integrations

This PHP library has these Framework integrations

[Symfony Bundle](https://github.com/puntmig/search-bundle)

With this bundle you will be able to integrate with your Symfony applications in
a very easy an intuitive way