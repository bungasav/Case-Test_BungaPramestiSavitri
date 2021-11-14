<?php

class idn_sudoku {

    private $input_array = array();
    private $grids = array();
    private $col_begin = array();

    public function solve_it($arr) {
        while (true) {
            $this->input_array = $arr;

            $this->set_columns();
            $this->set_grid();

            $ops = array();
            foreach ($arr as $k => $row) {
                foreach ($row as $kk => $r) {
                    if ($r == 0) {
                        $pos_vals = $this->posibilities($k, $kk);
                        $ops[] = array(
                            'rowIndex' => $k,
                            'columnIndex' => $kk,
                            'acceptable' => $pos_vals
                        );
                    }
                }
            }

            if (empty($ops)) {
                return $arr;
            }

            usort($ops, array($this, 'sort_ops'));

            if (count($ops[0]['acceptable']) == 1) {
                $arr[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = current($ops[0]['acceptable']);
                continue;
            }

            foreach ($ops[0]['acceptable'] as $value) {
                $tmp = $arr;
                $tmp[$ops[0]['rowIndex']][$ops[0]['columnIndex']] = $value;
                if ($result = $this->solve_it($tmp)) {
                    return $this->solve_it($tmp);
                }
            }

            return false;
        }
    }

    private function set_grid() {
        $grids = array();
        foreach ($this->input_array as $k => $row) {
            if ($k <= 2) {
                $row_num = 1;
            }
            if ($k > 2 && $k <= 5) {
                $row_num = 2;
            }
            if ($k > 5 && $k <= 8) {
                $row_num = 3;
            }

            foreach ($row as $kk => $r) {
                if ($kk <= 2) {
                    $col_num = 1; //1,2,3
                }
                if ($kk > 2 && $kk <= 5) {
                    $col_num = 2; //4,5,6
                }
                if ($kk > 5 && $kk <= 8) {
                    $col_num = 3; //7,8,9
                }
                $grids[$row_num][$col_num][] = $r;
            }
        }
        $this->grids = $grids;
    }

    private function set_columns() { 
        $col_begin = array();
        $i = 1;
        foreach ($this->input_array as $k => $row) {
            $e = 1;
            foreach ($row as $kk => $r) {
                $col_begin[$e][$i] = $r;
                $e++;
            }
            $i++;
        }
        $this->col_begin = $col_begin;
    }

    private function posibilities($k, $kk) {
        $values = array();
        if ($k <= 2) {
            $row_num = 1;
        }
        if ($k > 2 && $k <= 5) {
            $row_num = 2;
        }
        if ($k > 5 && $k <= 8) {
            $row_num = 3;
        }

        if ($kk <= 2) {
            $col_num = 1;
        }
        if ($kk > 2 && $kk <= 5) {
            $col_num = 2;
        }
        if ($kk > 5 && $kk <= 8) {
            $col_num = 3;
        }

        for ($n = 1; $n <= 9; $n++) {
            if (!in_array($n, $this->input_array[$k]) && !in_array($n, $this->col_begin[$kk + 1]) && !in_array($n, $this->grids[$row_num][$col_num])) {
                $values[] = $n;
            }
        }
        shuffle($values);
        return $values;
    }

    private function sort_ops($a, $b) {
        $a = count($a['acceptable']);
        $b = count($b['acceptable']);
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }

    public function getResult() {
        echo "[<br/>";
        foreach ($this->input_array as $k => $row) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[";
            foreach ($row as $kk => $r) {
                echo $r . ', ';
            }
            echo "],<br/>";
        }
        echo "]";
    }

}

$arr = array(
    array(0, 3, 0, 0, 0, 0, 8, 0, 0), 
	array(0, 0, 6, 3, 0, 0, 0, 4, 2), 
	array(2, 0, 8, 6, 7, 0, 3, 0, 5), 
	array(8, 5, 0, 0, 1, 0, 6, 2, 0), 
	array(0, 0, 7, 0, 0, 0, 9, 0, 0), 
	array(0, 4, 9, 0, 5, 0, 0, 1, 8), 
	array(9, 0, 5, 0, 4, 7, 2, 0, 6), 
	array(3, 7, 0, 0, 0, 6, 4, 0, 0), 
	array(0, 0, 1, 0, 0, 0, 0, 7, 0), 
);

$game = new idn_sudoku();
$res = $game->solve_it($arr);
$data = $game->getResult();

