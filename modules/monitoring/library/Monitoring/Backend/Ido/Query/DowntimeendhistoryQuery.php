<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Monitoring\Backend\Ido\Query;

class DowntimeendhistoryQuery extends IdoQuery
{
    protected $columnMap = array(
        'downtimehistory' => array(
            'state_time'    => 'h.actual_end_time',
            'timestamp'     => 'UNIX_TIMESTAMP(h.actual_end_time)',
            'raw_timestamp' => 'h.actual_end_time',
            'object_id'     => 'h.object_id',
            'type'          => "('dt_end')",
            'state'         => '(NULL)',
            'state_type'    => '(NULL)',
            'output'        => "('[' || h.author_name || '] ' || h.comment_data)",
            'attempt'       => '(NULL)',
            'max_attempts'  => '(NULL)',

            'host'                => 'o.name1 COLLATE latin1_general_ci',
            'service'             => 'o.name2 COLLATE latin1_general_ci',
            'host_name'           => 'o.name1 COLLATE latin1_general_ci',
            'service_description' => 'o.name2 COLLATE latin1_general_ci',
            'service_host_name'   => 'o.name1 COLLATE latin1_general_ci',
            'service_description' => 'o.name2 COLLATE latin1_general_ci',
            'object_type'         => "CASE WHEN o.objecttype_id = 1 THEN 'host' ELSE 'service' END"
        )
    );

    public function whereToSql($col, $sign, $expression)
    {
        if ($col === 'UNIX_TIMESTAMP(h.actual_end_time)') {
            return 'h.actual_end_time ' . $sign . ' ' . $this->timestampForSql($this->valueToTimestamp($expression));
        } else {
            return parent::whereToSql($col, $sign, $expression);
        }
    }

    protected function joinBaseTables()
    {
        $this->select->from(
            array('o' => $this->prefix . 'objects'),
            array()
        )->join(
            array('h' => $this->prefix . 'downtimehistory'),
            'o.' . $this->object_id . ' = h.' . $this->object_id . ' AND o.is_active = 1',
            array()
        )->where('h.actual_end_time > ?', '1970-01-02 00:00:00');
        $this->joinedVirtualTables = array('downtimehistory' => true);
    }
}

