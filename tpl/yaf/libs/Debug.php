<?php
/**
* @brief vj中使用\dw或\dp
*/
class Debug
{
    static public function watch($file,$line,$var,$varName="UnKonw")
    {
        echo "<br>*****************************DEBUG::WATCH******************************<br>\n";
        echo "<pre>";
        echo "$varName valure: <br>\n";
        var_dump($var);
        echo "</pre>";
        echo "from: [$file $line] <br>\n";
        echo "***************************************************************************<br>\n";
    }

    static public function prints()
    {
        $html = "\n";
        $array = debug_backtrace();
        foreach($array as $row)
        {
            $html .= '调用方法: '.$row['class'].'->'.$row['function']."\t\t\t\t".$row['file'].' '.$row['line']." \n";
        }
        echo ($html);
    }
}
