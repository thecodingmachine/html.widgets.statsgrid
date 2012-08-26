StatsGrid: a PHP pivot table
============================

StatsGrid is a PHP library that let's you generate HTML pivot tables from any dataset.
You give the data to be rendered as an array to StatsGrid and it will render the HTML. For instance, you can
give this array:

```php
$data = array(
	array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"February", "sales"=>12, "profit"=>2),	
	array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"April", "sales"=>12, "profit"=>2),	
	array("country"=>"US", "city"=>"NY", "year"=>2009, "month"=>"May", "sales"=>15, "profit"=>5),
	array("country"=>"US", "city"=>"Baltimore", "year"=>2009, "month"=>"April", "sales"=>42, "profit"=>3),
	array("country"=>"US", "city"=>"Baltimore", "year"=>2010, "month"=>"April", "sales"=>24, "profit"=>4),
	array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"May", "sales"=>12, "profit"=>2),
	array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"June", "sales"=>12, "profit"=>2),	
);
```
	
and StatsGrid can generate this kind of reports:

<table class='bluestatsgrid'><tr>
<td></td><td></td><td colspan='6' class='header column0'>2009</td><td colspan='6' class='header column0'>2010</td>
</tr><tr>
<td></td><td></td><td colspan='2' class='header column1'>February</td><td colspan='2' class='header column1'>April</td><td colspan='2' class='header column1'>May</td><td colspan='2' class='header column1'>April</td><td colspan='2' class='header column1'>May</td><td colspan='2' class='header column1'>June</td>
</tr><tr>
<td></td><td></td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td><td class='header column2'>Sales</td><td class='header column2'>Prof.</td>
</tr><tr>
<td rowspan='3' class='header row0'>US</td><td class='header row1'>Chicago</td><td class='value roweven columnodd'>12</td><td class='value rowodd columnodd'>2</td><td class='value roweven columnodd'>12</td><td class='value rowodd columnodd'>2</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td>
</tr><tr>
<td class='header row1'>NY</td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'>15</td><td class='value rowodd columneven'>5</td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td>
</tr><tr>
<td class='header row1'>Baltimore</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'>42</td><td class='value rowodd columnodd'>3</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'>24</td><td class='value rowodd columnodd'>4</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td>
</tr><tr>
<td class='header row0'>FR</td><td class='header row1'>Paris</td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'>12</td><td class='value rowodd columneven'>2</td><td class='value roweven columneven'>12</td><td class='value rowodd columneven'>2</td>
</tr></table>

Installing StatsGrid:
---------------------

StatsGrid comes as a composer package (the name of the package is *mouf/html.widgets.statsgrid*)

Not used to Composer? The first step is installing Composer. 
This is essentially a one line process:

```bash
curl -s https://getcomposer.org/installer | php
```

Windows users can download the phar file here: [http://getcomposer.org/download/](install composer).
Then create a *composer.json* file at the root of your project:

```json
{
    "require": {
        "mouf/html.widgets.statsgrid": ">=1.0-dev"
    },
    "minimum-stability": "dev" 
}
```

and finally, run

```bash
php composer.phar install
```


Usage sample:
-------------

To generate a statsgrid, you need several things:
- obviously, a dataset (the raw data that you will render)
- you will also need a set of row and a set of column descriptors (describing what should be in row and what should be in column)
- finally, you need to decide what values are to be displayed in the grid

StatsGrid is compatible with [Mouf](http://mouf-php.com) so you can completely define a grid using Mouf's graphical interface.

Not using [Mouf](http://mouf-php.com)? You should! Be here is a sample code base anyway:

```php
// Let's load the autoloader
require 'vendor/autoload.php';

// Let's import all required classes
use Mouf\Html\Widgets\StatsGrid\StatsGrid;
use Mouf\Html\Widgets\StatsGrid\StatsColumnDescriptor;
use Mouf\Html\Widgets\StatsGrid\StatsValueDescriptor;
use Mouf\Html\Widgets\StatsGrid\Aggregate\SumAggregator;

// Let's define the data to be displayed (usually, you will get this from a database using GROUP BY statements)
$data = array(
	array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"February", "sales"=>12, "profit"=>2),	
	array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"April", "sales"=>12, "profit"=>2),	
	array("country"=>"US", "city"=>"NY", "year"=>2009, "month"=>"May", "sales"=>15, "profit"=>5),
	array("country"=>"US", "city"=>"Baltimore", "year"=>2009, "month"=>"April", "sales"=>42, "profit"=>3),
	array("country"=>"US", "city"=>"Baltimore", "year"=>2010, "month"=>"April", "sales"=>24, "profit"=>4),
	array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"May", "sales"=>12, "profit"=>2),
	array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"June", "sales"=>12, "profit"=>2),	
);

// The StatsGrid object is the main object used to display the grid
$grid = new StatsGrid();

// Let's associate the data to the grid
$grid->setData($data);

// Now, let's define 2 row descriptors: COUNTRY and CITY
$countryRow = new StatsColumnDescriptor("country");
$cityRow = new StatsColumnDescriptor("city");
// We associate these column descriptors to the grid
$grid->addRow($countryRow);
$grid->addRow($cityRow);


// Now, let's define 2 column descriptors: YEAR and MONTH
$yearColumn = new StatsColumnDescriptor("year");
$monthColumn = new StatsColumnDescriptor("month");
// We associate these column descriptors to the grid
$grid->addColumn($yearColumn);
$grid->addColumn($monthColumn);

// Let's define the values to be displayed in the table
$salesValue = new StatsValueDescriptor("sales", "Sales");
$grid->addValue($salesValue);

// Finally, let's print the table
$grid->toHtml();
```

Adding aggregation (sums/means...):
-----------------------------------

Presenting data in a pivot table is nice, but often, you will find out you want to display sums or means of the data at the bottom of the table.
StatsGrid let's you *aggregate* data (performing sums/means...) on any column or any row. This way, you can perform sums / subsums, etc... the way you want.

In order to aggregate data, you just need to call the addAggregator method and pass a valid aggregator object.

```php
// Let's add 4 aggregators (sub and subsums on columns and on rows)
$grid->addAggregator(new SumAggregator($countryRow, $salesValue, "Total Sales"));
$grid->addAggregator(new SumAggregator($cityRow, $salesValue, "Total city"));
$grid->addAggregator(new SumAggregator($monthColumn, $salesValue, "Total month"));
$grid->addAggregator(new SumAggregator($yearColumn, $salesValue, "Total year"));
```
 
In the sample above we decide we want to sum data by country/city and year/month.
Therefore, we create 4 SumAggregator objects.
For each aggregator we need:
- The column/row we will be summing upon
- The object representing the value we will be summing
- The title of the aggregator

By just adding those 4 lines, we will get this:

<table class='bluestatsgrid'><tr>
<td></td><td></td><td colspan='4' class='header column0'>2009</td><td colspan='4' class='header column0'>2010</td><td class='header column0'>Total year</td>
</tr><tr>
<td></td><td></td><td class='header column1'>February</td><td class='header column1'>April</td><td class='header column1'>May</td><td class='header column1'>Total month</td><td class='header column1'>April</td><td class='header column1'>May</td><td class='header column1'>June</td><td class='header column1'>Total month</td><td></td>
</tr><tr>
<td rowspan='4' class='header row0'>US</td><td class='header row1'>Chicago</td><td class='value roweven columneven'>12</td><td class='value rowodd columneven'>12</td><td class='value roweven columneven'></td><td class='aggregate1 value rowodd columneven'>24</td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='aggregate1 value rowodd columneven'>0</td><td class='aggregate0 value roweven columneven'>24</td>
</tr><tr>
<td class='header row1'>NY</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'>15</td><td class='aggregate1 value rowodd columnodd'>15</td><td class='value roweven columnodd'></td><td class='value rowodd columnodd'></td><td class='value roweven columnodd'></td><td class='aggregate1 value rowodd columnodd'>0</td><td class='aggregate0 value roweven columnodd'>15</td>
</tr><tr>
<td class='header row1'>Baltimore</td><td class='value roweven columneven'></td><td class='value rowodd columneven'>42</td><td class='value roweven columneven'></td><td class='aggregate1 value rowodd columneven'>42</td><td class='value roweven columneven'>24</td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='aggregate1 value rowodd columneven'>24</td><td class='aggregate0 value roweven columneven'>66</td>
</tr><tr>
<td class='header row1'>Total city</td><td class='aggregate1 value roweven columnodd'>12</td><td class='aggregate1 value rowodd columnodd'>54</td><td class='aggregate1 value roweven columnodd'>15</td><td class='aggregate1 value rowodd columnodd'>81</td><td class='aggregate1 value roweven columnodd'>24</td><td class='aggregate1 value rowodd columnodd'>0</td><td class='aggregate1 value roweven columnodd'>0</td><td class='aggregate1 value rowodd columnodd'>24</td><td class='aggregate0 value roweven columnodd'>105</td>
</tr><tr>
<td rowspan='2' class='header row0'>FR</td><td class='header row1'>Paris</td><td class='value roweven columneven'></td><td class='value rowodd columneven'></td><td class='value roweven columneven'></td><td class='aggregate1 value rowodd columneven'>0</td><td class='value roweven columneven'></td><td class='value rowodd columneven'>12</td><td class='value roweven columneven'>12</td><td class='aggregate1 value rowodd columneven'>24</td><td class='aggregate0 value roweven columneven'>24</td>
</tr><tr>
<td class='header row1'>Total city</td><td class='aggregate1 value roweven columnodd'>0</td><td class='aggregate1 value rowodd columnodd'>0</td><td class='aggregate1 value roweven columnodd'>0</td><td class='aggregate1 value rowodd columnodd'>0</td><td class='aggregate1 value roweven columnodd'>0</td><td class='aggregate1 value rowodd columnodd'>12</td><td class='aggregate1 value roweven columnodd'>12</td><td class='aggregate1 value rowodd columnodd'>24</td><td class='aggregate0 value roweven columnodd'>24</td>
</tr><tr>
<td class='header row0'>Total Sales</td><td></td><td class='aggregate0 value roweven columneven'>12</td><td class='aggregate0 value rowodd columneven'>54</td><td class='aggregate0 value roweven columneven'>15</td><td class='aggregate1 value rowodd columneven'>81</td><td class='aggregate0 value roweven columneven'>24</td><td class='aggregate0 value rowodd columneven'>12</td><td class='aggregate0 value roweven columneven'>12</td><td class='aggregate1 value rowodd columneven'>48</td><td class='aggregate0 value roweven columneven'>129</td>
</tr></table>

Displaying several values:
--------------------------

If you look at the dataset we have been working on, we have 2 sets of values: "sales" and "profit".
So far, we have been displaying only the "sales" value. However, stats grid can perfectly handle displaying 2 values next to each other.

For instance, you could write:

```php
// Let's define the values to be displayed in the table
$salesValue = new StatsValueDescriptor("sales", "Sales");
$profitValue = new StatsValueDescriptor("profit", "Prof.");
$grid->addValue($salesValue);
$grid->addValue($profitValue);
```

This will displays The 2 values in 2 columns.
If you want values to be displayed in rows rather in column, just add:

```php
// There are several value descriptor, let's put those in rows.
$grid->setValuesDisplayMode(StatsGrid::VALUES_DISPLAY_VERTICAL);
```



Styling statsgrids:
-------------------

Statsgrid comes with a default CSS stylesheet.
You can find it in *css/dist/statsgrid.css*.

Using composer? Add this to your theme:

```html
<link rel="stylesheet" type="text/css" href="[myproject]/vendor/mouf/html.widgets.statsgrid/css/dist/statsgrid.css" />
```

By default the grid is in blueish tones.

You can change the main CSS class of the grid using:
```php
// Changes the design of the grid
$grid->setCssClass("redstatsgrid");
```

Those are valid CSS classes defined in the statsgrid package:
- bluestatsgrid
- redstatsgrid
- greenstatsgrid
- greystatsgrid
- yellowstatsgrid
- purplestatsgrid
- cyanstatsgrid

You can of course provide your own class to suit your needs.
The *css/src/statsgrid.scss* file contains the [SASS file](http://sass-lang.com/) that has been used to generate those themes easily.
