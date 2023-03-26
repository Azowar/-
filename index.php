interface IDB {
public function connect(string $host = "", string $username = "", string $password = "", string $database = ""): ?static;
public function select(string $query): array;
public function insert(string $table, array $data): bool;
public function update(string $table, int $id, array $data): bool;
public function delete(string $table, int $id): bool;
}
class MySQL implements IDB {
private $connection;
public function connect(
string $host = "",
string $username = "",
string $password = "",
string $database = ""
): ?static {
$this->connection = mysqli_connect($host, $username, $password, $database);
return $this;
}
public function select(string $query): array {
$result = mysqli_query($this->connection, $query);
$rows = array();
if ($result && mysqli_num_rows($result) > 0) {
while ($row = mysqli_fetch_assoc($result)) {
$rows[] = $row;
}
}
return $rows;
}
public function insert(string $table, array $data): bool {
$keys = array_keys($data);
$values = array_map(array($this->connection, 'real_escape_string'), array_values($data));
$query = "INSERT INTO $table (" . implode(', ', $keys) . ") VALUES ('" . implode("', '", $values) . "')";
return mysqli_query($this->connection, $query);
}
public function update(string $table, int $id, array $data): bool {
$set = array();
foreach ($data as $key => $value) {
$set[] = "$key = '" . mysqli_real_escape_string($this->connection, $value) . "'";
}
$set = implode(', ', $set);
$query = "UPDATE $table SET $set WHERE id = $id";
return mysqli_query($this->connection, $query);
}
public function delete(string $table, int $id): bool {
$query = "DELETE FROM $table WHERE id = $id";
return mysqli_query($this->connection, $query);
}
}