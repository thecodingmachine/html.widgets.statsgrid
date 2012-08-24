StatsGrid: a PHP pivot table
============================

StatsGrid is a PHP library that let's you generate HTML pivot tables from any dataset.
You give the data to be rendered as an array to StatsGrid and it will render the HTML. For instance, you can
give this array:

	$data = array(
		array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"February", "CA"=>12, "Benef"=>2),	
		array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"April", "CA"=>12, "Benef"=>2),	
		array("country"=>"US", "city"=>"NY", "year"=>2009, "month"=>"May", "CA"=>15, "Benef"=>5),
		array("country"=>"US", "city"=>"Baltimore", "year"=>2009, "month"=>"April", "CA"=>42, "Benef"=>3),
		array("country"=>"US", "city"=>"Baltimore", "year"=>2010, "month"=>"April", "CA"=>24, "Benef"=>4),
		array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"May", "CA"=>12, "Benef"=>2),
		array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"June", "CA"=>12, "Benef"=>2),	
	);

and StatsGrid can generate this kind of reports:

<table><tr>
<td></td><td></td><td colspan='6'>2009</td><td colspan='6'>2010</td>
</tr><tr>
<td></td><td></td><td colspan='2'>February</td><td colspan='2'>April</td><td colspan='2'>May</td><td colspan='2'>April</td><td colspan='2'>May</td><td colspan='2'>June</td>
</tr><tr>
<td></td><td></td><td>CA</td><td>Be.</td><td>CA</td><td>Be.</td><td>CA</td><td>Be.</td><td>CA</td><td>Be.</td><td>CA</td><td>Be.</td><td>CA</td><td>Be.</td>
</tr><tr>
<td rowspan='3'>US</td><td>Chicago</td><td>12</td><td>2</td><td>12</td><td>2</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
</tr><tr>
<td>NY</td><td></td><td></td><td></td><td></td><td>15</td><td>5</td><td></td><td></td><td></td><td></td><td></td><td></td>
</tr><tr>
<td>Baltimore</td><td></td><td></td><td>42</td><td>3</td><td></td><td></td><td>24</td><td>4</td><td></td><td></td><td></td><td></td>
</tr><tr>
<td>FR</td><td>Paris</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>12</td><td>2</td><td>12</td><td>2</td>
</tr></table> 

Usage sample:
-------------

To generate a statsgrid, you need several things:
- obviously, a dataset (the raw data that you will render)
- you will also need a set of row and a set of column descriptors (describing what should be in row and what should be in column)
- finally, you need to decide what values are to be displayed in the grid

Here is a sample code base:


	// Let's define the data to be displayed (usually, you will get this from a database using GROUP BY statements)
	$data = array(
		array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"February", "CA"=>12, "Benef"=>2),	
		array("country"=>"US", "city"=>"Chicago", "year"=>2009, "month"=>"April", "CA"=>12, "Benef"=>2),	
		array("country"=>"US", "city"=>"NY", "year"=>2009, "month"=>"May", "CA"=>15, "Benef"=>5),
		array("country"=>"US", "city"=>"Baltimore", "year"=>2009, "month"=>"April", "CA"=>42, "Benef"=>3),
		array("country"=>"US", "city"=>"Baltimore", "year"=>2010, "month"=>"April", "CA"=>24, "Benef"=>4),
		array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"May", "CA"=>12, "Benef"=>2),
		array("country"=>"FR", "city"=>"Paris", "year"=>2010, "month"=>"June", "CA"=>12, "Benef"=>2),	
	);
	
	// Let's create the instance
	$grid = new StatsGrid();
	// We define 2 rows: COUNTRY and CITY
	$grid->setRows(array(
		new StatsColumnDescriptor("country"),	
		new StatsColumnDescriptor("city")	
	));
	// We define 2 columns: YEAR and MONTHS
	$grid->setColumns(array(
			new StatsColumnDescriptor("year"),
			new StatsColumnDescriptor("month")
	));
	// We define 2 values: CA and Benef
	$grid->setValues(array(
		new StatsValueDescriptor("CA", "CA"),
		new StatsValueDescriptor("Benef", "Be."),		
	));
	// We set the data
	$grid->setData($data);
	
	// We print the table
	$grid->toHtml();


Adding aggregation (sums/means...):
-----------------------------------

Presenting data in a pivot table is nice, but often, you will find out you want to display sums or means of the data at the bottom of the table.
StatsGrid let's you *aggregate* data (performing sums/means...) on any column or any row. This way, you can perform sums / subsums, etc... the way you want.

In order to aggregate data, you just need to call the 

 
