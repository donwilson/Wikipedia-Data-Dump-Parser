WikiPedia Data Dump - PHP SAX Parser
====================================

This tiny PHP script will parse through the publicly available WikiPedia data dumps and extract useful information into easy to access locations.


**Warning:** Please use this program at your own discretion. 


**Requirements**

- PHP >= 5.0.0
- lib_xml extension enabled if 5.0.0 =< PHP < 5.1.0  -- [installation notes](http://www.php.net/manual/en/xmlreader.installation.php)
- MySQL with InnoDB support


**Recommendations**

- SSD or >=10K RPM Hard Drive


**How do I use this?**

Store individual sharded Wikipedia data dumps into `./data/`. Currently the program only accepts Wikipedia data dump files similiar to `enwiki-YYYYMMDD-pages-articles.xml-p###p###`, however with a few modifications it could easily process practically any Wikipedia data dump.

With your Wikipedia data dump files in the `./data/` folder, simply run `php -f ./parse.php`.


**The end result**

You'll end up with a folder with the following result: http://i.imgur.com/vjEYV.jpg - Folders with millions of individual files, each storing a unique Wikipedia page's contents. The white files above each folder in the screenshot is each individual MySQL table with the columns: `hash`, `id`, and `title`, where `id` is Wikipedia's internal MediaWiki pageid and `hash` is an md5 hash of that pageid. Files are stored in a folder matching the database table name, with a two level deep folder structure. For example, for the Wikipedia page [Michael Hudson](), its internal MediaWiki id is `7861009` (with an md5 hash of `1b0efe02d01ba376c597d58d95c219bd`), which can be found in `./pages-articles_007525004-009225000/1b/0e/fe02d01ba376c597d58d95c219bd`.


**Why I wrote this program**

There have been a lot of programs out there to parse WikiPedia data dumps, but most of them are of other languages (Python, Perl, etc.) or very old. This is just a proof in concept that PHP can parse tremendously large XML files in the matter of minutes with very minimal resource usage. In my personal tests (i7 920, 240gb RAID0 SSD, 12GB 1666), I managed to parse through an individual XML WikiPedia data dump in 2-3 minutes, resulting in 3-5k/second simultaneous MySQL row inserts and individual file/folder creation on the same disk.

Of course there is still a bit to be done with this script, from giving it the ability to parse other types of Wikipedia data dumps to extracting more information out of each `<page>` element.
