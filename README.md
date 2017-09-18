# Search PHP library

> This repository is part of the ApiSearch project. To get more information
> about it, please visit http://apisearch.io. This a project created with love
> by [Puntmig Development SLU](http://puntmig.com)

This library aims to provide to any php developer nicely interfaces to manage
all processes related to Apisearch, using basic >=PHP7.1 classes.

- [Model](#model)
    - [Item](#item)
    - [ItemUUID](#itemUUID)
    - [Building an Item](#building-an-item)
    - [Coordinate](#coordinate)
    - [Located Item](#located-item)
    - [Item manipulation](#item-manipulation)
    - [Location Ranges](#location-ranges)
- [Query](#query-model)
    - [Building a Query](#building-a-query)
    - [Filters](#filters)
    - [Filter types](#filter-types)
    - [Filtering by type](#filtering-by-type)
    - [Filtering by id](#filtering-by-id)
    - [Filtering by location](#filtering-by-location)
    - [Filtering by range](#filtering-by-range)
    - [Filtering by field](#filtering-by-field)
    - [Aggregations](#query-aggregations)
    - [Sort by field](#sort-by-field)
    - [Sort by location](#sort-by-location)
    - [Sort randomly](#sort-randomly)
    - [Enabling Suggestions](#enabling-suggestions)
    - [Excluding some items](#excluding-some-items)
    - [Enabling highlighting](#enabling-highlighting)
- [Result](#result)
    - [Reading Aggregations](#result-aggregations)
    - [Aggregation counter](#aggregation-counter)
    - [Reading Suggestions](#reading-suggestions)
- [Event](#event)
    - [Event object](#event-object)
- [Repository](#repository)
    - [HttpRepository](#http-repository)
    - [TransformableReposittory](#transformable-reposittory)
    - [InMemoryRepository](#in-memory-repository)
    - [Reset](#reset)
    - [Index](#index)
    - [Delete](#delete)
    - [Flush](#flush)
    - [Query](#query-repository)
- [EventRepository](#event-repository)
    - [Event](#event)
- [Special scenarios](#special-scenarios)
    - [Many-to-many relations](#many-to-many-relations)
- [Integrations](#integrations)

## Model

The library provides you a set of model objects. All repositories will work
using them, so please, be sure you understand every part of the model before any
integration.

### Item

The Item is the base class for this package. An Item instance represents a
single row in your read-only model, and can be mapped with any class of your own
model.

Because this platform allows you to integrate any kind of object with this Item
object, the internals of the objects are as simple as we could do, in order to
provide you as much flexibility as we could.

Lets take a look at what Items is composed by.

* id - A string representation of the id of the Item. This id is not required to
be unique in your model universe, but is required to be unique along all 
entities of the same type (for example, along products, this id should be
unique). This parameter is required and cannot be null.
* type - Because an Item can be mapped by any entity from your model, this
parameter defined what entity has been mapped. This is parameter is required and
cannot be null.
* metadata - An array of data-values. This data will be not processed nor 
indexed, and will only be accessible once returned results. Values for this
array can have any format. By default, an empty array is used.
* indexed_metadata - An array of indexed, filterable and aggregable data-values.
This data will not be searchable at all. By default, an empty array is used.
* searchable_metadata - An array of strings used for searching. Each string will
be decomposed by the engine and used for searching the item. By default, an 
empty array is used.
* exact_matching_metadata - An array of strings used for searching. Each string
will not be decomposed and will be used as it is introduced. Current item will
be returned as result only if the query string contains one or many introduced
values. By default, an empty array is used.
* suggest - An array of strings where each item can propose suggestions for
searching time. Strings wont be decomposed neither. By default, an empty array 
is used.
* Coordinate - An Item can be geolocated in space, so an instance of Coordinate
can be injected here. This value is not required.

Let's see an example of an item.

``` yml
id: 4303ui203
type: product
metadata:
    name: "T-shirt blue and red"
    description: "This is an amazing T-shirt"
    ean: 7827298738293
indexed_metadata:
    sizes:
        - M
        - L
        - XL
    colors:
        - Blue
        - Red
    price: 10
    old_price: 15
    brand: Supershirts
    created_at: now()
searchable_metadata:
    name: T-shirt blue and red
    description: This is an amazing T-shirt
    brand: Supershirts
exact_matching_metadata:
    - 4303ui203
    - 7827298738293
suggest:
    - T-shirt
```

Let's explain a little better this example

* Our product with ID 4303ui203 is mapped as an Item
* We have a name, a description and an EAN stored in the item, and because is 
not filterable not aggregable by these values, we place them in the metadata
array.
* We have other values like sizes, colors, price, old_price and brand prepared
to be filtered and aggregated by. These values will be accessible as well when
Items are provided as results.
* When final user searches on our website, this Item will be part of the result
if the search contains any of the words included as searchable_metadata (
after some transformations, will see later), so when searching by *amazing*,
this item will be a result. If searching by *Elephant*, will not.
* If the final user searches exactly by *4303ui203* or *7827298738293*, this
Item will be part of the result as well.
* If you have suggestions enabled, and if the final user start searches by
string *T-shi*, this item will add a suggestion of *T-shirt*. This is completely
different from the search fields.

Before start building our first Item, let's see another object we need to know
in order to use the factory methods inside Item object.

### ItemUUID

Remember that we said that the id field in Item only is unique in the universe
of the entities with same type? Then, we need a representation of this Unique
id.

And this representation is the object ItemUUID. A simple class that contains an
id and a type. Let's see how to build one of these.

```php
$itemUUID = new ItemUUID('4303ui203', 'product');
```

This is a real Unique Id representation of our model, and this instance should
be unique in all our universe.

### Building an Item

So let's build our first Item instance. Because an Item can be build by 
different ways, we will use static factories instead of the private 
*__construct* method.

If you remember, all data but id and type is not required, so a simple
implementation of a new Item could be as simple as that.

``` php
$itemUUID = new ItemUUID('4303ui203', 'product');
$item = Item::create($itemUUID);
```

This Item would not have any parameter, and would be equivalent to this piece of
code.

``` php
$itemUUID = new ItemUUID('4303ui203', 'product');
$item = Item::create(
    $itemUUID,
    [], // Metadata
    [], // Indexed Metadata
    [], // Searchable Metadata
    [], // Exact Matching Metadata
    []  // Suggest elements
);
```

Lets add some extra data to have a nice representation of our first example.

``` php
$itemUUID = new ItemUUID('4303ui203', 'product');
$item = Item::create(
    $itemUUID,
    [
        'name' => 'T-shirt blue and red',
        'description' => 'This is an amazing T-shirt'
        'ean' => 7827298738293
    ], 
    [
        'sizes' => [
            'M',
            'L',
            'XL',
        ],
        'colors' => [
            'Blue',
            'Red',
        ],
        'price' => 10,
        'old_price => 15,
        'brand' => 'Supershirts',
        'created_at' => new DateTime(),
    ],
    [
        'name' => 'T-shirt blue and red',
        'description', 'This is an amazing T-shirt',
        'brand', 'Supershirts',
    ],
    [
        '4303ui203',
        '7827298738293'
    ],
    [
        'T-shirt'
    ]
);
```

This Item would map exactly as shown in the first example.

### Coordinate

A simple Coordinate is composed by a latitude and a longitude values. That
simple. Both values are float formatted.

``` php
$itemCoordinate = new Coordinate(
    40.12, 
    -71.34
);
```

### Located Item

If you want to create a located Item, then you can use the static construct
method `createLocated`. Because a located item must have a location, otherwise
this would be a conventional Item, then both an ItemUUID and Coordinate
instances must be passed as parameters.

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

As before, this method allow all other parameters to be defined after the
coordinate.

``` php
$itemUUID = new ItemUUID('12345', 'product');
$itemCoordinate = new Coordinate(
    40.12, 
    -71.34
);
$item = Item::createLocated(
    $itemUUID,
    $itemCoordinate,
    [], // Metadata
    [], // Indexed Metadata
    [], // Searchable Metadata
    [], // Exact Matching Metadata
    []  // Suggest elements
);
```

### Item manipulation

After the creation of an Item instance, or even after its retrieval from the
repository, you can manage all metadata (single metadata and indexed metadata)
values by using the specific getters and setters.

```php
$metadata = $item->getMetadata();
$metadata['something'] = 'value';
$item->setMetadata($metadata);
$item->addMetadata('another_thing', 'another_value');
```

In order to provide an object as much resistant as possible in front of changes,
you can consider all metadata data sets as a unique data set, even if internally
you have divided it in two different arrays. For example, if you have a field
called *price* and at the beginning of your project definition this value is not
going to be indexed, then you should store it inside metadata. Then, in your 
project, if you will access to this value by using the `getMetadata` getter, and
accessing to the desired position.

```php
$item->getMetadata()['price'];
```

But what happens if your price needs to be indexed? Then you should change your
indexing point, and instead of placing the element as a simple metadata, you
should place it as an indexed metadata. So, what happens with all the code
points where you've been requiring the price value? You should change it
well, right?

This will not work anymore

```php
$item->getMetadata()['price'];
```

Instead of that, you'll need to start using this

```php
$item->getIndexedMetadata()['price'];
```

Well, this would be something that may cause you too many code changes, where
should be something insignificant.

In order to avoid this, you should take some decisions in your model.

* Don't repeat keys inside your metadata and indexed_metadata arrays.
* When you request a metadata value, use the `->get($fieldName)` method. This
will return the metadata value accessing all metadata packages at the same time.

In this example, price will be retrieved both from metadata and
indexed_metadata, so even if you change price from one to the other, nothing bad
will happen :)

```php
$item->get('price');
```

### Location Ranges

When talking about located items, and when retrieving and filtering them, we
need to know so well a small part of our model called Location Ranges. They are
related with the Coordinate class, and specifies an area containing many of
them.

There are three type of area definitions.

#### A center point and a distance

Given a center point, defined as a Coordinate instance, and a distance, defined
as an integer and a distance unit (km or mi) joined in a string, you can define
a simple filtering range. You must use an object called `CoordinateAndDistance`.

```php
$locationRange = new CoordinateAndDistance(
    new Coordinate(40.9, -70.0),
    '50km'
);
```

> This is useful when using, for example, a website with active localization.
> The browser can request the localization and send the coordinates to us, so we
> can provide a better experience to the final user

#### Two square sides

If you have the top-left coordinate and the bottom-right coordinate of a square,
inside of where you want to locate all the items, you can use this filter
type. In that case, you need both Coordinate instances.

```php
$locationRange = new Square(
    new Coordinate(40.9, -70.0),
    new Coordinate(39.4, -69.1),
);
```

> This is useful when working with maps. Maps are usually presented in a square
> visualization mode, so when the final user scrolls, having these two
> coordinates (top-left, bottom-right) we can look the items we want to show

#### A finite set of coordinates (polygon)

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
```

You can add as many coordinates as you need in order to build the desired area.

> This is useful when the final user has any kind of drawing tool, so an
> specific polygon can be defined by any user. Useful as well when composing
> maps, for example, defining country areas as polygons.

## Query

Knowing how our model is defined, let's start by knowing how to make a simple
Query Request. Query objects will give us the possibility of communication
between our project and the server by just using some object methods, and by
using a single pattern called builder.

### Building a Query

Let's start with something really easy.

```
$query = Query::create("something");
```

That simple. This small query will look for all entities in the repository,
Scoring each of the results containing the word "something" by hit scoring. The
best the first.

> Sorting by scoring means that, the best appearance the word "something" has 
> inside each result, the better punctuation has.

Let's make something a little bit harder. Let's take only the first 100 elements
of the second page (from the result 101 to the 200). By default, is none of
these last values are defined, you will request the first 10 results.

``` php
$query = Query::create(
    "something", // The query string
    2,           // The page we want to retrieve
    100          // How many items do we want?
);
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

We will use [ItemUUIDs](#itemUUID) here in both cases.

``` php
$query = Query::createByUUID(new ItemUUID('12', 'book'));
$query = Query::createByUUIDs([
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

Let's see all available types

* Filter::MUST_ALL - All results must match all filter elements
* Filter::MUST_ALL_WITH_LEVELS - All results must match all filter elements, but
when aggregating, only facets with the minor level encountered will be shown.
E.g. categories.
* Filter::AT_LEAST_ONE - At least one element must match.
* Filter::EXCLUDE - Items should be excluded from results

Every time we create a new filter, we must determine the type of this filter
application. Depending on that value, the filter will cause different values and
the resulting aggregation (facet) will change, even on your screen. Let's take a
look at the different filters we can apply.

#### Must all with levels

An special explanation of this aggregation type.

Imagine your item is categorized with a tree-like structure.

- A - A1, A2
- B - B1, B2, B3
- C - C1

You item could be related with a first-level category, and with one if its 
subcategories. For example, your Item is categorized as A and A2 at the same
time.In that case you should relate your item with both, but adding an extra
field in your categories called *level*. The level of the category.

When you print these aggregations and you've defined this categorization as 
MUST_ALL_WITH_LEVELS, you will print only these categories with the current
level. So, if you don't have any filter applied, you should be able to filter
only by first level categories.

- [ ] A
- [ ] B
- [ ] C

So what happens when we apply the A filter? Then, and because A has two children
A1 and A2, the aggregations will appear like that.

- [x] A
- [ ] A1
- [ ] A2

### Filter types

We will mainly talk about two different filter types, and it is very important
for you to understand both, why are they important and where to use each one.

First of all, we have something called Universe. We will call Universe to the
total set of Results. No matter the type, no matter the ID. Each Item accessible
by our API is part of our Universe.

In our website, or in our app, inside each landing page or screen we will want
to work with the entire Universe or with a subset of it, so this first step will
require us to use the filterUniverse methods.

``` php
$query = Query::createMatchAll()
    ->filterUniverseByTypes(['A', 'B']);
```

Once our Universe is properly defined, then we have to let the user navigate
through this universe by using the standard filters.

``` php
$query = Query::createMatchAll()
    ->filterUniverseByTypes(['A', 'B'])
    ->filterBy('brand', 'brand', ['Superbrand']);
```

Each filter strategy is documented for both universe and regular filters. As you
will see both methods will always change a little bit (regular filters will
always have a name as first parameter in order to relate later with a possible
aggregation).

### Filtering by Type

So, try to imagine an environment when, even you have types A, B and C, you only
want to work with A and B. In this environment C is not welcomed, and you don't
want C Items to be in any set of results.

Then, all queries inside this environment will need to filter the entire
universe by types A and B. Let's see how to do it.

``` php
$query = Query::createMatchAll()
    ->filterUniverseByTypes(['A', 'B']);
```

All possible results will only include A and B. Think about this filter as a
permanent filter executed before all others.

Then you can use regular Filtering by type by using this method

``` php
$query = Query::createMatchAll()
    ->filterUniverseByTypes(['A', 'B'])
    ->filterByTypes(['A']);
```

But alert ! This seems to be exactly the same, right? Well, in this case we are
filtering by Types A and B, and then by type A, so results would only include A
types. That would be completely equivalent to filter the entire universe once by
type A.

Well, indeed. This would only work if your application has not aggregations nor
any kind of interaction with your user, where can filter manually by clicking
some kind of links.

Once Universe is filtered, and if you aggregate your values (in this case,
types), Results will contain only types A, but aggregations will still contain
all of them that are actually existing in the filtered Universe, so in this case
user would see something like this.

```
[x] Type A
[ ] Type B
```

We could even have something like that

``` php
$query = Query::createMatchAll()
    ->filterUniverseByTypes(['A', 'B'])
    ->filterByTypes(['A', 'B']);
```

With a result like that

```
[x] Type A
[x] Type B
```

While if we have this implementation, ignoring our Universe filter, considering
that our filter is already working properly

``` php
$query = Query::createMatchAll()
    ->filterByTypes(['A', 'B']);
```

Then, our result would be something like that, so our Universe is not filtered
anymore and is composed by the total set of Items, including the C types.

``` php
[x] Type A
[x] Type B
[ ] Type C
```

On the other hand, if we only want the set of results matching your filter types
without the aggregations, we can also set a second boolean parameter to disable 
aggregations (by default is set to `true`).

``` php
$query = Query::createMatchAll()
    ->filterByTypes(
        ['A', 'B']
        false
    );
```

A third and last parameter can be set to sort the aggregations result. By default, 
this parameter is set to *SORT_BY_COUNT_DESC*.

``` php
$query = Query::createMatchAll()
    ->filterByTypes(
        ['A', 'B']
        true,
        Aggregation::SORT_BY_COUNT_ASC
    );
```

### Filtering By Id

You can filter universe as well by ids. In that case, you can image that, no
matter what or how filters you add. Your result set will be of maximum 3 items.

``` php
$query = Query::createMatchAll()
    ->filterUniverseByIds(['10', '11', '12']);
```

This is only useful if you work with a limited set of Items known by Ids.

Of course, filtering by ID is available as well inside your defined universe.
This is useful, for example, if you ID is a human readable value, and you want
to select a set of items from a list.

``` php
$query = Query::createMatchAll()
    ->filterByIds(['10', '11', '12']);
```

### Filter by location

You can filter your universe as well by Location if your Items are Geolocated.
This will allow you to work only with some Items positioned in a certain area.
You can use any of [Location Ranges](#location-ranges) explained previously.

```php
$query = Query::createMatchAll()
    ->filterUniverseByLocation(new CoordinateAndDistance(
        new Coordinate(40.9, -70.0),
        '50km'
    ))
```

Location is something that you should filter by just once. And because you can't
aggregate by locations, it has'nt make sense at all to have both filters,
universe and regular, so they both mean exactly the same.

### Filter by range

You can filter your universe as well by range. Depending if the filter uses a
date range or not, you should use one of these methods. Let's imagine a landing
page where to list all T-shirts with low price (up to 20 euros). We want to add
only elements created during last month

``` php
$from = // Date Atom of start of the month
$to = // Date Atom of the end of the month
$query = Query::createMatchAll()
    ->filterUniverseByRange('price', ['0..20'], Filter::MUST_ALL)
    ->filterUniverseByDateRange('created_at', ["$from..$to"], Filter::MUST_ALL);
```

Furthermore, once defined your subset of available values, you can use the range
filter the same way as others.

This filter is considerably useful when filtering by price, by rating or by any
other numeric value (discount percentage...). Let's work with the example of
price.

Let's consider that we want all items with a price value from 50 to 60, and 
from 90 to 100 euros. Let's consider as well that this price value is part of
the indexed metadata. Let's build the filter.

```php
Query::createMatchAll()
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
Query::createMatchAll()
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

### Filter by field

Finally, and of course, you can filter your universe by any value inserted in
your indexed_metadata array. Let's take our first example, and let's create a
landing page for only products from brand *Supershirts*. Other brands will not
be a possibility.

``` php
$query = Query::createMatchAll()
    ->filterUniverseBy('brand', ['Supershirts'], Filter::MUST_ALL);
```

You can filter by any field as well after universe filtering. This method have 
a first parameter called filter name. This should be unique, so two filters with 
same name will just be overridden. You can make two or more filters with 
different name over the same field. This filter name will be used as well later
when matching with existing aggregations.

```php
Query::createMatchAll()
    ->filterBy(
        'filtername',
        'field1',
        ['value1', 'value2']
    );
```

By default, this filter is defined as *AT_LEAST_ONE* but you can change this 
behavior by adding a fourth method parameter.

```php
Query::createMatchAll()
    ->filterByMeta(
        'filtername',
        'field1',
        ['value1', 'value2'],
        Filter::MUST_ALL
    );
```

> This filter works with the indexed_metadata field. Remember that the metadata
> field stores non-indexable data

By default, when you filter by meta, specific metadata field aggregation will be
enabled. Disable this aggregation by adding a fifth and last parameter, or just
override it later with a more specific aggregation configuration.

```php
Query::createMatchAll()
    ->filterBy(
        'filtername',
        'field1',
        ['value1', 'value2'],
        Filter::AT_LEAST_ONE,
        false
    );
```

### Aggregations {#query-aggregations}

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

You can create aggregations by hand, for example, if you don't really want
filters, or if the aggregation itself requires an special configuration.

```php
Query::createMatchAll()
    ->aggregateBy(
        'fieldname'
        'field1'
    );
```

Previous filters with name `fieldname` will be searched in order to create the
Result object.
You can change the order of the aggregation, so you don't have to do it later in
your process.

```php
Query::createMatchAll()
    ->aggregateBy(
        'fieldname'
        'field1',
        Filter::AT_LEAST_ONE,
        Aggregation::SORT_BY_COUNT_DESC
    );
```

You can chose between these values

- Aggregation::SORT_BY_COUNT_DESC
- Aggregation::SORT_BY_COUNT_ASC
- Aggregation::SORT_BY_NAME_DESC
- Aggregation::SORT_BY_NAME_ASC

You can limit as well the number of elements you want to return in the
aggregation. By default, there's no limit, so if your result aggregation has
10000 possible values, an array of 10000 counters will be returned. This is
usually not good for performance.

```php
Query::createMatchAll()
    ->aggregateBy(
        'fieldname'
        'field1',
        Filter::AT_LEAST_ONE,
        Aggregation::SORT_BY_COUNT_DESC,
        Aggregation::NO_LIMIT
    );
```

Aggregations can be enabled or disabled by using these flag methods. This flag
will override all behaviors from all filter methods (remember that when
filtering by some fields, for example Types, you can enable or disable a
specific aggregation). If aggregations are enabled, then the behavior will not
change and each field specific behaviors will be used. If disable, all field
specific behaviors will be disabled.

```php
Query::create('')
    ->disableAggregations()
;
```

In this case, aggregations are specifically enabled by Types setting the second 
parameter to `true`, but disabled by flag, so no aggregations will be requested.

```php
Query::createMatchAll()
    ->filterByTypes(
        ['product'],
        true
    )
    ->disabledAggregations()
;
```

### Sort by field

You can sort your results, of course. The Query object provides one method for
this, and the SortBy object defines a prebuilt set of sorting types ready to be
used by you. You can define the sorting field and the type by yourself.

```php
Query::createMatchAll()
    ->sortBy(
        ['indexed_metadata.manufacturer', 'asc']
    );

Query::createMatchAll()
    ->sortBy(
        ['indexed_metadata.name', 'desc']
    );

Query::createMatchAll()
    ->sortBy(
        ['indexed_metadata.updated_at', 'desc']
    );
```

We can use prebuilt sorts. The first one is the one applied by default when no
sorting is defined. The better score given a query, the earlier in results.
This is the list of all of them.

```php
Query::createMatchAll()
    ->sortBy(SortBy::SCORE)
    ->sortBy(SortBy::ID_ASC)
    ->sortBy(SortBy::ID_DESC)
    ->sortBy(SortBy::TYPE_ASC)
    ->sortBy(SortBy::TYPE_DESC)
;
```

When you define a sort element, you override the existing one.

### Sort by location

A set of special sorting types can sort as well by location. In order to make
this sorting work, we must create our Query instance by using the method
`createLocated()` instead of `create()`. The only difference between both is
that the first one's first parameter is a `Coordinate` instance. Therefore, 
the second parameter is the query text.

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
$query = Query::createLocated(
        new Coordinate(40.0, -70.0), 
        ''
    )
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

### Sort randomly

You can sort your elements in a random way by using the fast predefined value

```php
Query::createMatchAll()
    ->sortBy(SortBy::RANDOM)
;
```

### Enabling suggestions

Suggestions can be enabled or disabled by using these flag methods.

```php
Query::create('')
    ->disableAggregations()
;

Query::create('')
    ->enableAggregations()
;
```

Please, read [Reading Suggestions](#reading-suggestions) to know a little bit
more about suggestions.

### Excluding some elements

Having some kind of black list would be useful as well. For example, when
printing a related carousel given an item, and filtering by the type,
would be useful to exclude the current element from the list.

In order to do this, we will use UUIDs, so we can filter by any kind of
element only having the UUID.

```php
Query::createMatchAll()
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
Query::createMatchAll()
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

## Result

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

- getTotalElements() - get the total items in your universe. If you don't have
applied any universe filter, then you will have all of them. Otherwise, you will
have the number of elements in your universe.
- getTotalHits() - get the total hits produced by your query. This is not the
number of Items you have in your Result object, but the Items you can reach in
total by paginating along the result hits.

### Reading Aggregations

The other important part of the Result object is the Aggregation set. In order
to iterate over all Aggregations you can make a simple foreach over the result
of the `->getAggregations()` result (returns an implementation of the PHP
interface Traversable). You can access directly to an aggregation by using the
`->getAggregation($name)` method and the aggregation assigned name.

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

### Reading Suggestions

If your query had the suggests enabled, then you will find some suggestions in
your Result instance by using the getter method.

```php
$suggests = $result->getSuggests();
```

Each suggest is defined as an array of non unique strings.

## Repository

OK, so now we know how to manage all our model objects. How they interact
between them and how we should integrate them with our code.

But how it really works? We need an interface where we can communicate with an
existing endpoint, so we can really have nice results given a set of pre-indexed
data.

Let's check the interface `Puntmig\Search\Repository\Repository`

Using an implementation of this main repository, you'll be able to index,
delete, reset and query your main data set. Each interaction will create an 
internal event, each one named in a particular way. To query over these events,
please check the [EventRepository](#event-repository) chapter.

All repository implementations, before being used, need your API secret in order
to make sure you're using the right repository.

Let's take a look at all our repository interfaces

### HttpRepository {#http-repository}

This is the main implementation of this repository, ready for production
purposes and defined for pointing to our main servers.

In order to make it work, you'll need a HTTPClient implementation. In that case,
and because your usage will be maily for production, you can use the
GuzzleClient implementation.

```php
$repository = new HttpRepository(
    new GuzzleClient('http://api.ourhost.xyz:1234')
);
$repository->setKey('mysecretkey');
```

### TransformableRepository {#transformable-repository}

This is a small wrapper of the simple HttpRepository, ready to understand and
interact with your own Domain, instead of working with Items.

Before understanding how this adapter work, we should understand what a
transformer is and how can be really useful when integrating our project with
ApiSearch.

Imagine we have a domain class called Product. Because this class is part of our
domain, we should never change its composition because of external changes,
right? So imagine that we want to start using ApiSearch. As reading this
documentation we can see that ApiSearch understands about Items, and only about
them, so you could think...

`Oups, I should change my product and only work with Items`

That would be a very very bad decision, so remember that with or without
ApiSearch, your product will continue being a Product.

Said that, and if you check all available Repository methods, you'll notice that
you can only use Item related methods (addItem, deleteItem...), so does it mean
that do I need to work always with items? Not at all.

We must find a way where there is a silent transformation between our model
(Product) and the ApiSearch model (Item, ItemUUID), and this way is called
Transformers.

So what is a Transformer? Easy. A class that can convert any of your model
objects into an Item, by implementing the interface WriteTransformer, and vice
versa, by implementing the interface ReadTransformer. All Repositories must
implement WriteTransformer, so it has not sense at all to have a transformer
that does'nt write, but implementing ReadTransformer is optional.

The difference between both strategies is that by implementing ReadTransformer,
you will receive your own model objects when making queries. Otherwise, when
reading from the repository, you will receive only Item instances.

Let's check a Transformer example

```php
class ProductTransformer implements ReadTransformer, WriteTransformer
{
    /**
     * Is an indexable object.
     *
     * @param mixed $object
     *
     * @return bool
     */
    public function isValidObject($object): bool
    {
        return $object instanceof Product;
    }

    /**
     * Create item by object.
     *
     * @param mixed $object
     *
     * @return Item
     */
    public function toItem($object): Item
    {
        return Item::create(
            $this->toItemUUID($object),
            [
                // Metadata 
                'name' => $object->getName(),
                'description' => $object->getDescription(),
                'ean' => $object->getEan(),
            ],
            [
                // Indexed metadata
                'price' => $object->getPrice(),
                'old_price' => $object->getOldPrice(),
                'brand' => $object->getBrand()->getName(),
                'created_at' => $object->getCreatedAt(),
                'sizes' => array_values($object->getSizes()),
                'colors' => array_values($object->getColors()),
            ],
            [
                // Searchable metadata
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'brand' => $this->getBrand()->getName(),
            ],
            [
                // Exact matching metadata
                $this->getId(),
                $this->getEan(),
            ],
            [
                // Suggestions
                'T-shirt',
            ]
    }

    /**
     * Create item UUID by object.
     *
     * @param mixed $object
     *
     * @return ItemUUID
     */
    public function toItemUUID($object): ItemUUID
    {
        return new ItemUUID(
            $object->getId(),
            'product'
        );
    }
    
    /**
     * The item should be converted by this transformer.
     *
     * @param Item $item
     *
     * @return bool
     */
    public function isValidItem(Item $item): bool
    {
        return $item->getType() === 'product';
    }

    /**
     * Create object by item.
     *
     * @param Item $item
     *
     * @return mixed
     */
    public function fromItem(Item $item)
    {
        return new Product(
            $item->getId(),
            $item->get('name'),
            $item->get('description),
            // ...
    }
}
```

Ok, we have our transformer ready. Then what? Let's see how we can build a 
TransformableRepository instance.

> Please, check Symfony integration. This is only a small snippet to show how
> this class is internally built. The Bundle build all these instances by using
> the dependency injection component.

```php
$productTransformer = new ProductTransformer();
$transformer = new Transformer($eventDispatcher);
$transformer->addReadTransformer($productTransformer);
$transformer->addWriteTransformer($productTransformer);
$transformableRepository = new TransformableRepository(
    $httpRepository,
    $transformer 
);
$transformableRepository->setKey('mysecretkey');
```

And that's it.
Discovering a little bit the TransformableRepository we will see that, apart
of having the natural Item related methods, we will have same methods but with
Object instead of Item

- ->addObject()
- ->deleteObject()

And when querying the repository, if your class specific transformer implements
ReadTransformer, instead of having an Item instance, you'll have a
transformation.

> Using Read transformation or not should be a project scope decision, so having
> only a few Transformers implementing ReadTransformer interface is not a good
> thing.

### InMemoryRepository {#in-memory-repository}

Only for development and testing purposes. Not all endpoints are available, and
not all features can be done by using a simple in-memory array, so you'll be
able to index, delete, reset and perform basic queries (remember, in memory,
this means that between requests, this won't work at all).

```php
$repository = new InMemoryRepository();
$repository->setKey('mysecretkey');
```

So what can I do with any of these implementations?

### Reset

Do you want to reset your entire index? That easy. One single call and all your
read-only data will be completely erased.

```php
$repository->reset();
```

When a repository is reset, internally this is deleted and created again. An
index can be created by using a specific language. When this language is
defined, then some internal improvements are performed in order to provide
better experience on search time.

```php
$repository->reset('en');
```

### Index

This repository endpoint aims to let you add your entities in the database. Of 
course, to understand how easy is to do that, first of all you need to 
understand how the model is modeled (the first chapter of this documentation is
about that).

```php
$repository->addItem(Item $item);
```

If you use TransformableRepository, you can use as well the Object related
method.

```php
$repository->addObject(Object $object);
$repository->addObject($product);
```

This method only prepares this new item to be added/modified, but does'nt
change it actually

### Delete

Deletes an existing element from your repository. In order to use this endpoint,
you need to work with Item references.

```php
$itemUUID = new ItemUUID('10', 'product');
$repository->deleteItem($itemUUID);
```

If you use TransformableRepository, you can use as well the Object related
method.

```php
$repository->deleteObject(Object $object);
$repository->deleteObject($product);
```

This method only prepares this new item to be deleted, but does'nt change it
actually

### Flush

Perform real changes by having a stack of additions and deletions.

```php
$repository->flush();
```

You can batch items when connecting to the repository in order to minimize the
number of connections and the size of these. In next example, every connection
will contain 100 elements maximum. By default this value is set to 500.

```php
$repository->flush(100);
```

If we want to only flush if and only if we have a minimum of 100 elements, then
we can use the second parameter by setting it to true.

```php
$repository->flush(100, true);
```

### Query {#query-repository}

This endpoint will be a very important part of your integration, so allow you
to, given a Query instance, get a Result instance.

```php
$result = $repository->query(Query $query) : Result
```

That's it. The result of the query method is a Result instance. To know a little
bit more about this object, check the documentation chapter.

## EventRepository

Do you remember that each Repository interaction creates a simple Event? That is
because we track every single movement you make to our servers. And this is good
because if we can know, you can know.

So let's take a look at how you can retrieve all these events and check as many
metrics as you want. With this example, we get all first 100 Queries done from
yesterday at same hour until now.

```php
$eventRepository = new EventRepository(
    new GuzzleClient('http://api.ourhost.xyz:1234')
);
$repository->all(
    'mysecretkey',
    'QueryWasMade',
    (time() - (24 * 60 * 60)),
    time(),
    100,
    0
);
```

Let's check as well all different events.

- IndexWasReset
- ItemsWereIndexed
- ItemsWereDeleted
- QueryWasMade

So, what does this endpoint return to us?

### Event

An array of Events. And what is an event? A very simple class that has
everything we need in order to make metrics, panels and side calculations.

```php
/**
 * Class Event.
 */
class Event implements HttpTransportable
{
    /**
     * @var int
     *
     * Id
     */
    private $id;

    /**
     * var string.
     *
     * Consistency hash
     */
    private $consistencyHash;

    /**
     * @var string
     *
     * name
     */
    private $name;

    /**
     * @var string
     *
     * Key
     */
    private $key;

    /**
     * @var string
     *
     * Payload
     */
    private $payload;

    /**
     * @var int
     *
     * Occurred on
     */
    private $occurredOn;
}
```

When retrieving the payload, you will receive a json encoded array, so make sure
you decode it if you want to digg into the event content.

```php
$payload = json_decode($event->getPayload(), true);
```

## Integrations

You can find some integrations of this library

[Symfony Bundle](https://github.com/puntmig/search-bundle)
