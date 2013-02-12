# ishmael

This is a simple UI to put on top of the data that <a href="http://www.maatkit.org/doc/mk-query-digest.html">mk-query-digest</a> outputs. It let's you browse the query report in a more readable fashion. The aim is to display all the information from the report in a readable, navigable way.

This tool does not add anything to the `mk-query-digest` utility itself. It simply displays the data that the utility generates. 

The name comes from the tool's purpose - "hunting whale queries" - and Richard Crowley's free-associating mind. 

## Usage

Simply create a `conf.php` file using the sample as a guide and copy the files into a folder served by a some webserver that knows what to do with .php files. The code was written to be as portable as possible, so it uses the old-school `mysql_` set of functions and no advanced language features.

## Screenshot

<a href="http://github.com/mihasya/ishmael/raw/master/screenshot.png"><img width="600" style="width: 600px" src="http://github.com/mihasya/ishmael/raw/master/screenshot.png" /></a>

## Authors
* Mikhail Panchenko
* Tim Denike
* Asher Feldman
* JS graph code by Ross Harmes
