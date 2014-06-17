<?php
namespace WScore\Basic\Dba;

class Builder
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Quote
     */
    protected $quote;

    public function buildFrom()
    {
        return $this->quote->quote( $this->query->table );
    }

    public function buildJoin() {
        return '';
    }

    public function buildWhere() {
        return $this->query->where()->__toString();
    }

    public function buildColumn() {
        return implode( ', ', $this->query->columns );
    }

    public function buildGroupBy() {
        return implode( ', ', $this->query->group );
    }

    public function buildOrderBy() {
        $sql = [];
        foreach( $this->query->order as $order ) {
            $sql[] = $order[0]." ".$order[1];
        }
        return implode( ', ', $sql );
    }
}