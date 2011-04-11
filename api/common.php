<?PHP

 require_once(dirname(dirname(__FILE__)) . '/modules/pheal/Pheal.php');

 spl_autoload_register("Pheal::classload");
 PhealConfig::getInstance()->api_base = 'https://api.eveonline.com/';
 PhealConfig::getInstance()->cache = new PhealFileCache(dirname(dirname(__FILE__)) . '/cache/');
 PhealConfig::getInstance()->log = new PhealFileLog(dirname(dirname(__FILE__)) . '/logs/');

?>
