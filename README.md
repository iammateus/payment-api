# Payment API
A RESTful payment API that integrates with Pagseguro's API Checkout Transparente. It offers all of Pagseguro's options of payment and validations.

This project was created for learning purposes and to serve as an example of the use of third party APIs.

Accepted payment methods: Boleto, Credit Card, and Online Debit.

## Requeriments

1. This project runs on [Docker](https://docs.docker.com/).
2. [Pagseguro](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) credentials.

## Installation

    $ docker-compose up
    
## Test

Run all tests

    $ composer test

Run tests filtered by test name or test file

    $ composer test -- --filter { filename/test name }
    
## Env

A list of required environment variables that you need to set in the .env file:

| Name | Value |
|--|--|
| PAGSEGURO_EMAIL| [Credentials Email](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) || PAGSEGURO_TOKEN | [Credentials Token](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#autenticacao) |
| PAGSEGURO_URL | [Pagseguro URL](https://dev.pagseguro.uol.com.br/reference/checkout-transparente#transparente-ambientes-disponiveis)|
| PAGSEGURO_NOTIFICATION_URL | URL for [notifications from Pagseguro](https://m.pagseguro.uol.com.br/v2/guia-de-integracao/api-de-notificacoes.html?_rnt=dd#!rmcl) |

## License

[Apache License 2.0](https://github.com/iammateus/payment-api/blob/master/LICENSE)
