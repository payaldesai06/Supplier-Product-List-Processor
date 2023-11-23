<?php
error_reporting(E_ALL ^ E_NOTICE);
//check memory limit
if (memory_get_peak_usage(1) > "2e+10") {
  throw new Exception("Minimum file size limit is 20GB!");
}
$columns = ["make", "model", "condition", "grade", "capacity", "colour", "network"];
$requiredColumns = ["make", "model"];
$result = [];
$files = getopt("", ["file:", "unique-combinations:"]);
if (!isset($files["file"])) {
  echo "Please provide a input file.";
} else if (!isset($files["unique-combinations"])) {
  echo "Please provide a file name to save unique combinations.";
} else {
  $rows = array_map('str_getcsv', file($files["file"]));
  foreach ($rows as $i => $data) {
    if ($i == 0) {
      continue;
    }
    $rowData = [];
    $key = "";
    for ($c = 0; $c < count($columns); $c++) {
      $column = $columns[$c];
      if (in_array($column, $requiredColumns) && $data[$c] == null) {
        throw new Exception(ucfirst($column) . " field is required!");
      }
      $rowData[$column] = $data[$c];
      $key .= $data[$c];
    }
    //set project objects file
    if (!empty($rowData)) {
      if (isset($result[$key])) {
        $result[$key]['count']++;
      } else {
        $rowData['count'] = 1;
        $result[$key] = $rowData;
      }
    }
  }
  //set unique project objects file with group by
  if (!empty($result)) {
    foreach ($result as $row) {
      file_put_contents($files["unique-combinations"], json_encode($row) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
  }
}
