# File Editor
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

## License

MIT

<br><br>

## How to install it

Use composer.

```
composer require adinan-cenci/file-editor
```