<?php
function pythonDay(){
$tre = shell_exec('python script_test_python.py '.$_POST['dateday']);
}
?>
