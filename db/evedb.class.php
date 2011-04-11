<?PHP

require_once(dirname(dirname(__FILE__)) . '/model/ship.php');

class EveDB {

 private $pdo;

 private $handlers;

 public function __construct($connection = null) {
  $this->pdo = new PDO($connection == null ? 'sqlite:' . dirname(__FILE__) . '/inc110-sqlite3-v1.db' : $connection);

  $this->handlers = array(
   'items' => new ItemsTableHandler($this->pdo),
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

?>
