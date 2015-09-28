mdList2Table
============

A PopClip Extension to convert a markdown formatted list (of max two dimensions) into a markdown formatted table.

### Usage

A list, set out in the following format:

	- Heading 1
	  - Item 1
	  - Item 2
	- Heading 2
	  - Item 1
	  - Item 2
	- Heading 3

will be transformed into the following, harder-to-write-by-hand, format:

```
-->    | Heading 1 | Heading 2 | Heading 3 |
       |-----------|-----------|-----------|
       |  Item 1   |  Item 1   |           |
       |  Item 2   |  Item 2   |           |
```

### Testing

Using [phpunit][ref1], run the following command in Terminal from the project folder:

	phpunit --bootstrap src/autoload.php tests/MdList2TableTest.php

[ref1]: https://phpunit.de