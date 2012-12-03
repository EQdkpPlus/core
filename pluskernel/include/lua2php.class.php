<?
/*
 * - WowLuaParser v0.1 (12/05 2005) ( Probably the first and the last version :p )
 * - By Hurlevent <Mystik> (EU - cho'gall)
 * - Contact : website <mystik-guilde.net> or mail <wonesek@yahoo.fr>
 * - Use it at yout own risk :p
 */

 class cls_wowLuaParser {

    var    $content,
        $content_,
        $debug;

    function __construct() {
        $this->debug    = false;
        $this->content_    = array();
    }

    /* Recupere les données d'une chaine lua simple */
    function load($str) {
        $str        = utf8_decode($str);
        $this->content    = array();
        $this->content_    = split("rn", $str);

        $this->loadContentByDepth($this->content, 0, count($this->content_) - 1);

        if ($this->debug) {
            print "<pre>nResultat :nn";
            print_r($this->content);
            print "</pre>";
        }
    }

    function debug($str) {
        if ($this->debug) echo "<pre>" . $str . "</pre>";
    }

    function loadContentByDepth(&$arr, $start, $end, $z = 0) {

        /* on parcour le fichier */
        $this->debug(str_repeat("   ", $z) . "Parcour de la ligne " . $start . " à la ligne " . $end . ".");

        for($i = $start; $i <= $end; $i++) {

            $this->debug(str_repeat("   ", $z) . "Ligne " . $i . " : '" . $this->content_[$i] . "'");

            /*
             * Tableau ?
             */
            if ($z == 0) {
                preg_match("#^[    ]{" . $z . "}(["0-9a-z_]+) = {#siU", $this->content_[$i], $aRes);
            } else {
                preg_match("#^[    ]{" . $z . "}[(["0-9a-z_]+)] = {#siU", $this->content_[$i], $aRes);

            }

            /* la ligne parsée est le debut d'un tableau */
            if (count($aRes) > 0) {
                /* Ligne début de block */
                $block_start = $i + 1;

                /* On recupere la clé */
                $key = $this->cleanKey($aRes[1]);

                $this->debug(str_repeat("   ", $z) . "Tableau '" . $key . "' trouvé à la ligne " . $i . ". Recherche fin de tableau...");

                /* On parcour la suite du fichier à la recherche de la fin du tableau */
                for($j = $i + 1; $j <= $end; $j++) {

                    if (ereg("^[    ]{" . $z . "}}", $this->content_[$j])) {

                        $this->debug(str_repeat("   ", $z) . "Fin de tableau ligne " . $j . ".");

                        /* si la fin du tableau vient toute de suite apres le debut du block, c'est un tableau vide */
                        if ($j == $block_start) {
                            $arr[$key] = array();
                        /* sinon le tableau n'est pas vide */
                        } else {
                            $this->loadContentByDepth($arr[$key], $block_start, $j-1, $z+1);
                        }

                        $i = $j;
                        break;
                    }
                }
            }

            /*
             * Variable simple
             */
            preg_match("#^[    ]{" . $z . "}[(["0-9a-z_]+)] = ([^r]+),$#siU", $this->content_[$i], $aRes);

            if (count($aRes) > 0) {
                $key = $this->cleanKey($aRes[1]);
                $val = $this->cleanVal($aRes[2]);
                $arr[$key] = $val;
                $this->debug(str_repeat("   ", $z) . "Variable simple '" . $key . "' trouvée.");
            }
         }
     }

     function cleanKey($key) {
         $key = trim($key);
         if (ereg(""(.*)"", $key)) {
             return substr($key, 1, -1);
         } else {
             return $key;
         }
     }

     function cleanVal($val) {
         $val = trim($val);
         if (ereg(""(.*)"", $val)) {
             return substr($val, 1, -1);
         } else {
             return $val;
         }
     }
 }
 ?>