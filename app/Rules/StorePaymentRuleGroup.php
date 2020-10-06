<?php

namespace App\Rules;

class StorePaymentRuleGroup extends RuleGroup{

    protected static array $rules = [
        'method' => 'required|in:BOLETO,CREDIT_CARD',
        'sender' => 'required',
        'sender.name' => 'required|string|min_words:2',
        'sender.document' => 'required',
        'sender.document.type' => 'required|in:CPF,CNPJ',
        'sender.document.value' => 'required|document:sender.document.type',
        'sender.phone' => 'required',
        'sender.phone.areaCode' => 'required|area_code',
        'sender.phone.number' => 'required|digits_between:8,9',
        'sender.email' => 'required|email',
        'sender.hash' => 'required',
        'shipping' => 'required',
        'shipping.addressRequired' => 'required|boolean',
        'shipping.street' => 'required_if:shipping.addressRequired,1|max:80',
        'shipping.number' => 'required_if:shipping.addressRequired,1|max:20',
        'shipping.district' => 'required_if:shipping.addressRequired,1|max:60',
        'shipping.city' => 'required_if:shipping.addressRequired,1|max:60|min:2',
        'shipping.state' => 'required_if:shipping.addressRequired,1|size:2',
        'shipping.country' => 'required_if:shipping.addressRequired,1|in:BRA',
        'shipping.postalCode' => 'required_if:shipping.addressRequired,1|digits:8',
        'shipping.complement' => 'max:40',
        'shipping.cost' => 'numeric',
        'shipping.type' => 'in:1,2,3',
        'reference' => 'max:200',
        'extraAmount' => 'required|numeric',
        'items' => 'required|array',
        'items.*.id' => 'required|max:36',
        'items.*.description' => 'required|max:100',
        'items.*.quantity' => 'required|integer|min:1|max:100',
        'items.*.amount' => 'required|numeric|max:10000',
        'creditCard' => 'required_if:method,CREDIT_CARD',
        'creditCard.token' => 'required_if:method,CREDIT_CARD',
        'creditCard.holder' => 'required_if:method,CREDIT_CARD',
        'creditCard.holder.name' => 'required_if:method,CREDIT_CARD|max:50',
        'creditCard.holder.documents' => 'required_if:method,CREDIT_CARD',
        'creditCard.holder.documents.type' => 'required_if:method,CREDIT_CARD|in:CPF,CNPJ',
        'creditCard.holder.documents.value' => 'required_if:method,CREDIT_CARD|document:creditCard.holder.documents.type',
        'creditCard.holder.documents.birthDate' => 'required_if:method,CREDIT_CARD|date_format:d/m/Y',
        'creditCard.holder.phone' => 'required_if:method,CREDIT_CARD',
        'creditCard.holder.phone.areaCode' => 'required_if:method,CREDIT_CARD|area_code',
        'creditCard.holder.phone.number' => 'required_if:method,CREDIT_CARD|digits_between:7,9',
        'creditCard.holder.phone.number' => 'required_if:method,CREDIT_CARD|digits_between:7,9',
        'installment' => 'required_if:method,CREDIT_CARD',
        'installment.quantity' => 'required_if:method,CREDIT_CARD|numeric|min:1|max:18'
    ];
}