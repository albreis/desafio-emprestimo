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

header('Content-Type: application/json');

$yourApiKey = getenv('API_KEY');
$client = \Tectalic\OpenAi\Manager::build(
    new \GuzzleHttp\Client(),
    new \Tectalic\OpenAi\Authentication($yourApiKey)
);

$params = [
    'model' => 'gpt-3.5-turbo', // Modelo de reescrita de texto da OpenAI
    'messages' => [
        ['role' => 'system', 'content' => '
        baseado nas regras:

        Conceder o empréstimo pessoal se o salário do cliente for igual ou inferior a R$ 3000.
        Conceder o empréstimo pessoal se o salário do cliente estiver entre R$ 3000 e R$ 5000, se o cliente tiver menos de 30 anos e residir em São Paulo (SP).
        Conceder o empréstimo consignado se o salário do cliente for igual ou superior a R$ 5000.
        Conceder o empréstimo com garantia se o salário do cliente for igual ou inferior a R$ 3000.
        Conceder o empréstimo com garantia se o salário do cliente estiver entre R$ 3000 e R$ 5000, se o cliente tiver menos de 30 anos e residir em São Paulo (SP).
        
        vou te enviar idade, salario e localidade e de acordo com as regras acima você vai me responder somente o json assim:

        {
          "customer": "Vuxaywua Zukiagou",
          "loans": [
            {
              "type": "PERSONAL",
              "interest_rate": 4
            },
            {
              "type": "GUARANTEED",
              "interest_rate": 3
            },
            {
              "type": "CONSIGNMENT",
              "interest_rate": 2
            }
          ]
        }
        
        '],
        ['role' => 'user', 'content' => "Nome: {$name}, Idade: {$age}, Salário: {$income}, Localidade: {$location}"],
    ]
];

$response = $client->chatCompletions()->create(new \Tectalic\OpenAi\Models\ChatCompletions\CreateRequest($params))->toModel();

$_output = trim($response->choices[0]->message->content, '"');

header('Content-Type: application/json');

echo $_output;
