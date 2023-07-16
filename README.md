# File Editor

A small library to edit and read files.

This used to be part of my [json-lines](https://github.com/adinan-cenci/json-lines) library, but I decided to move to a separated repository for the sake of organization.

<br><br>

## How to use it

**Instantiating**

```php
use AdinanCenci\FileEditor\File;

$file = new File('my-file.txt');
```

<br><br>

**Iterating**

```php
foreach ($file->lines as $lineN => $line) {
    echo "$lineN: $line <br>";
}
```

<br><br>

**Add a line to the end of the file**

```php
$file->addLine('foo-bar');
```

<br><br>

**Add a line to the middle of the file**

```php
$lineN = 5;
$line  = 'foo-bar';
$file->addLine($line, $lineN);
```

If the file has less than `$line` lines, the gap will be filled with blank lines.

<br><br>

**Add several lines to the end of the file**

```php
$lines = [
    'foo: bar',
    'bar: foo',
];

$file->addLines($lines);
```

<br><br>

**Add several lines in the middle of the file**

```php
$lines = [
    2 => 'foo: bar',
    6 => 'bar: foo',
];

$file->addLines($lines, false);
```

<br><br>

**Replace an existing line**

```php
$lineN = 10;
$line  = 'foo-bar';
$file->setLine($lineN, $line);
```

The difference between `::addLine()` and `::setLine()` is that `::setLine()` will overwrite whatever is already present at `$line`. 

<br><br>

**Set multiple lines**

```php
$lines = [
    0 => 'foo: bar',
    5 => 'bar: foo',
];

$file->setLines($lines);
```

<br><br>

**Retrieve lines**

```php
$lineN = 10;
$line  = $file->getLine($lineN);
```

Returns `null` if the line does not exist.

<br><br>

**Retrieve multiple objects**

```php
$linesN = [0, 1, 2];
$lines  = $file->getLines($linesN);
```

<br><br>

**Delete lines**

```php
$lineN = 10;
$file->deleteLine($lineN);
```

<br><br>

**Delete multiple lines**

```php
$linesN = [0, 1, 2];
$file->deleteLines($linesN);
```

<br><br>

## Search

The library also provides a way to query the file.  
Instantiate a new `Search` object, give it conditions and call the `::find()` method, 
it will return an array of matching lines indexed by their line in the file.

```php
$search = $file->search();
$search->condition('content', 'value to compare', 'operator');
$results = $search->find();
```

<br><br>

**Equals operator**

```php
$search->condition('lineNumber', 10, '=');
// Will match the 11th line in the file.
```

<br><br>

**In operator**

```php
$search->condition('content', ['Iliad', ' Odyssey'], 'IN');
// Will match lines that match either "Iliad" or "Odyssey" 
// ( case insensitive ).
```

<br><br>

**Like operator**

```php
$search->condition('content', 'foo', 'LIKE');
// Will match lines that contains the word "foo"
// e.g: "foo", "foo bar", "foofighters" etc ( case insensitive ).

$search->condition('content', ['foo', 'bar'], 'LIKE');
// It also accept arrays. This will match match 
// "fool", "barrier", "barista" etc.
```

<br><br>

**Regex operator**

```php
$search->condition('content', '#\d{2}\/\d{2}\/\d{4}#', 'REGEX');
// Will match lines against a regex expression.
```

<br><br>

**Number comparison operators**

It also supports "less than", "greater than", "less than or equal", "greater than or equal" and "between".

```php
$search
  ->condition('lineNumber', 2022, '<')
  ->condition('lineNumber', 1990, '>')
  ->condition('lineNumber', 60, '<=')
  ->condition('lineNumber', 18, '>=')
  ->condition('length', [10, 50], 'BETWEEN');
```

<br><br>

### Negating operators

You may also negate the operators.

```php
$search
  ->condition('content', 'Iliad', '!=') // Different to ( case insensitive ).
  ->condition('content', ['Iliad', ' Odyssey'], 'NOT IN') // case insensitive.
  ->condition('length', [10, 50], 'NOT BETWEEN')
  ->condition('content', ['foo', 'bar'], 'UNLIKE');
```

<br><br>

### Multiple conditions

You may add multiple conditions to a search.
By default all of the conditions must be met.

```php
$search = $file->search();
$search
  ->condition('content', 'Iron Maiden', '=')
  ->condition('lineNumber', 2000, '<');
$results = $search->find();
// Will match entries for Iron Maiden, before the line 2000.
```

But you can make it so that only one needs to be met.

```php
$search = $file->search('OR');
$search
  ->condition('content', 'Blind Guardian', '=')
  ->condition('content', 'Demons & Wizards', '=');
$results = $search->find();
// Will match entries for both Blind Guardian and Demons & Wizards.
```

<br><br>

### Conditions groups

You may also group conditons to create complex queries.

```php
$search = $file->search('OR');

$search->andConditionGroup()
  ->condition('content', 'Angra', '=')
  ->condition('lineNumber', 2010, '<');

$search->andConditionGroup()
  ->condition('content', 'Almah', '=')
  ->condition('lineNumber', 2010, '>');

$results = $search->find();
// Will match entries for Angra from before line 2010 OR
// entries for Almah from after that
```

<br><br>

## License

MIT

<br><br>

## How to install it

Use composer.

```
composer require adinan-cenci/file-editor
```