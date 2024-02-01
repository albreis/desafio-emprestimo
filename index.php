<?php require 'vendor/autoload.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_REQUEST;

$name = (string) ($input['name'] ?? null);
if (empty($name)) {
    exit('Customer name is required');
}

$age = (int) ($input['age'] ?? null);
if (empty($age)) {
    exit('Age is required');
}

$income = (float) ($input['income'] ?? null);
if (empty($income)) {
    exit('Income is required');
}

$location = (string) ($input['location'] ?? null);
$ufs = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MS', 'MT', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
if (empty($location)) {
    exit('Location is required');
}
if (!in_array($location, $ufs)) {
    exit('Location is invalid');
}

$output = [
    'customer' => $name,
    'loans' => []
];

if ($income <= 3000) {
    $output['loans']['PERSONAL'] = ['type' => 'PERSONAL', 'interest_rate' => 4];
    $output['loans']['GUARANTEED'] = ['type' => 'GUARANTEED', 'interest_rate' => 3];
}

if ($income >= 5000) {
    $output['loans']['CONSIGNMENT'] = ['type' => 'CONSIGNMENT', 'interest_rate' => 2];
}

if (($income >= 3000 && $income <= 5000) && $age < 30 && $location == 'SP') {
    $output['loans']['PERSONAL'] = ['type' => 'PERSONAL', 'interest_rate' => 4];
    $output['loans']['GUARANTEED'] = ['type' => 'GUARANTEED', 'interest_rate' => 3];
}

$output['loans'] = array_values($output['loans']);

header('Content-Type: application/json');

echo json_encode($output, JSON_PRETTY_PRINT);
