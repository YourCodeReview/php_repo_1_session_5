<?php

namespace App\Model;

use CodeIgniter\Model;
use Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use Exception;

class BaseModel extends Model
{

    public function GetRespNull($message)
    {
        return Services::ResponceItem(
            $message,
            []
        );
    }

    public function GetResp($message, $sql, IncomingRequest $request, string $returnType, int $total_record = 0)
    {
        $sqlcount = <<<SQL
        SELECT COUNT(1) FROM ($sql) AS cnt
        SQL;

        $count = $this->db->query($sqlcount)->getFirstRow();

        $page = 1;
        if ((int)$request->getVar('page') > 0) {
            $page = (int)$request->getVar('page');
        }

        $onpage = 10;
        if ((int)$request->getVar('limit') > 0) {
            $onpage = (int)$request->getVar('limit');
        }
        if ((int)$request->getVar('limit') > 100) {
            $onpage = 100;
        }

        $offset = ((int)$page - 1) * (int)$onpage;

        $sql .= <<<SQL
         LIMIT $onpage OFFSET $offset
        SQL;

        $res = $this->db->query($sql)->getResult($returnType);

        return Services::ResponceList(
            $message,
            $res,
            $page,
            $onpage,
            $count->count,
            $total_record
        );
    }

    public function GetRespAll($message, $sql, IncomingRequest $request, string $returnType, int $total_record = 0)
    {
        $sqlcount = <<<SQL
        SELECT COUNT(1) FROM ($sql) AS cnt
        SQL;

        $count = $this->db->query($sqlcount)->getFirstRow();

        $page = 1;
        $onpage = 999;

        $res = $this->db->query($sql)->getResult($returnType);

        return Services::ResponceList(
            $message,
            $res,
            $page,
            $onpage,
            $count->count,
            $total_record
        );
    }

    public function GetRespItem($message, $sql, IncomingRequest $request, string $returnType, mixed $extdata = [])
    {
        $res = $this->db->query($sql)->getFirstRow($returnType);

        return Services::ResponceItem(
            $message,
            $res,
            $extdata
        );
    }

    /// TODO: Check this function before use!!!
    public function GetUniqValues($allowedFilters, IncomingRequest $request, string $returnType) //  Нужно перепроверить/ Зачем пердавать тип?
    {
        if ($request->getVar('field') != "") {
            $FieldName = json_decode($request->getVar('field'), true, 512, JSON_THROW_ON_ERROR);
            $Search = json_decode($request->getVar('search'), true, 512, JSON_THROW_ON_ERROR);
            if ($allowedFilters[$FieldName]) {
                if ($allowedFilters[$FieldName] != "") {
                    $sql = "SELECT * FROM (" . $allowedFilters[$FieldName] . ") main WHERE UPPER(main.text) LIKE UPPER('" . $Search . "')";
                    $this->GetRespAll('Get Uniq fields', $sql, $request, $this->returnType); //Зачем пердавать тип?
                } else {
                    // Для поля " + fieldName + " не используются списки
                }
            } else {
                // Для поля " + fieldName + " нельзя получить уникальные значения
            }
        }
    }

    public function QueryAddSort(string $sql, string $parentTable, IncomingRequest $request)
    {
        if ($request->getVar('sortby') != "") {
            $sql .= " ORDER BY " . $parentTable . "." . $request->getVar('sortby') . " ";
            if ((int)$request->getVar('sortdesc') == 1) {
                $sql .= "DESC ";
            } else {
                $sql .= "ASC ";
            }
        }
        return $sql;
    }

    public function QueryAddFilters(string $sql, IncomingRequest $request, array $avaliblefilters = [])
    {
        if ($request->getVar('filters')) {
            try {
                $filterslist = json_decode($request->getVar('filters'), true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $th) {
                return $sql;
            }
            $filterquery = [];
            foreach ($filterslist as $key => $filter) {
                if (array_key_exists($key, $avaliblefilters)) {
                    $subfilterquery = [];
                    foreach ($filter as $value) {
                        if (str_ends_with($key, '_id')) {
                            $subfilterquery[] = $value;
                        } else {
                            $subfilterquery[] = "UPPER('" . $value . "')";
                        }
                    }
                    if (str_ends_with($key, '_id')) {
                        $filterquery[] = "fmain." . $key . " IN (" . implode(",", $subfilterquery) . ") ";
                    } else {
                        $filterquery[] = "UPPER(fmain." . $key . ") IN (" . implode(",", $subfilterquery) . ") ";
                    }
                }
            }
            if (count($filterquery) > 0) {
                $sql = "SELECT * FROM ( " . $sql . " ) fmain WHERE " . implode(" AND ", $filterquery);
            }
        }
        return $sql;
    }
}
