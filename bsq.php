<?php

class BSQ
{

    public static $bsqRow;
    public static $bsqCol;

    public function __construct($matrix, $rows, $col)
    {
        $this->matrix = $matrix;
        $this->rows = $rows;
        $this->col = $col;
        $this->dp = [];
        //on remplace les obstacles par des zéros
        foreach ($this->matrix as $value) {
            $this->dp[] = str_replace("o", 0, $value);
        }
        $this->result = 0;
        //permet de garder la taille du plus grand carre
        $this->maxSize = 0;
    }

    public function isValidPos($i, $j)
    {
        //si les positions sont hors du tableau, on retourne false
        if ($i < 0 || $j < 0 || $i >= $this->rows || $j >= $this->col) {
            return false;
        }
        return true;
    }

    public function isEmpty($i, $j)
    {
        if (self::isValidPos($i, $j) == false || $this->matrix[$i][$j] === "o") {
            return false;
        }

        if ($i == 0 || $j == 0) {
            $this->dp[$i][$j] =  1;
        } else {
            $this->dp[$i][$j] =  1 + (int)min($this->dp[$i - 1][$j - 1], $this->dp[$i - 1][$j], $this->dp[$i][$j - 1]);
        }
        $this->maxSize =  max($this->maxSize, $this->dp[$i][$j]);
    }

    public function dp()
    {
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->col; $j++) {
                self::isEmpty($i, $j);
            }
        }
        return $this->dp;
    }

    public function getBsq()
    {
        $pattern = "";
        foreach ($this->dp as $key => $row) {
            if (array_search($this->maxSize, $row)) {
                self::$bsqRow = $key;
                self::$bsqCol = array_search($this->maxSize, $row);
                break;
            }
        }
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->col; $j++) {
                if (
                    $i > self::$bsqRow - $this->maxSize
                    && $i <= self::$bsqRow
                    && $j > self::$bsqCol - $this->maxSize
                    && $j <= self::$bsqCol
                ) {
                    $this->matrix[$i][$j] = "X";
                }
            }
        }
        return $this->matrix;
        //return $this->dp;
    }
}

if (isset($argv[1]) && file_exists($argv[1])) {
    $content = file_get_contents($argv[1]);
    $matrix = explode("\n", $content);
    $rows = count($matrix);
    $col = strlen($matrix[0]);
    //on met la derniere ligne du tableau à la meme longueur
    $matrix[$rows - 1] .= "\n";
    //on convertit chaque ligne de string en array
    foreach ($matrix as $key => $row) {
        $matrix[$key] = str_split($row);
    }
    $bsq = new BSQ($matrix, $rows, $col);

    //affichages
    $bsq->dp();
    foreach ($bsq->getBsq() as $row) {
        echo implode(" ", $row) . PHP_EOL;
    }
}
