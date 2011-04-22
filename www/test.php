<?PHP

 require('../api/common.php');
 require('../db/evedb.class.php');
 require('../model/asset.php');

 header('Content-type: text/plain');

 $api = new Pheal($_GET['user'], $_GET['key']);
 $db = new EveDB();

 function getAsset($asset) {
  global $db;

  $res = new Asset();

  $res->id = $asset->itemID;

  if (isset($asset->locationID)) {
   $res->location = $db->locations->getById($asset->locationID);
  }

  $res->quantity = $asset->quantity;
  $res->flag = $db->flags->getById($asset->flag);
  $res->singleton = $asset->singleton;
  $res->item = $db->items->getById($asset->typeID);
  
  if (isset($asset->contents)) {
   $res->contents = array();

   foreach($asset->contents as $content) {
    $child = getAsset($content);

    if (!isset($res->contents[$child->flag->name])) {
     $res->contents[$child->flag->name] = array();
    }

    $res->contents[$child->flag->name][] = $child;
   }
  }

  return $res;
 }

 foreach ($api->corpScope->AssetList(array('characterID' => $_GET['char']))->assets as $asset) {
  var_dump(getAsset($asset));
 }
?>
