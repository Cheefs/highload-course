<?php

require_once( 'db.php' );

class SaleBasketRepository {

    /**
     * @return mixed|string
     */
    public static function tableName() {
        return 'b_sale_basket';
    }

    public function find(int $id) {
        $table = static::tableName();
        $sql = "SELECT * FROM {$table} WHERE id = :id";
        return ( new DB())->find($sql, [':id' => $id]);
    }

    public function findAll(array $params = [], $limit = 10000 ) {
        if (count($params)) {
            $str = self::findConditions($params);
        }
        $table = static::tableName();
        $sql = "SELECT * FROM {$table}" . ( $str ?? '' ) . " limit $limit";

        return ( new DB())->findAll($sql, $params);
    }

    private function findConditions($params) {
        $result = ' WHERE ';
        $paramsCount = count($params);
        $counter = 0;
        foreach ($params as $k => $v) {
            $counter++;
            $prefix = $counter < $paramsCount ? ' AND ' : '';
            $result .= "`{$k}` = :{$k}{$prefix}" ;
        }
        return $result;
    }
}
