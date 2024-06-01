<?php

namespace App;

use App\Exceptions\ModelNotFoundException;

abstract class Model
{
    private DB $db;
    private const METHOD_SELECT = "select";
    private const METHOD_UPDATE = "update";
    private const METHOD_DELETE = "delete";

    /**
     * Db information
     */
    protected $table = "";
    private $database = "";

    /**
     * query filter
     */
    private array $select = ['*'];
    private array $filters = [];
    private array $operatorFilters = [];
    private array $joins = [];

    public function __construct()
    {
        $table = $this->table;
        $this->db = App::db();
        $this->database = $this->db->config['database'];
    }

    public function create(array $data)
    {
        $database = $this->database;
        $table = $this->table;
        $fields = "";
        $values = "";

        foreach ($data as $key => $value) {
            if (array_key_first($data) != $key) {
                $fields .= ", $key";
                $values .= ", :$key";
            } else {
                $fields .= "$key";
                $values .= ":$key";
            }
        }

        $stat = $this->db->prepare(
            "INSERT INTO $database.$table ($fields) VALUES ($values)"
        );

        $stat->execute($data);

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $database = $this->database;
        $table = $this->table;
        $fields = "";
        $values = [];

        foreach ($data as $key => $value) {
            $values[] = $value;
            if (array_key_first($data) != $key) {
                $fields .= ", $key = ?";
            } else {
                $fields .= "$key = ?";
            }
        }

        $values[] = $id;


        $stat = $this->db->prepare(
            "UPDATE $database.$table SET $fields WHERE $table.id = ?"
        );

        return $stat->execute($values);
    }

    public function delete($id)
    {
        $stat = $this->db->prepare(
            $this->prepareDeleteQuery($id)
        );

        return $stat->execute(
            array_merge(
                $this->filters,
                [
                    'id' => $id
                ]
            )
        );
    }

    public function find($id)
    {
        $database = $this->database;
        $table = $this->table;
        $stat = $this->db->prepare(
            "SELECT * FROM $database.$table
            WHERE $table.id = ?"
        );

        $stat->execute([$id]);

        return $stat->fetch();
    }

    public function first()
    {
        $stat = $this->db->prepare(
            $this->prepareSelectQuery()
        );

        $stat->execute($this->filters);

        $result = $stat->fetch();

        return $result;
    }

    public function findOrFail(int $id)
    {
        $result = $this->first($id);

        if (!$result) {
            throw new ModelNotFoundException("Blog is not found", 404);
        }

        return $result;
    }

    public function findById(int $id)
    {
    }

    public function getAll(string $sortColumn = "id", string $orderBy = "desc")
    {
        $stat = $this->db->prepare($this->prepareSelectQuery($sortColumn, $orderBy));
        $stat->execute($this->filters);

        return $stat->fetchAll();
    }

    public function paginateAll(
        array $select = ['*'],
        int $page = 1,
        int $limit = 20,
        string $sortColumn = "id",
        string $orderBy = "desc"
    ) {
        $from = 0;

        if ($page > 1) {
            $from = ($page - 1) * $limit;
        }

        $stat = $this->db->prepare($this->prepareSelectQuery($select, $sortColumn, $orderBy, $from, $limit));
        $stat->execute(
            array_merge(
                $this->filters,
                [
                    'offset' => $from,
                    'limit' => $limit
                ]
            )
        );

        return $stat->fetchAll();
    }

    public function select(array $select = ['*'])
    {
        $this->select = $select;
        return $this;
    }

    public function when($value, $callback, $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    public function where(string $column, $value): self
    {
        $this->filters[$column] = $value;
        $this->operatorFilters[$column] = "=";
        return $this;
    }

    public function whereLike(string $column, $value): self
    {
        return $this;
    }

    public function whereNotEqual(string $column, $value): self
    {
        $this->filters[$column] = $value;
        $this->operatorFilters[$column] = "<>";
        return $this;
    }

    public function whereNotNull(string $column, $value): self
    {
        return $this;
    }

    public function join(string $joinTable, string $left, string $operator = "=", string $right)
    {
        $database = $this->database;
        $this->joins[] = [
            'left' => $left,
            'right' => $right,
            'join' => "INNER JOIN $database.$joinTable",
            'operator' => $operator
        ];

        return $this;
    }

    private function prepareSelectQuery(
        string $sortColumn = "id",
        string $orderBy = "desc",
        int $offset = null,
        int $limit = null
    ) {
        $database = $this->database;
        $table = $this->table;

        $whereClause = $this->getWhereClause($this->filters);

        $select = implode(", ", $this->select);

        $joinString = "";

        if ($this->joins && count($this->joins) > 0) {
            foreach ($this->joins as $value) {
                $joinTable = $value['join'];
                $left = $value['left'];
                $right = $value['right'];
                $operatorJoin = $value['operator'];
                $joinString .= "$joinTable ON $left" . $operatorJoin . "$right";
            }
        }

        $query = "SELECT $select FROM $database.$table $joinString $whereClause ORDER BY $table.$sortColumn $orderBy";

        if (!is_null($offset) && !is_null($offset) && $offset >= 0 && $limit >= 0) {
            $query .= ' LIMIT :limit OFFSET :offset ';
        }


        // var_dump($query);
        // exit;

        return $query;
    }

    private function prepareDeleteQuery($id = null)
    {
        $database = $this->database;
        $table = $this->table;

        $filters = $this->filters;

        if ($id) {
            $filters['id'] = $id;
        }

        $whereClause = $this->getWhereClause($filters);

        $query = "DELETE FROM $database.$table $whereClause";

        return $query;
    }

    private function getWhereClause(array $filters)
    {
        $whereFields = "";

        foreach ($filters as $key => $value) {
            $operator = $this->operatorFilters[$key] ?? "=";
            if (array_key_first($filters) != $key) {
                $whereFields .= " AND $key $operator :$key";
            } else {
                $whereFields .= "WHERE $key $operator :$key";
            }
        }

        return $whereFields;
    }
}
