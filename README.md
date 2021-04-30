
# Payment API
A payment API that integrates with Pagseguro's Checkout Transparente API. It offers all Pagseguro's validations, and options of payment.

This project was created for learning purposes and to serve as an example of the use of third party APIs.

The accepted payment methods are Boleto, Credit Card, and Online Debit.

## Requeriments

- [Docker](https://docs.docker.com/)
- [Pagseguro credentials](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) 

## Installation

    $ docker-compose up
    
## Test

Run all tests

    $ composer test

Run tests filtered by test name or test file

    $ composer test -- --filter { filename/test name }
    
## Environment Variables

| Name | Value |
|--|--|
| PAGSEGURO_EMAIL| [Email to authenticate to Pagseguro](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) |
| PAGSEGURO_TOKEN | [Token to authenticate to Pagseguro](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) |
| PAGSEGURO_URL | [Pagseguro URL](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#transparente-ambientes-disponiveis)|
| PAGSEGURO_NOTIFICATION_URL | URL for [notifications from Pagseguro](https://m.pagseguro.uol.com.br/v2/guia-de-integracao/api-de-notificacoes.html?_rnt=dd#!rmcl) |

## License

[Apache License 2.0](https://github.com/iammateus/payment-api/blob/master/LICENSE)
