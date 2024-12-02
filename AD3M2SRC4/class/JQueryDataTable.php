<?php

/**
 * Clase para usar el dataTable de jQuery
 * https://datatables.net
 */
class JQueryDataTable {

    public $options = array();

    /**
     * 
     * @var string example -> SET SESSION group_concat_max_len=15360
     */
    public $setBeforeQuery = null;

    function __construct($options = null) {
        if ($options) {
            $this->options = $options + $this->options;
        }
    }

    /*
      public function paginate($options = null) {

      }
     */

    /**
     * Creates and executes the query
     *
     * @param query string
     * @param columns 
     * @param conds = defaults conditions
     * 					"data":{ "conds": {"AND":"table.field = 1" }}
     * @return String
     * @access private
     */
    private function _createConditions($qString = null, $cols = null, $conds = null) {
        $conditions = '';
        $first_cond = false;

        if (!empty($qString)) {

            // Obtenemos las columnas por las que se puede buscar
            $i = 0;
            foreach ($cols as $AndOr => $v) {
                if (!empty($v['data'])) {
                    if (!empty($v['searchable'])) {
                        $m = explode('.', $v['data']);
                        if (count($m) > 0) {
                            $m = array_reverse($m);
                            if (!$first_cond) {
                                $conditions .= ' WHERE (' . $m[1] . '.' . $m[0];
                                $first_cond = true;
                            } else
                                $conditions .= ' OR ' . $m[1] . '.' . $m[0];

                            $conditions .= ' LIKE "%' . $qString . '%"';
                        }
                        else {
                            if (!$first_cond) {
                                $conditions .= ' WHERE (' . $m[0];
                                $first_cond = true;
                            } else
                                $conditions .= ' OR ' . $m[0];

                            $conditions .= ' LIKE "%' . $qString . '%"';
                        }
                    }
                }
            }
        }

        if ($conditions != '') {
            $conditions.=')';
        }
//        Dbg::data($conditions);
        $i = 0;
        if (!empty($conds)) {
            //Dbg::pd($conds);
//            foreach ($conds as $k => $v) {
//                if (!$first_cond) {
//                    $conditions .= ' WHERE ' . $v;
//                    $first_cond = true;
//                } else
//                    $conditions .= ' ' . $k . ' ' . $v . ' ';
//            }
            foreach ($conds as $AndOr => $cndtns) {
                foreach ($cndtns as $cndtn) {
                    if (!$first_cond) {
                        $conditions .= ' WHERE ' . $cndtn;
                        $first_cond = true;
                    } else
                        $conditions .= ' ' . $AndOr . ' ' . $cndtn . ' ';
                }
            }
        }
        //Dbg::pd($conditions);
        return $conditions;
    }

    /**
     * Creates and executes the query
     *
     * @param fields list i.e. Relationship.Model.field
     * @return String
     * @access private
     */
    private function _createJoins($fields = null) {
        $joins = '';
        $fields = explode(',', $fields);
        $models = array();
        $first_table = false;

        // Obtenemos los modelos
        foreach ($fields as $k => $v) {

            $m = explode('.', $v);

            // Muchos a muchos
            if (count($m) == 3) {
                if (!in_array(trim($m[0]), $models)) {
                    $models[] = trim($m[0]);
                    if (!$first_table) {
                        $joins .= ' FROM ' . $m[0] . ' ';
                        $first_table = true;
                    }
                }
                if (!in_array(trim($m[1]), $models)) {
                    $models[] = trim($m[1]);
                    $joins .= ' JOIN ' . $m[1] . ' ON ' . $m[1] . '.id = ' . $m[0] . '.' . strtolower($m[1]) . '_id';
                }
            }
            // Una sola tabla
            elseif (count($m) == 2) {
                if (!in_array(trim($m[0]), $models)) {
                    $models[] = trim($m[0]);
                    if (!$first_table) {
                        $joins .= ' FROM ' . $m[0] . ' ';
                        $first_table = true;
                    }
                }
            }
            /*
              $m = explode('.', $v);
              $i=0;
              while($elem = array_shift($stack)) {
              // Descartamos el campo
              if($i > 0) {
              if(!in_array($elem, $models))
              $model[] = $elem;
              }
              $i++;
              } */
            //Dbg::data($v);
        }
        return $joins;
    }

    /**
     * Creates and executes the query
     *
     * @param datatable draw
     * @param query string
     * @return JSON object
     * @access public
     */
    public function _search($draw, $qString = null, $order = null, $columns = null, $limit = null, $offset = null, $conds = null) {
        $completeFields = $fields = '';
        $i = 0;
        foreach ($columns as $k => $v) {
            if (!empty($v['data'])) {
                $m = explode('.', $v['data']);
                if (count($m) > 0) {
                    $m = array_reverse($m);
                    $fields .= ($i == 0) ? ($m[1] . '.' . $m[0]) : (', ' . $m[1] . '.' . $m[0]);
                    $completeFields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                } else {
                    $fields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                    $completeFields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                }
                $i++;
            }
        }
        // Create search string
        $sql = 'SELECT sql_calc_found_rows ' . $fields;
        // Pagging search string
        $totalCount = 'SELECT found_rows() AS total ';
        $joins = $this->_createJoins($completeFields);
        $conditions = $this->_createConditions($qString, $columns, $conds);
        $sql .= $joins . $conditions;
        // Set the column order
        $sql .= ' ORDER BY ' . $order;
        // Set the limit and offset
//        $sql .= ' LIMIT ' . $offset . ',' . $limit . '; ';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $offset . ',' . $limit . ';';
        } else {
            $sql .= ' ; ';
        }
//      print_r($sql);
        // Get total records
        $dt = new DataBase();
        $resultset = $dt->multiQuery($sql . $totalCount);
//        Dbg::data($sql . $sql_pagging);
//        Dbg::data($resultset);
        $total_datos = 0;
        while ($data = $resultset[1]->fetch_assoc()) {
            $total_datos = $data['total'];
        }
        // Create array	
        $results = array();
        $i = 0;
        while ($data = $resultset[0]->fetch_assoc()) {
            $arr = array();
            //Dbg::data($data);
            // Get field information for all columns
            $info = $resultset[0]->fetch_fields();
            //Dbg::data($info);
            $id_flag = true;
            foreach ($info as $val) {
                //TODO verify same name of different tables
                //Dbg::data($columns[$i]['data']);
                $results[$i][$val->table][$val->name] = utf8_encode($data[$val->name]);
                //Dbg::data($val->name);
                if (strpos($val->name, 'id') > 0 && $id_flag) {
                    //Dbg::data("entraa");
                    $results[$i]["DT_RowId"] = 'row_' . $data[$val->name];
                    $id_flag = false;
                }
                //die;
            }
            //Dbg::data($arr);
            // TODO prueba m:m
            /* $arr = array(
              "RegionUser" =>array(
              "id"=>1,
              "Region" => array(
              "id" => 2,
              "name" => "Region 1"
              ),
              "User" => array(
              "id" => 3,
              "name"=> "Eddin Gustavo",
              "lastname1"=> "Medina",
              "lastname2"=> "Cid",
              "phone"=> "12345678"
              )
              )
              ); */
            //$results[] = $arr;
            $i++;
        }
        //Dbg::data(json_encode($results));
        //Dbg::data($resultset);
        //Dbg::data($resultset[1]);
        $data = array(
            "draw" => $_POST['draw'],
            "recordsFiltered" => $total_datos,
            "recordsTotal" => $resultset[0]->num_rows,
            "data" => $results);
        return $data;
        /*
          if(!empty($qString)) {
          $arr = array(
          "draw"=> $_POST['draw'],
          "recordsTotal"=> 1,
          "recordsFiltered"=> 1,
          "data"=> array(
          "first_name"=> "Airi",
          "last_name"=> "Satou",
          "position"=> "Accountant",
          "office"=> "Tokyo",
          "start_date"=> "28th Nov 08",
          "salary"=> "$162,700"
          )
          );
          return $arr;
          }
          return '{
          "draw": '.$_POST['draw'].',
          "recordsTotal": 57,
          "recordsFiltered": 57,
          "data": [
          {
          "RegionUser": {
          "id": 1,
          "Region": {
          "id": 1,
          "name": "Region 1"
          },
          "User": {
          "id": 3,
          "name": "Eddin Gustavo",
          "lastname1": "Medina",
          "lastname2": "Cid",
          "phone": "12345678"
          }
          }
          },
          {
          "RegionUser": {
          "id": 16,
          "Region": {
          "id": 17,
          "name": "Region 17"
          },
          "User": {
          "id": 3,
          "name": "Alberto",
          "lastname1": "Moreno",
          "lastname2": "",
          "phone": "76543223"
          }
          }
          }
          ]
          }';
         */
    }

    /**
     * Executes the query
     *
     * @param datatable draw
     * @param query string
     * @return JSON object
     * @access public
     */
    public function _searchQuery($draw, $qString = null, $query = null, $columns = null, $aliasTable = 'List', $conds = null, $order = null, $limit = null, $offset = null) {
        $completeFields = $fields = '';
        $i = 0;

        if (is_array($columns)) {
          foreach ($columns as $k => $v) {
            if (!empty($v['data'])) {
                $m = explode('.', $v['data']);
                if (count($m) > 0) {
                    $m = array_reverse($m);
                    $fields .= ($i == 0) ? ($m[1] . '.' . $m[0]) : (', ' . $m[1] . '.' . $m[0]);
                    $completeFields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                } else {
                    $fields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                    $completeFields .= ($i == 0) ? $v['data'] : ', ' . $v['data'];
                }
                $i++;
            }
          }
        }

        //$conditions = '';
        $conditions = $this->_createConditions($qString, $columns, $conds);
//        Dbg::pd($query);
        // Create search string
        $sql = 'SELECT sql_calc_found_rows * FROM (' . $query . ') ' . $aliasTable . '  ' . $conditions . ' ' . $order;
        $totalCount = ' SELECT found_rows() AS total; ';
//        Dbg::data($offset);
//        Dbg::data($limit);
        // Set the limit and offset
        if ($limit > 0) {
            $sql .= ' LIMIT ' . $offset . ',' . $limit . ';';
        } else {
            $sql .= ' ; ';
        }
//        $sql .= ' LIMIT ' . $offset . ',' . $limit . ';';
        //         print_r($sql);
//        Dbg::data($sql);
//        Dbg::pd($sql);
        // Get total records
        $dt = new DataBase();
//        $eddin = $dt->simpleQuery();
//        Dbg::data($this->setBeforeQuery);
        $resultset = $dt->multiQuery($sql . $totalCount, $this->setBeforeQuery);
//        Dbg::data($sql_pagging . $sql);
//        Dbg::data($resultset);
//        // Create array	
        $total_datos = 0;
        $data = $resultset[1];
//        Dbg::data($data);
        $total_datos = $data[0]['total'];

        $results = array();
        $i = 0;
//        while ($data = $resultset[0]->fetch_assoc()) {
        foreach ($resultset[0] as $data) {
            $arr = array();
//            Dbg::data($data);
            if ($aliasTable == '') {
                // Get field information for all columns
                $info = $resultset[0]->fetch_fields();
//                Dbg::data($info);
                $id_flag = true;
                foreach ($info as $val) {
                    //TODO verify same name of different tables
                    //Dbg::data($columns[$i]['data']);
                    $results[$i][$val->table][$val->name] = utf8_encode($data[$val->name]);
                    //Dbg::data($val->name);
                    if (strpos($val->name, 'id') > 0 && $id_flag) {
                        //Dbg::data("entraa");
                        $results[$i]["DT_RowId"] = 'row_' . $data[$val->name];
                        $id_flag = false;
                    }
                    //die;
                }
            } else {
                $id_flag = true;
                foreach ($data as $key => $value) {
                    $results[$i][$aliasTable][$key] = utf8_encode($value);
                    if ($key == 'id' && $id_flag) {
                        //Dbg::data("entraa");
                        $results[$i]["DT_RowId"] = 'row_' . $i;
                        $id_flag = false;
                    }
                }
            }
            //Dbg::data($arr);
            $i++;
        }
        //Dbg::data(json_encode($results));
        $data = array(
            "draw" => !empty($_POST['draw'])?$_POST['draw']:'',
            "recordsFiltered" => $total_datos,
            "recordsTotal" => count($resultset[0]),
            "data" => $results);
//        Dbg::data($data);
        return $data;
    }

    /**
     * Load the default information
     *
     * @param datatable $_POST
     * @return JSON object
     * @access public
     */
    public function loadInfo($info) {
        $draw = 1;
        $search = $order = $columns = $conds = '';
        $len = 10;
        $start = 0;
        $result = '';
        //die(Dbg::data($_POST));
        if (isset($info['draw']))
            $draw = $info['draw'];
        if (!empty($info['search']['value']))
            $search = $info['search']['value'];
        if (!empty($info['order'])) {
            $col = $_POST['order'][0]['column'];
            $info['columns'][$col]['data'];
            $order = $info['columns'][$col]['data'] . " " . $_POST['order'][0]['dir'];
        }
        if (!empty($info['columns'])) {
            $columns = $info['columns'];
        }
        if (!empty($info['length'])) {
            $len = $info['length'];
        }
        if (!empty($info['start'])) {
            $start = $info['start'];
        }
        if (!empty($info['conds'])) {
            $info['conds'] = (array) $info['conds'];
            foreach ($info['conds'] as $key => $value) {
                $conds[$key] = (array) $value;
            }
        }
        //Dbg::data($conds);
        $result = $this->_search($draw, $search, $order, $columns, $len, $start, $conds);
        die(json_encode($result));
    }

    public function queryInfo($info, $jquery = true) {

        $draw = 1;
        $search = $order = $columns = $query = $conds = "";
        $aliasTable = 'list';
        $limit = 1300000000;
        $offset = 0;
        $result = '';

        if (isset($info['draw']))
            $draw = $info['draw'];
        if (!empty($info['search']['value']))
            $search = $info['search']['value'];
        if (!empty($info['order'])) {
            //$i_order = $_POST['order'][0];
            $dataOrders = array();
            foreach ($info['order'] as $orders) {
                if (isset($info['columns'][$orders['column']]['data']))
                    $dataOrders[] = $info['columns'][$orders['column']]['data'] . ' ' . $orders['dir'];
            }
            if (!empty($dataOrders)) {
                $order = ' ORDER BY ' . implode(', ', $dataOrders);
            }
        }
        if (!empty($info['columns'])) {
            $columns = $info['columns'];
        }
        if (!empty($info['length'])) {
            $limit = $info['length'];
        }
        if (!empty($info['start'])) {
            $offset = $info['start'];
        }
        if (!empty($info['query'])) {
            $query = $info['query'];
        }
        if (!empty($info['aliasTable'])) {
            $aliasTable = $info['aliasTable'];
        }

//        Dbg::data($columns);

        $result = $this->_searchQuery($draw, $search, $query, $columns, $aliasTable, $conds, $order, $limit, $offset);
        if ($jquery) {
            die(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_HEX_AMP));
        }
        
        return $result;
    }

    public function queryData($info) {

        $draw = 1;
        $search = $order = $columns = $query = $conds = "";
        $aliasTable = 'list';
        $limit = 10;
        $offset = 0;
        $result = '';

        if (isset($info['draw']))
            $draw = $info['draw'];
        if (!empty($info['search']['value']))
            $search = $info['search']['value'];
        if (!empty($info['order'])) {
            //$i_order = $_POST['order'][0];
            $dataOrders = array();
            foreach ($info['order'] as $orders) {
                if (isset($info['columns'][$orders['column']]['data']))
                    $dataOrders[] = $info['columns'][$orders['column']]['data'] . ' ' . $orders['dir'];
            }
            if (!empty($dataOrders)) {
                $order = ' ORDER BY ' . implode(', ', $dataOrders);
            }
        }
        if (!empty($info['columns'])) {
            $columns = $info['columns'];
        }
        if (!empty($info['length'])) {
            $limit = $info['length'];
        }
        if (!empty($info['start'])) {
            $offset = $info['start'];
        }
        if (!empty($info['query'])) {
            $query = $info['query'];
        }
        if (!empty($info['aliasTable'])) {
            $aliasTable = $info['aliasTable'];
        }
        $result = $this->_searchQuery($draw, $search, $query, $columns, $aliasTable, $conds, $order, $limit, $offset);
        return $result;
    }

}
