<?php
$a1 = [-1, -2, -3, -4, -5, -6, -7, -8, -9, -10];
$a2 = [-1, 1, -2, 2, 3, -3, -4, 5];
$a3 = [-0.01, -0.0001, -.15];
$a4 = ["-1", "2", "-3", "4", "-5", "5", "-6", "6", "-7", "7"];

function bePositive($arr) {
    // DF39, 2/7/2023
    echo "<br>Processing Array:<br><pre>" . var_export($arr, true) . "</pre>";
    echo "<br>Positive output:<br>";
    $positiveArray = array();
    foreach($arr as $num)
        {
            if (is_numeric($num))
            {   
                $positiveNumber = abs($num);
                if (is_string($num))
                {
                    $positiveNumber = (string)$positiveNumber;
                }
                $positiveArray[] = $positiveNumber;
            }
        }
    var_dump($positiveArray);
    echo "<br>";
    //note: use the $arr variable, don't directly touch $a1-$a4
    //TODO use echo to output all of the values as positive (even if they were originally positive) and maintain the original datatype
    //hint: may want to use var_dump() or similar to show final data types
}
echo "Problem 3: Be Positive<br>";
?>
<table>
    <thread>
        <th>A1</th>
        <th>A2</th>
        <th>A3</th>
        <th>A4</th>
    </thread>
    <tbody>
        <tr>
            <td>
                <?php bePositive($a1); ?>
            </td>
            <td>
                <?php bePositive($a2); ?>
            </td>
            <td>
                <?php bePositive($a3); ?>
            </td>
            <td>
                <?php bePositive($a4); ?>
            </td>
        </tr>
</table>
<style>
    table {
        border-spacing: 2em 3em;
        border-collapse: separate;
    }

    td {
        border-right: solid 1px black;
        border-left: solid 1px black;
    }
</style>
