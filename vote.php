<?php
$id =  (int) $_GET[id];
$vote = (int) $_GET[vote];

if (file_exists("$id.dat")) {

    $ip= $_SERVER['REMOTE_ADDR'];
    $ip_file = file_get_contents("ip$id.dat");
    $ip_abbr = explode(",", $ip_file);
    $data = file("$id.dat");

    if ($vote) {

        foreach($ip_abbr as $value)
            if ($ip == $value) {echo "<p><b><font color=red> Ви вже голосували! </font></b></p>";
                exit;
            }

        echo "<p><b><font color=green> Дякуємо! </font></b><br /><i>*Показані результати до вашого голосування:</i><p>";
    }

    echo "<table border=0 style='text-align:left' ><th colspan=3 style='text-align:center'><b>$data[0]</b></th>";

    for ($i=1;$i<count($data);$i++) {
        $votes = explode("~", $data[$i]);
        $graf = 100*$votes[0]/(count($ip_abbr)-1);
        echo "<tr><td>$votes[1]</td><td style='text-align: center'><b>$votes[0]</b></td><td> <span style='font-size: small'>".round($graf, 3)."%</span><div style='background: red; height:5px; width:".round($graf, 0)."px'></div></td></tr>";
    }
    echo "<tr><td>Всього<b></b></td><td>".(count($ip_abbr)-1)."</td><td>&nbsp;</td></tr></table>";

    if ($vote) {
        $f = fopen("$id.dat","w");
        flock($f,LOCK_EX);
        fputs($f, "$data[0]");
        for ($i=1;$i<count($data);$i++) {
            $votes = explode("~", $data[$i]);
            if ($i==$vote) $votes[0]++;
            fputs($f,"$votes[0]~$votes[1]");
            fflush($f);
            flock($f,LOCK_UN);
        }
        fclose($f);

        $ip_adr = fopen("ip$id.dat","a++");
        flock($ip_adr,LOCK_EX);
        fputs($ip_adr, "$ip".",");
        fflush($ip_adr);
        flock($ip_adr,LOCK_UN);
        fclose($ip_adr);
    }

} else {
    echo "Такого голосування не існує.";
    exit;
}
?>