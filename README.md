MdList2Table
============

A PopClip Extension to convert a markdown-formatted list into a markdown-formatted table.

## Usage

To keep the extension flexible, it is configured – by regular expression – to appear when a markdown-formatted list is selected, independent of application or OS.

A list, set out in the following format:

	- Heading 1
	  - Item 1
	  - Item 2
	- Heading 2
	  - Item 1
	  - Item 2
	- Heading 3

will be transformed into the following, slower-to-write-by-hand format:

```
-->    | Heading 1 | Heading 2 | Heading 3 |
       |-----------|-----------|-----------|
       |  Item 1   |  Item 1   |           |
       |  Item 2   |  Item 2   |           |
```

#### Table Gaps

A table will be as tall as the heading with the most items, and as wide as the number of heading list items, but gaps can be left in rows by leaving an empty list item (with or without trailing spaces) – as so:

	- Heading 1
	  - Item 1
	  - 
	  - Item 2
	- Heading 2
	  - 
	  - Item 1
	  - Item 2

giving:

```
-->    | Heading 1 | Heading 2 |
       |-----------|-----------|
       |  Item 1   |           |
       |           |  Item 1   |
       |  Item 2   |  Item 2   |
```

#### Deeper Lists

Lists with items more than 2 levels deep will be flattened to be only 2 levels deep. For example:

	- Heading 1
	  - Item 1
	    - Sub-item 1
	- Heading 2
	  - Item 1
	  - Item 2
	- Heading 3

will be transformed into the following table:

```
-->    | Heading 1 | Heading 2 | Heading 3 |
       |-----------|-----------|-----------|
       |  Item 1   |  Item 1   |           |
       | Subitem 1 |  Item 2   |           |
```

#### Empty Tables

An empty table can be created from an empty list structure, (again, with or without trailing spaces), though there is no guarantee that your markdown parser itself will play nicely with it.

	- 
	  - 
	- 
	  - 

## Testing

Using [phpunit][ref1], run the following command in Terminal from the project folder:

	phpunit --bootstrap src/autoload.php tests/MdList2TableTest.php

[ref1]: https://phpunit.de