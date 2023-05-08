<?php
class Incomes {
  private $pdo;
  private $spendings;
  
  public function __construct($pdo) {
    $this->pdo = $pdo;
  }
  
  private function fetchIncomes() {
    $sql = "SELECT * FROM incomes";
    $statement = $this->pdo->prepare($sql);
    $statement->execute();
    $this->incomes = $statement->fetchAll(PDO::FETCH_ASSOC);
  }
  
  public function getTotalIncomesByMonth() {
    $this->fetchIncomes();
    $totalIncomesAmounts = array();
    for ($i = 1; $i <= 6; $i++) {
      $totalIncomesAmounts[$i] = 0;
    }
    foreach($this->incomes as $income) {
      $date = explode('-', $income["accrual_date"]);
      $month = abs($date[1]);
      $totalIncomesAmounts[$month] += $income["amount"];
    }
    return $totalIncomesAmounts;
  }
  
  public function getIncomeDifferenceByMonth() {
    $totalIncomesAmounts = $this->getTotalIncomesByMonth();
    $incomeDifferences = array();
    for ($i = 1; $i < 6 ; $i++) {
      $incomeDifferences[$i] = abs($totalIncomesAmounts[$i + 1] -  $totalIncomesAmounts[$i]);
    }
    return $incomeDifferences;
  }
}


$dbUserName = "root";
$dbPassword = "password";
$pdo = new PDO("mysql:host=mysql; dbname=tq_quest; charset=utf8", $dbUserName, $dbPassword);
$incomes = new Incomes($pdo);
$incomeTotals = $incomes->getTotalIncomesByMonth();
$incomeDifferences = $incomes->getIncomeDifferenceByMonth();


foreach($incomeDifferences as $month => $difference) {
  echo $month . "月と" . ($month + 1) . "月の差分: " . $difference . "円<br />";
}