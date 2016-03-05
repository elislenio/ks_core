<?php
namespace Ks\CoreBundle\Services;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types;

/**
 * Crud1
 *
 * Service
 */
class Crud1
{
	private $conn;
	private $qb;
	private $conf;
	private $csv_callback;
	private $list_callback;
	private $parameters;
	
	public function __construct()
    {
    }
	
	private function getFilterStr($field, $type, $condition, $insensitive=false)
	{
		switch ($type)
		{
			case 'text':
				if ($insensitive) $field = 'upper(' . $field . ')';
				break;
		}
		
		switch ($condition)
		{
			case 'eq':
			case 'is':
				return $this->qb->expr()->eq($field, '?');
				break;
			case 'ne':
				return $this->qb->expr()->neq($field, '?');
				break;
			case 'gt':
				return $this->qb->expr()->gt($field, '?');
				break;
			case 'ge':
				return $this->qb->expr()->gte($field, '?');
				break;
			case 'lt':
				return $this->qb->expr()->lt($field, '?');
				break;
			case 'le':
				return $this->qb->expr()->lte($field, '?');
				break;
			case 'bt':
				return $field . ' BETWEEN ? AND ?';
				break;
			case 'ends':
			case 'begins':
			case 'contains':
				return $this->qb->expr()->like($field, '?');
				break;
			case 'isnull':
				return $this->qb->expr()->isNull($field);
				break;
			case 'isnotnull':
				return $this->qb->expr()->isNotNull($field);
				break;
			default:
				return $this->qb->expr()->eq($field, '?');
		}
	}
	
	private function addParameter($value, $type, $condition, $insensitive=false)
	{
		if ($value === false)	return;
		
		$doctrine_type = null;
		
		switch ($type)
		{
			case 'text':
				if ($insensitive) $value = strtoupper($value);
				break;
			case 'number':
				$value = (int) $value;
				break;
			case 'datetime':
				$value = new \DateTime($value);
				$doctrine_type = Types\Type::DATETIME;
				break;
		}
		
		switch ($condition)
		{
			case 'ends':
				$value = '%'.$value;
				break;
			case 'begins':
				$value = $value.'%';
				break;
			case 'contains':
				$value = '%'.$value.'%';
				break;
			case 'isnull':
			case 'isnotnull':
				return;
				break;
		}
		
		$this->parameters[] = array($value, $doctrine_type);
	}
	
	private function getQuery($type, $request)
	{
		$this->parameters = array();
		
		// Makes associative array
		$extra_search = $request->get('extra_search');
		$search = array();
		
		if ($extra_search)
			foreach ( $extra_search as $e)
				$search[$e['name']] = $e['value'];
		
		// Query builder
		$this->qb = $this->conn->createQueryBuilder();
		
		// Select
		if ($type == 'count')
			$this->qb->select('count(*)');
		else
			$this->qb->select(implode(',', $this->conf['sql']['select']));
		
		// From
		$this->qb->from($this->conf['sql']['from'][0], $this->conf['sql']['from'][1]);
		
		if (isset($this->conf['sql']['innerJoin']))
			foreach ($this->conf['sql']['innerJoin'] as $j)
				$this->qb->innerJoin($j[0], $j[1], $j[2], $j[3]);
		
		// Where
		
		if (isset($this->conf['sql']['where']))
			foreach ($this->conf['sql']['where'] as $w)
				$this->qb->andWhere($w);
		
		if (isset($this->conf['sql']['where_param']))
			foreach ($this->conf['sql']['where_param'] as $p)
				$this->parameters[] = array($p, null);
			
		// Only process allowed filters
		foreach ($this->conf['filters'] as $k => $v)
		{
			(isset($v['value'])) ? $value = $v['value'] : $value = false;
			(isset($v['condition'])) ? $condition = $v['condition'] : $condition = false;
			
			if (array_key_exists('f_'.$k, $search))
				$value = $search['f_'.$k];
			
			if (array_key_exists('f_c_'.$k, $search))
				$condition = $search['f_c_'.$k];
			
			if (strlen($value) > 0)
			{
				$f_where = $this->getFilterStr($v['field'], $v['type'], $condition, true);
				$this->qb->andWhere($f_where);
				
				if (isset($v['value_callback']) && is_callable($v['value_callback']) )
					$value = call_user_func($v['value_callback'], $value);
				
				if ($condition == 'bt')
				{
					$this->addParameter($value[0], $v['type'], $condition, true);
					$this->addParameter($value[1], $v['type'], $condition, true);
				}
				else
				{
					$this->addParameter($value, $v['type'], $condition, true);
				}
			}
		}
		
		foreach ($this->parameters as $k=>$v)
			$this->qb->setParameter($k, $v[0], $v[1]);
			
		$columns = $request->get('columns');
		
		if ($type == 'full')
		{
			// Order by
			if ($request->get('order'))
			foreach ($request->get('order') as $o)
			{
				// Prevents SQL injection to the order by "direction"
				// http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#order-by-clause
				if ( mb_strtoupper($o['dir']) == 'DESC' )
					$dir = 'desc';
				else
					$dir = 'asc';
				
				$this->qb->addOrderBy($columns[$o['column']]['data'], $dir);
			}
		
			// Pagination
			if ($request->get('length') != -1)
				$this->qb
					->setFirstResult($request->get('start'))
					->setMaxResults($request->get('length'));
		}
	}
	
	/**
     * Outputs the csv data 
     *
     */
    public function outputCsv()
	{
		$results = $this->qb->execute();
		
		$output = fopen('php://output', 'w+');
 
		// UTF-8 BOM (for excel)
		$utf8_with_bom = chr(239) . chr(187) . chr(191);
		fwrite($output, $utf8_with_bom);
		
		$headings = $this->conf['csv_columns'];
		
		// output the column headings
		$csvheadings = array();
		
		foreach ($headings as $h)
			$csvheadings[] = $h['title'];
			
		fputcsv($output, $csvheadings, ';');

		// TODO: get this from config:
		// Fetch the data queried from database
		$fetch_limit = 65000;
		$i = 0;
		
		// output the rows
		while ($i < $fetch_limit && $row = $results->fetch() )
		{
			$record = array();
			
			foreach ($headings as $k => $h)
			{
				$field = $h['field'];
				
				if (array_key_exists($field, $row))
				{
					if ($this->csv_callback && is_callable($this->csv_callback) )
						$record[] = call_user_func($this->csv_callback, $k, $field, $row[$field]);
					else
						$record[] = $row[$field];
				}
				else
				{
					$record[] = '';
				}
			}
			
			fputcsv($output, $record, ';');
			$i++;
		}
		
		fclose($output);
	}
	
	public function getDeniedResponse()
	{
		$response = array();
		$response['server_msg'] = 'Acceso denegado.';
		$response['data'] = array();
		
		return new Response(
			json_encode($response),
			200,
			array('Content-Type' => 'application/json')
		);
	}
	
	public function getList(Connection $conn, $request, $conf, $list_callback=false)
	{
		$this->conn = $conn;
		$this->conf = $conf;
		$this->list_callback = $list_callback;
		
		$response = array();
		
		if ($request->get('draw'))
			$response['draw'] = (int) $request->get('draw');
		
		$this->getQuery('full', $request);
		$records = $this->qb->execute()->fetchAll();
		
		$this->getQuery('count', $request);
		$count = $this->qb->execute()->fetchColumn(0);
		
		$response['recordsTotal'] = $count;
		$response['recordsFiltered'] = $count;
		
		if ($this->list_callback && is_callable($this->list_callback) )
			$response['data'] = call_user_func($this->list_callback, $records);
		else
			$response['data'] = $records;
		
		return new Response(
            json_encode($response),
            200,
            array('Content-Type' => 'application/json')
        );
	}
	
	public function exportCsv(Connection $conn, $request, $conf, $csv_callback=false)
	{
		$this->conn = $conn;
		$this->conf = $conf;
		$this->getQuery('export', $request);
		$this->csv_callback = $csv_callback;
		
		$response = new StreamedResponse();
		$response->setCallback(array($this, 'outputCsv'));
		
		$d = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			$this->conf['csv_filename']
		);

		$response->headers->set('Content-Disposition', $d);
		$response->send();
		return $response;
	}
	
	public function getExtraSearchValue($request, $name)
	{
		$extra_search = $request->get('extra_search');
		if ($extra_search)
			foreach ( $extra_search as $e)
				if ($e['name'] == $name)
					return $e['value'];
	}
}
