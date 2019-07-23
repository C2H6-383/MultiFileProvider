# MultiFileProvider
##### A small PHP class for combining multiple CSS/JS files into one File.

## Requirements
- PHP 5.4 or newer

## Getting Started
1. Download the source files from this repository
2. Extract the archive
3. Copy the *MultiFileProvider.php* File in the directory of your website
4. require it in php
7. You are ready to read the *basics* chapter of this readme.

##Basics
####1. CSS files *without url()*
**a) your php file (e.g. allcss.php):**

```php
//create the object 
require_once('path_to_MultiFileprovider.php'); //require the file
$your_object_name=new MultiFileProvider(MultiFileProvider::$allowedMimeTypes["css"]); //specifying the css mime type is easy, because you could access all allowed mime type  of the class as array (file extension = key, mime type = value) with MultiFileProvider::$allowedMimeTypes.

//add files with keywords, which can be used to request this file via <link> in html
$your_object_name->addFiles("a_key_word", ["file_1.css","file_2.css"]); //add multiple files to one key word
$your_object_name->addFile("another_keyword", "file_3.css"); //add one file for one keyword

//run the file creation process
$your_object_name->createMultiFile();
```

**b) the HTML page (e.g. index.html):**
```html
<!doctype html>
<html lang="de"  contenteditable="false">
    <head>
        ...
		<!-- add Files file_1.css and file_2.css to the page, using the keyword a_key_word-->
        <!-- and add File file_3.css to the page, using the keyword another_keyword-->
        <link rel="stylesheet" type="text/css" href="path/to/allcss.php/a_key_word/another_keyword/foo/a"/>
        ...
    </head>
    ...
```
now you have file_1.css, file_2.css and file_3.css in the page (if you have more files, use more keywords, but you have to add these files in the php page with addFile/addFiles)

####2. CSS files *with url()*
If you have relative paths in one of your css files, like so: url(../../file.extension), you have to replace the relative path with the full web adress or, you can write a key word, like THE_WEBADRESS_OF_MY_PAGE to let the MultiFileProvider class do the work.
**a) your php file (e.g. allcss.php):**

```php
//create the object 
require_once('path_to_MultiFileprovider.php'); //require the file
$your_object_name=new MultiFileProvider(MultiFileProvider::$allowedMimeTypes["css"],"http://my-website.com/topLevel/");//mime type like in example 1. The adress is the Top level of your website, like http://google.com or http://mysite.com/another_folder_level/

//add files with keywords, which can be used to request this file via <link> in html, like example 1
$your_object_name->addFiles("a_key_word", ["file_1.css","file_2.css"]); //add multiple files to one key word
$your_object_name->addFile("another_keyword", "file_3.css"); //add one file for one keyword

//add the key word (used in the css files), which gets replaced with the top level, specified in the object creation
$your_object_name->set_RootWord("THE_WEBADRESS_OF_MY_PAGE");
//now, the class will replace this "RootWord" with the top level adress

//run the file creation process, like example 1
$your_object_name->createMultiFile();
```

**b) the HTML page (e.g. index.html):** like example 1
```html
<!doctype html>
<html lang="de"  contenteditable="false">
    <head>
        ...
		<!-- add Files file_1.css and file_2.css to the page, using the keyword a_key_word-->
        <!-- and add File file_3.css to the page, using the keyword another_keyword-->
        <link rel="stylesheet" type="text/css" href="path/to/allcss.php/a_key_word/another_keyword/foo/a"/>
        ...
    </head>
    ...
```
now you have file_1.css, file_2.css and file_3.css in the page and all THE_WEBADRESS_OF_MY_PAGE phrases are replaced by "http://my-website.com/topLevel/"

####3. JS files 
**a) your php file (e.g. alljs.php):**

```php
//create the object 
require_once('path_to_MultiFileprovider.php'); //require the file
$your_object_name=new MultiFileProvider(MultiFileProvider::$allowedMimeTypes["js"]); //like example 1, only changing the key for the allowedMimeTypes to "js" (because we want a .js file)

//add files with keywords, which can be used to request this file via <script src=""> in html
$your_object_name->addFiles("a_key_word", ["script_1.js","script_2.js"]); //add multiple files to one key word
$your_object_name->addFile("another_keyword", "script_3.js"); //add one file for one keyword

//run the file creation process
$your_object_name->createMultiFile();
```

**b) the HTML page (e.g. index.html):**
```html
<!doctype html>
<html lang="de"  contenteditable="false">
    <head>
		...
		<script src="path/to/alljs.php/a_key_word"></script>
		...
    </head>
    <body>
		...
		<script src="path/to/alljs.php/another_keyword"></script>
	</body>
</html>
```
now you have script_1.js and script_2.js included in your HTML head and script_3.js included after your page in the HTML body.

####4. CSS & JS files - only example 1,2 and 3 together :)
**a) the PHP file for the CSS files (e.g. allcss.php):**
```php
//create the object 
require_once('path_to_MultiFileprovider.php'); //require the file
$your_object_name=new MultiFileProvider(MultiFileProvider::$allowedMimeTypes["css"],"http://my-website.com/topLevel/");//like example 2

//add files with keywords, which can be used to request this file via <link> in html, like example 1
$your_object_name->addFiles("a_key_word", ["file_1.css","file_2.css"]); //add multiple files to one key word
$your_object_name->addFile("another_keyword", "file_3.css"); //add one file for one keyword

//add the key word (used in the css files), which gets replaced with the top level, specified in the object creation
$your_object_name->set_RootWord("TOP_LEVEL_ADRESS");
//now, the class will replace this "RootWord" with the top level adress

//run the file creation process, like example 1
$your_object_name->createMultiFile();
```

**b) the PHP file for the JSfiles (e.g. alljs.php):**
```php
//create the object 
require_once('path_to_MultiFileprovider.php'); //require the file
$your_object_name=new MultiFileProvider(MultiFileProvider::$allowedMimeTypes["js"]); //like example 3

//add files with keywords, which can be used to request this file via <script src=""> in html
$your_object_name->addFiles("a_key_word", ["script_1.js","script_2.js"]); //add multiple files to one key word
$your_object_name->addFile("another_keyword", "script_3.js"); //add one file for one keyword

//run the file creation process
$your_object_name->createMultiFile();
```
**c) the HTML page (e.g. index.php):**
```html
<!doctype html>
<html lang="de"  contenteditable="false">
    <head>
		...
		<link rel="stylesheet" type="text/css" href="path/to/allcss.php/a_key_word/another_keyword/foo/a"/>
		<script src="path/to/alljs.php/a_key_word"></script>
		...
    </head>
    <body>
		...
		<script src="path/to/alljs.php/another_keyword"></script>
	</body>
</html>
```
now you have the styles from file_1.css, file_2.css and file_3.css in your page and the scripts from script_1.js and script_2.js in your HTML head and the scripts from script_3.js in your HTML body after your page.

*I hope, you can create your page with this examples*


## Functions

The following functions and variables could be accessed with an Object of the MultiFileProvider class
##### General
- **constructor($mimeType, $SiteTopAddress="http://google.com")** - constructs the object, *$mime* type is the mime type of all added files, *$SiteTopAddress* is only used if set_RootWord is performed.
- **createMultiFile()** - creates a File, with all files (specified by a key word in the url) and returns it to the browser
- **set_RootWord($newWord)** - sets the key word, which gets replaced in the files with the (in the constructor specified) *SiteTopAddress*

##### File Managment
- **addFile($urlKeyWord, $FileName)** - adds a File with *FileName*  and *urlKeyWord* to the list of included files. This file could be accessed from the browser by adding the *urlKeyWord* to the url after the file name, like so: http://yourpage.com/file.php/key1/key2/key3/key_n
- **addFiles($urlKeyWord, $FileNames)** - adds multiple Files with names in the array *FileNames* to the list of included files... Like *addFile*

##### values
- ****

------------

## Release notes
**1.0**
- added general functions like open, close, set content...

**1.1**
- fixed some errors and added content_hide() and content_show functions...

**1.2**
- fixed some erros
- added async functions
- added remove functions
