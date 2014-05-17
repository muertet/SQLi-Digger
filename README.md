SQLi-Digger
===========

[![Analytics](https://ga-beacon.appspot.com/UA-17476024-7/SQLi-Digger/readme?pixel)](https://github.com/muertet/SQLi-Digger)

** OLD HORRIBLE CODE **


Usage:
TARGET_URL=Must finish with vulnerable var(ex: http://google.com/index.php?id=1)

$injection=new SqlDigger(TARGET_URL);

$injection->init();

$injection->getRows();

* $injection->currentTable(); // returns current table
* $r=$injection->show('databases'); //returns databases
* $r=$injection->show('tables',array('db'=>'DATABASE')); //returns tables from DATABASE
* $r=$injection->show('columns',array('db'=>'DATABASE','table'=>'TABLE')); //returns colums from DATABASE.TABLE
