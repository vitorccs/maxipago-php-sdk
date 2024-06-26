# Maxipago - SDK PHP
SDK em PHP para API Maxipago

## Requisitos
* PHP >= 8.1

## Descrição
SDK em PHP para a [API Maxipago](https://www.maxipago.com/developers/apidocs/).

**Importante:** no momento, estão disponíveis as formas de pagamentos PIX e Boleto. Futuramente, será implementado Cartão de Crédito.

## Instalação
Via Composer
```bash
composer require vitorccs/maxipago-php-sdk
```

## Parâmetros
| Parâmetro             | Obrigatório | Padrão | Comentário                                             |
|-----------------------|-------------|--------|--------------------------------------------------------|
| MAXIPAGO_MERCHANT_ID  | Sim         | null   | Merchant ID para autenticação                          |
| MAXIPAGO_MERCHANT_KEY | Sim         | null   | Merchant Key para autenticação                         |
| MAXIPAGO_SANDBOX      | Não         | false  | Habilita o modo Sandbox                                |
| MAXIPAGO_TIMEOUT      | Não         | 30     | Timeout em segundos para estabelecer conexão com a API |
 
Podem ser definidos por variáveis de ambiente:

```bash
# Em um arquivo .env do seu projeto:
MAXIPAGO_MERCHANT_ID=myMerchantId
MAXIPAGO_MERCHANT_KEY=myMerchantKey
MAXIPAGO_SANDBOX=true
MAXIPAGO_TIMEOUT=60
```
Ou passados como argumentos do serviço:

```php
use Vitorccs\Maxipago\Entities\Parameters;
use Vitorccs\Maxipago\Http\SaleService;

$parameters = new Parameters(
    'myMerchantId',
    'myMerchantKey', 
    true, // modo sandbox
    60 // timeout
);

$saleService = new SaleService($parameters);
```

## Serviços implementados

```php
# inicia os serviços de transação e consulta
use Vitorccs\Maxipago\Http\CustomerService;
use Vitorccs\Maxipago\Http\QueryService;
use Vitorccs\Maxipago\Http\SaleService;

$customerService = new CustomerService();
$saleService = new SaleService();
$queryService = new QueryService();
```
### Criar Cliente
```php
// Nota: utilize CustomerBuilder (descrito mais abaixo na documentação)
// para gerar facilmente o objeto $customer
$response = $customerService->create($customer);
```

### Realizar Pedido

```php
// Nota: utilize PixSaleBuilder (descrito mais abaixo na documentação)
// para gerar facilmente o objeto $order
$response = $saleService->createPixSale($order);
```

### Consultar Pedido
Pode-se obter a transação por qualquer um destes critérios
```php
// retorna todas as transações do pedido (array)
$response = $queryService->getByOrderId('0A0104AB:018FAAE5F66A:FE79:5318D453');

// retorna apenas a transação mais atual (object)
$response = $queryService->getLastByOrderId('0A0104AB:018FAAE5F66A:FE79:5318D453');

// retorna todas as transações com este reference number (array)
$response = $queryService->getByReferenceNumber(221342);

// retorna a transação com este ID (objeto) 
$response = $queryService->getByTransactionId(18390209);
```

### Cancelar Pedido

```php
// Nota: somente disponível para pedidos em aberto e para algumas 
// formas de pagamento (ex: Boleto e PIX)
$response = $saleService->cancelSale($transactionId);
```

### Estornar Pedido

```php
// Nota: todos os campos são obrigatórios.
$chargeTotal = 100.00;
$response = $saleService->refundSale($orderId, $referenceNumber, $chargeTotal);
```

## Construtores (Builders)
Para auxiliar a criar uma Transação, foram disponibilizados alguns construtores:

### Criar Cliente
```php
use Vitorccs\Maxipago\Enums\CustomerGender;

$customer = CustomerBuilder::create('373.067.250-92',  'Joao', 'Silva')
        ->setPhone('11 91234-5678')
        ->setEmail('email@email.com')
        ->setGender(CustomerGender::M)
        ->setBirthDate(\Datetime::createFromFormat('Y-m-d', '1991-02-10'))
        ->get();
````

### Criar Pedido PIX
```php
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Builders\PixSaleBuilder;

// Demonstrando os campos mais essenciais
$pixExpiration = 82400; // em segundos
$pixSale = PixSaleBuilder::create(Processor::TEST, 30.00, 'COD1001', $pixExpiration)
        ->setPixPaymentInfo('Mensagem de agradecimento') // opcional
        ->createBilling(
            name: 'João Silva',
            cpf: '373.067.250-92'
        )
        ->get();
```

### Criar Pedido Boleto
```php
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Builders\BoletoSaleBuilder;
use Vitorccs\Maxipago\Enums\CustomerType;
use Vitorccs\Maxipago\Enums\BoletoChargeType;

// Demonstrando os campos mais essenciais
$expirationDate = '2024-10-01';
$pixSale = BoletoSaleBuilder::create(Processor::PIXITAU, 50.00, 'COD1002', $expirationDate)
        ->setCustomerIdExt('227.732.755-78')
        ->createBilling(
            name: 'João Silva Souza',
            cpf: '227.732.755-78',
            email: 'joao.silva@email.com', // opcional
            birthdate: '1980-10-25', // opcional
            customerType: CustomerType::Individual // opcional
        )
        ->setBillingAddressFields(
            'Rua Teste, 123',
            '4o andar',
            'Bairro Teste',
            'São Paulo',
            'SP',
            '03456-000'
        )
        ->setDiscount('2024-10-01', 5.00) // opcional
        ->setCharge('2024-12-03', BoletoChargeType::AMOUNT, 2.50) // opcional
        ->setInterestRate('2024-12-03', 1.10) // opcional
        ->get();
```

## Tratamento de erros
Esta biblioteca lança as seguintes exceções:

* `MaxipagoValidationException` para erros diversos detectados pela API Maxipago, inclusive erros que impediram a Transação de ser criada (`errorCode` diferente de 0). 
* `MaxipagoProcessorException` quando a Transação foi criada normalmente pela Maxipago, mas há um erro de "processor" (`responseCode` diferente de 0). Obs: apenas disponível nos serviços de criar transações.
* `MaxipagoNotFoundException` ao tentar localizar uma Transação que não existe (ex: localizar por OrderId ou TransactionId).
* `MaxipagoRequestException` para as demais falhas não tratadas pela API, incluindo erros de servidor (HTTP 4xx ou 5xx) e de conexão (ex: timeout).

Exemplo de corpo da resposta onde será lançado uma exceção `MaxipagoValidationException`
```xml
<root>
    <header>
        <errorCode>1</errorCode>
        <errorMsg><![CDATA[Descrição do erro]]></errorMsg>
    </header>
</root>
```

Exemplo de corpo da resposta onde será lançado uma exceção `MaxipagoProcessorException`
```xml
<root>
  <orderID>0B0214CB:018A9C5B6ACA:DBCC:0344E731</orderID>
  <referenceNum>15d71318-71f3-4312-96f8-741f082dd6e7</referenceNum>
  <transactionID>623999551</transactionID>
  <transactionTimestamp>1716315187</transactionTimestamp>
  <responseCode>1</responseCode>
  <responseMessage/>
  <processorCode>400</processorCode>
  <processorMessage>Descrição do erro</processorMessage>
  <processorName>PIXITAU</processorName>
  <errorMessage>Descrição do erro</errorMessage>
</root>
```

## Exemplo de implementação

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/vendor/autoload.php';

putenv('MAXIPAGO_MERCHANT_ID=myMerchantId');
putenv('MAXIPAGO_MERCHANT_KEY=myMerchantKey');
putenv('MAXIPAGO_SANDBOX=true');

use Vitorccs\Maxipago\Builders\BillingDataBuilder;
use Vitorccs\Maxipago\Builders\PixSaleBuilder;
use Vitorccs\Maxipago\Enums\Processor;
use Vitorccs\Maxipago\Exceptions\MaxipagoProcessorException;
use Vitorccs\Maxipago\Exceptions\MaxipagoRequestException;
use Vitorccs\Maxipago\Exceptions\MaxipagoValidationException;
use Vitorccs\Maxipago\Http\QueryService;
use Vitorccs\Maxipago\Http\SaleService;

try {
    $queryService = new QueryService();
    $saleService = new SaleService();

    // CRIANDO TRANSAÇÃO
    $pixSale = PixSaleBuilder::create(Processor::TEST, 200.00, 'COD_10001', 82400)
        ->setIpAddress('200.201.202.203')
        ->createBilling(
            name: 'João Silva',
            cpf: '409.289.289-11',
            rg: '4.533.890-0',
            companyName: 'Company Name'
        )
        ->get();
    $checkSuccess = true;  // Habilitar MaxipagoProcessorException
    $response = $saleService->createPixSale($pixSale, $checkSuccess);
    print_r($response);
    
    // CONSULTANDO UMA TRANSAÇÃO
    $checkSuccess = true;  // Habilitar MaxipagoNotFoundException
    $response = $queryService->getLastByOrderId($response->orderID, $checkSuccess);
    print_r($response);
    
     // CANCELANDO UMA TRANSAÇÃO (EM ABERTO)
    $response = $saleService->cancelSale($response->transactionId);
    print_r($response);

} catch (MaxipagoValidationException $e) { // erros de Validação da API
    echo sprintf('ValidationException: %s (ErrorCode: %s, ResponseCode: %s)', $e->getMessage(), $e->getErrorCode(), $e->getResponseCode());

} catch (MaxipagoProcessorException $e) { // erros de Processor da API
    echo sprintf('ProcessorException: %s (ProcessorCode: %s)', $e->getMessage(), $e->getProcessorCode());

} catch (MaxipagoRequestException $e) { // erros não tratados (servidor e conexão)
    echo sprintf('RequestException: %s (HTTP Status: %s)', $e->getMessage(), $e->getHttpCode());

} catch (\Exception $e) { // demais erros (runtime, etc)
    echo $e->getMessage();
}
```

## Webhooks
Para processar os [webhooks da Maxipago](https://www.maxipago.com/developers/apidocs/notificacoes-webhook/), pode-se usar a biblioteca de conversão de XML para PHP.
```php
use Vitorccs\Maxipago\Converters\SymfonyXmlConverter;

// recebe os dados em post
$xmlString = file_get_contents('php://input');

// prepara serviço de conversão de XML 
$converter = new SymfonyXmlConverter();

// converte o XML recebido em array PHP
$phpArray = $converter->decodeArray($xmlString);
echo $phpArray['transaction-event']['orderID'];

// converte o XML recebido em array PHP
$phpObject = $converter->decodeObject($xmlString);
echo $phpArray->{'transaction-event'}->orderID;
```

## Testes
Caso queira contribuir, por favor, implementar testes de unidade em PHPUnit.

Para executar:

```bash
composer test
```

 
