<?PHP

 require('../api/common.php');
 require('../db/evedb.class.php');

 header('Content-type: text/plain');

 $api = new Pheal($_GET['user'], $_GET['key']);
 $db = new EveDB();

 function getAsset($asset) {
  global $db;

  $res = $db->items->getById($asset->typeID);
  
  if (isset($asset->contents)) {
   $res->contents = array();

   foreach($asset->contents as $content) {
    $res->contents[] = getAsset($content);
   }
  }

  return $res;
 }

 foreach ($api->charScope->AssetList(array('characterID' => $_GET['char']))->assets as $asset) {
  var_dump(getAsset($asset));
 }
?>
