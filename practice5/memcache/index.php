<?php  /// php  синтаксис подсвечивает только в нутри тегов <?php

$m = new Memcached();
$m->addServer('localhost', 11211);
$infoPage = $m->get('infoPage');

/** просто сложил страницу в кеш, для проверки как он ее потом вернет, пролем не возникло */
if ( !$infoPage ) {
 $m->set('infoPage', include('1.html'), 86400 );
}
echo $m->get('infoPage');