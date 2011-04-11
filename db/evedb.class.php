<?PHP

require_once(dirname(dirname(__FILE__)) . '/model/ship.php');

class EveDB {

 private $pdo;

 private $handlers;

 public function __construct($connection = null) {
  $this->pdo = new PDO($connection == null ? 'sqlite:' . dirname(__FILE__) . '/inc110-sqlite3-v1.db' : $connection);

  $this->handlers = array(
   'items' => new ItemsTableHandler($this->pdo),
   'locations' => new LocationsTableHandler($this->pdo),
  );
 }

 public function __get($type) {
  return isset($this->handlers[$type]) ? $this->handlers[$type] : null;
 }

}

abstract class BaseHandler {

 protected $pdo;

 public function __construct($pdo) {
  $this->pdo = $pdo;
  $this->initStatements();
 }

 protected abstract function initStatements();

}

class ItemsTableHandler extends BaseHandler {

 private $getByIdSmt;

 protected function initStatements() {
  $columns = 'categoryName, typeID, typeName, invTypes.description';
  $joins = 'JOIN invGroups ON (invGroups.groupID = invTypes.groupID) JOIN invCategories ON (invGroups.categoryID = invCategories.categoryID)';
  $skeleton = 'SELECT ' . $columns . ' FROM invTypes ' . $joins . ' WHERE';

  $this->getByIdSmt = $this->pdo->prepare($skeleton . ' typeID = :id');
 }

 public function getById($id) {
  $this->getByIdSmt->execute(array(':id' => $id));
  return $this->getByIdSmt->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
 }

}

class LocationsTableHandler extends BaseHandler {

 private $getByIdSmt;

 protected function initStatements() {
  $this->getByIdSmt = $this->pdo->prepare('SELECT categoryName, itemName FROM mapDenormalize JOIN invTypes ON (mapDenormalize.typeID = invTypes.typeID) JOIN invGroups ON (invGroups.groupID = invTypes.groupID) JOIN invCategories ON (invGroups.categoryID = invCategories.categoryID) WHERE itemID = :id');
 }

 public function getById($id) {
  // 66014940 <= locationID <= 66014952 then staStations.stationID  = locationID - 6000000
  // 66000000 <= locationID <= 66999999 then staStations.stationID  = locationID - 6000001
  // 67000000 <= locationID <= 67999999 then ConqStations.stationID = locationID - 6000000
  // 60014861 <= locationID <= 60014928 then ConqStations.stationID = locationID
  // 60000000 <= locationID <= 61000000 then staStations.stationID  = locationID
  // 61000000 <= locationID             then ConqStations.stationID = locationID
  //                                 default mapDenormalize.itemID  = locationID

  $id = (double) $id;

  if (66014940 <= $id && $id <= 66014952 || 67000000 <= $id && $id <= 67999999) {
   $id -= 6000000;
  } else if (66000000 <= $id && $id <= 66999999) {
   $id -= 6000001;
  }

  $this->getByIdSmt->execute(array(':id' => $id));
  return $this->getByIdSmt->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
 }

}

?>
