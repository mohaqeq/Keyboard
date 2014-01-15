<?php

/**
 * Description of KeyboardTraverser
 *
 * @author Mohaqeq
 */
class KeyboardTraverser {

    private $keyboard;
    private $movmentRules;
    
    /**
     * Get an array of key sequences for each character in input string
     * 
     * @param string $sentence
     * @return array
     */
    public function getKeySequence($sentence){
        $chars = str_split($sentence);
        $keySequences = array(array("E"));
        for ($i = 0; $i < count($chars) - 1; $i++) {
            $paths = $this->getPaths($chars[$i], $chars[$i + 1]);
            $keys = array();
            foreach ($paths as $path) {
                $pathChars = str_split($path);
                $keySeq = "";
                for ($j = 0; $j < count($pathChars) - 1; $j++) {
                    $fPos = $this->getPos($pathChars[$j]);
                    $sPos = $this->getPos($pathChars[$j + 1]);
                    $x = $fPos[0] - $sPos[0];
                    $y = $fPos[1] - $sPos[1];
                    if ($x == -1 || $x == 3){
                        $keySeq .= "D";
                    }elseif ($x == 1 || $x == -3){
                        $keySeq .= "U";
                    }elseif ($y == -1 || $y == -2 || $y == -8 || $y == 24 || $y == 25){
                        $keySeq .= "R";
                    }elseif ($y == 1 || $y == 2 || $y == 8 || $y == -24 || $y == -25){
                        $keySeq .= "L";
                    }
                }
                $keySeq .= "E";
                $keys[] = $keySeq;
            }
            $keySequences[] = $keys;
        }
        return $keySequences;
    }

    /**
     * Get an array of refiend paths between to characters
     * 
     * @param string $from
     * @param string $to
     * @return array
     */
    private function getPaths($from, $to) {
        return $this->enforceRules($this->getSimplePaths($from, $to));
    }

    /**
     * Get refined paths from input paths
     * 
     * @param array $paths
     * @return array
     */
    private function enforceRules($paths) {
        $minLenght = 100;
        $newPaths = array();
        foreach ($paths as $path) {
            $newPath = $path;
            for ($i = 0; $i < count($this->movmentRules); $i++) {
                $newPath = str_replace($this->movmentRules[$i][0], $this->movmentRules[$i][1], $newPath);
            }
            $newPathLen = strlen($newPath);
            if ($newPathLen < $minLenght) {
                $minLenght = $newPathLen;
                $newPaths = array();
                $newPaths[] = $newPath;
            } elseif ($newPathLen == $minLenght) {
                $newPaths[] = $newPath;
            }
        }
        return array_unique($newPaths);
    }

    /**
     * Get all posible paths between to characters
     * 
     * @param string $from
     * @param string $to
     * @return array
     */
    private function getSimplePaths($from, $to) {
        $fromPos = $this->getPos($from);
        $toPos = $this->getPos($to);
        $rdist = $toPos[0] - $fromPos[0];
        $rowDist = abs($rdist) < 2 ? $rdist : $rdist - $this->sign($rdist) * 4;
        $cdist = $toPos[1] - $fromPos[1];
        $colDist = abs($cdist) < 13 ? $cdist : $cdist - $this->sign($cdist) * 26;
        $paths = array();
        $rowDir = $this->sign($rowDist);
        $colDir = $this->sign($colDist);
        $absColDist = abs($colDist);
        $reverseCol = $absColDist == 13 ? -2 : 0;
        if ($rowDist == 0) {
            for ($rc = 1; $rc > $reverseCol; $rc -= 2) {
                $path = "";
                for ($c = 0; $c <= $absColDist; $c++) {
                    $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                    $path .= $this->getKey(array($fromPos[0], $col));
                }
                $paths[] = $path;
            }
        } elseif (abs($rowDist) == 1) {
            for ($cc = 0; $cc <= $absColDist; $cc++) {
                for ($rc = 1; $rc > $reverseCol; $rc -= 2) {
                    $r = 0;
                    $path = "";
                    for ($c = 0; $c <= $absColDist; $c++) {
                        if ($c == $cc) {
                            $row = $this->mod($fromPos[0] + $rowDir * $r, 4);
                            $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                            $path .= $this->getKey(array($row, $col));
                            $r = 1;
                        }
                        $row = $this->mod($fromPos[0] + $rowDir * $r, 4);
                        $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                        $path .= $this->getKey(array($row, $col));
                    }
                    $paths[] = $path;
                }
            }
        } else {
            for ($ri = -1; $ri < 2; $ri += 2) {
                for ($ccc = 0; $ccc <= $absColDist; $ccc++) {
                    for ($cc = $ccc; $cc <= $absColDist; $cc++) {
                        for ($rc = 1; $rc > $reverseCol; $rc -= 2) {
                            $r = 0;
                            $path = "";
                            for ($c = 0; $c <= $absColDist; $c++) {
                                if ($c == $ccc) {
                                    $row = $this->mod($fromPos[0] + $rowDir * $r, 4);
                                    $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                                    $path .= $this->getKey(array($row, $col));
                                    $r += $ri;
                                }
                                if ($c == $cc) {
                                    $row = $this->mod($fromPos[0] + $rowDir * $r, 4);
                                    $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                                    $path .= $this->getKey(array($row, $col));
                                    $r += $ri;
                                }
                                $row = $this->mod($fromPos[0] + $rowDir * $r, 4);
                                $col = $this->mod($fromPos[1] + $colDir * $c * $rc, 26);
                                $path .= $this->getKey(array($row, $col));
                            }
                            $paths[] = $path;
                        }
                    }
                }
            }
        }
        return $paths;
    }

    /**
     * Get the position of specified key
     * 
     * @param string $key
     * @return array
     */
    private function getPos($key) {
        for ($row = 0; $row < count($this->keyboard); $row++) {
            $col = strpos($this->keyboard[$row], $key);
            if ($col !== FALSE) {
                return array($row, $col);
            }
        }
        return NULL;
    }

    /**
     * Get the key at the specified position
     * 
     * @param array $pos
     * @return string
     */
    private function getKey($pos) {
        return substr($this->keyboard[$pos[0]], $pos[1], 1);
    }

    /**
     * Get the sign of input number
     * 
     * @param number $number
     * @return number
     */
    private function sign($number) {
        return $number == 0 ? 0 : (int) ( $number / abs($number));
    }

    /**
     * Mathematic module
     * 
     * @param number $number
     * @return number
     */
    private function mod($number, $module) {
        return ($number % $module) + ($number < 0 ? $module : 0);
    }

    function __construct() {
        $this->keyboard = array(
            "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
            "abcdefghijklmnopqrstuvwxyz",
            "0123456789!@#$%^&*()?/|\\+-",
            "`~[]{}<>        .,;:'\"_=\r\r"
        );
        $this->movmentRules = array(
            array(" PONMLKJI", " I"),
            array(" PONMLKJ", " IJ"),
            array(" PONMLK", " IJK"),
            array(" PONML", " IJKL"),
            array(" PONM", " IJKLM"),
            array(" PON", " IJKLMN"),
            array(" PO", " IJKLMNO"),
            array(" P", " IJKLMNOP"),
            array(" ONMLKJI", " I"),
            array(" ONMLKJ", " IJ"),
            array(" ONMLK", " IJK"),
            array(" ONML", " IJKL"),
            array(" ONM", " IJKLM"),
            array(" ON", " IJKLMN"),
            array(" O", " IJKLMNO"),
            array(" NMLKJI", " I"),
            array(" NMLKJ", " IJ"),
            array(" NMLK", " IJK"),
            array(" NML", " IJKL"),
            array(" NM", " IJKLM"),
            array(" N", " IJKLMN"),
            array(" MLKJI", " I"),
            array(" MLKJ", " IJ"),
            array(" MLK", " IJK"),
            array(" ML", " IJKL"),
            array(" M", " IJKLM"),
            array(" LKJI", " I"),
            array(" LKJ", " IJ"),
            array(" LK", " IJK"),
            array(" L", " IJKL"),
            array(" KJI", " I"),
            array(" KJ", " IJ"),
            array(" K", " IJK"),
            array(" JI", " I"),
            array(" J", " IJ"),
            array(" ^%$#", " #"),
            array(" ^%$", " #$"),
            array(" ^%", " #$%"),
            array(" ^", " #$%^"),
            array(" %$#", " #"),
            array(" %$", " #$"),
            array(" %", " #$%"),
            array(" $#", " #"),
            array(" $", " #$"),
            array(" 89!@#", " #"),
            array(" 89!@", " #@"),
            array(" 89!", " #@!"),
            array(" 89", " #@!9"),
            array(" 8", " #@!98"),
            array(" 9!@#", " #"),
            array(" 9!@", " #@"),
            array(" 9!", " #@!"),
            array(" 9", " #@!9"),
            array(" !@#", " #"),
            array(" !@", " #@"),
            array(" !", " #@!"),
            array(" @#", " #"),
            array(" @", " #@"),
            array("\rY", "\rZY"),
            array("\r+", "\r-+"),
            array("        ", " "),
            array("       ", " "),
            array("      ", " "),
            array("     ", " "),
            array("    ", " "),
            array("   ", " "),
            array("  ", " "),
            array("\r\r", "\r"),
        );
    }

}

?>
