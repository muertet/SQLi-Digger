SQLi-Digger
===========


Usage:
TARGET_URL=Must finish with vulnerable var(ex: http://google.com/index.php?id=1)

$injection=new SqlDigger(TARGET_URL);

$injection->init();

$injection->getRows();

* $injection->currentTable(); // returns current table
* $r=$injection->show('databases'); //returns databases
* $r=$injection->show('tables',array('db'=>'DATABASE')); //returns tables from DATABASE
* $r=$injection->show('columns',array('db'=>'DATABASE','table'=>'TABLE')); //returns colums from DATABASE.TABLE
