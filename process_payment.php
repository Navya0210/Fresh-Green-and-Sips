<?php
require_once 'config/db_config.php';

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input data
if (!$data || !isset($data['paymentMethodId']) || !isset($data['customerData']) || !isset($data['cartItems'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Insert customer data
    $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone, address) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $data['customerData']['name'],
        $data['customerData']['email'],
        $data['customerData']['phone'],
        $data['customerData']['address']
    ]);
    $customerId = $pdo->lastInsertId();

    // Calculate total amount
    $totalAmount = $data['amount'];

    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$customerId, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($data['cartItems'] as $item) {
        $stmt->execute([
            $orderId,
            $item['name'],
            1, // Default quantity
            $item['price']
        ]);
    }

    // Process payment (integrate with Stripe or other payment gateway)
    try {
        // Here you would typically make a call to your payment gateway's API
        // For demonstration, we'll simulate a successful payment
        $paymentSuccess = true;
        $transactionId = 'TRANS_' . time() . rand(1000, 9999);

        if ($paymentSuccess) {
            // Record payment
            $stmt = $pdo->prepare("INSERT INTO payments (order_id, amount, payment_method, transaction_id, status) VALUES (?, ?, 'card', ?, 'completed')");
            $stmt->execute([$orderId, $totalAmount, $transactionId]);

            // Update order status
            $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
            $stmt->execute([$orderId]);

            // Commit transaction
            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Payment processed successfully',
                'orderId' => $orderId,
                'transactionId' => $transactionId
            ]);
        } else {
            throw new Exception('Payment processing failed');
        }
    } catch (Exception $e) {
        // Payment failed
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Payment processing failed: ' . $e->getMessage()
        ]);
    }
} catch (PDOException $e) {
    // Database error
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Other errors
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}