WikiPedia Data Dump - PHP SAX Parser
====================================

This tiny PHP script will parse through the publicly available WikiPedia data dumps and extract useful information into easy to access locations.


Warning
- Use this program at your own discretion. 


Prerequisites
- PHP >= 5.0.0
- lib_xml extension enabled if 5.0.0 =< PHP < 5.1.0  -- [installation notes](http://www.php.net/manual/en/xmlreader.installation.php)
- MySQL with InnoDB support


Recommendations
- SSD or >=10K RPM Hard Drive


How to run


Known bugs
- 



Why I wrote this program
There have been a lot of programs out there to parse WikiPedia data dumps, but most of them are of other languages (Pythong, Perl, etc.) or very old. This is just a proof in concept that PHP can parse tremendously large XML files in the matter of minutes with very minimal resource usage. In my personal tests (i7 920, 240gb RAID0 SSD, 12GB 1666), I managed to parse through an individual XML WikiPedia data dump in 2-3 minutes, resulting in 3-5k/second simultaneous MySQL row inserts and individual file/folder creation on the same disk.

 internal MediaWiki pageid and title into a MySQL table and the raw contents of each WikiPedia page into an individual file in an easy to find file path.