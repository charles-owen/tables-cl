<?php

use CL\Tables\Config;
use CL\Tables\Table;
use CL\Tables\TableWhere;

/**
 * Test Table example table used for testing the system components.
 */
class TestTable extends Table {
	public function __construct(Config $config) {
		parent::__construct($config, "test");
	}

	public function createSQL() {
		return <<<SQL
CREATE TABLE if not exists $this->tablename (
  id       int(11) NOT NULL AUTO_INCREMENT, 
  data     char(20) NOT NULL, 
  nullable int(11), 
  `unique` int(11) NOT NULL UNIQUE, 
  PRIMARY KEY (id));
SQL;
	}

	public function add($data, $nullable, $unique) {
		$sql = <<<SQL
insert into $this->tablename(data, nullable, `unique`)
values(?, ?, ?)
SQL;

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([$data, $nullable, $unique]);
		return $this->pdo->lastInsertId();
	}

	public function get() {
		$sql = <<<SQL
select * from $this->tablename
order by `unique`
SQL;

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function query(array $params) {
		$where = new TableWhere($this);

		if(isset($params['before'])) {
			$where->append('`unique`<?', $params['before'], \PDO::PARAM_INT);
		}

		if(isset($params['after'])) {
			$where->append('`unique`>?', $params['after'], \PDO::PARAM_INT);
		}

		if(isset($params['id'])) {
			$where->append('id=?', $params['id'], \PDO::PARAM_INT);
		}

		if(isset($params['data'])) {
			$where->append('data=?', $params['data']);
		}

		$sql = <<<SQL
select * from $this->tablename
$where->where
order by `unique`
SQL;

		if(isset($params['limit'])) {
			$sql .= "\nlimit ?";
			$where->append(null, intval($params['limit']), \PDO::PARAM_INT);
		}

		echo $where->sub_sql($sql);
		$result = $where->execute($sql);
		return $result->fetchAll(\PDO::FETCH_ASSOC);

	}
}
