<?php
if ($modx->event->name !== 'OnOrderPaid' || !isset($token) || !$fully_paid) return;
$processor = $modx->commerce->loadProcessor();
$cart = $processor->getCart();
$_payment = new \Commerce\Payments\Payment($modx);
$method = new ReflectionMethod($_payment, 'prepareItems');
$method->setAccessible(true);
$items = $method->invoke($_payment, $cart);
$docItems = [];
foreach ($items as $item) {
    $docItems[] = [
        'Qty'         => $item['count'],
        'Price'       => $item['price'] * 100,
        'Description' => $item['name'],
        'PaymentItem' => $item['product'] ? 1 : 4,
        'PaymentType' => 4,
        'Tax'         => $tax
    ];
}
$info = [
    'requestId' => $order['id'] . '-' . $order['hash'],
    'method'    => 'income',
    'params'    => [
        'Cashier' => [
            'Name' => $name,
            'Inn'  => $inn
        ],
        'Persona' => [
            'Name'  => $order['name'],
            'Email' => $order['email'],
            'Phone' => $order['phone']
        ],
        'SendCheck'      => 'Email',
        'DatePayment'    => date('d.m.Y', strtotime($payment['created_at'])),
        'DocItems'       => $docItems,
        'SumTypePayment' => 2
    ]
];
$info = json_encode($info, JSON_UNESCAPED_UNICODE);

$ch = curl_init($mode === 'api' ? 'https://api.arendakass.ru/api' : 'https://demo.arendakass.ru/api');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . trim($token),
    'Accept: application/json'
]);
$result = curl_exec($ch);
if ($result === false) {
    $modx->logEvent(1, 3, 'Curl error: ' . curl_error($ch), 'ArendaKass');
} else {
    $result = json_decode($result, true);
    if (!isset($result['transaction_id'])) {
        $modx->logEvent(1, 2, 'Transaction error: <br><pre>' . print_r($result, true) . '</pre>', 'ArendaKass');
    }
}
curl_close($ch);
